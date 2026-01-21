import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// Fonction route() simple pour Inertia
if (!window.route) {
  window.route = function(name, params = {}, absolute = false) {
    const routes = {
      'dashboard': '/',
      'projects.index': '/projects',
      'tasks.index': '/tasks',
      'calendar': '/calendar'
    };
    
    let url = routes[name] || '/';
    
    // Remplace les paramètres
    if (params) {
      Object.keys(params).forEach(key => {
        url = url.replace(`{${key}}`, params[key]);
        url = url.replace(`:${key}`, params[key]);
      });
    }
    
    if (absolute) {
      return (window.Ziggy?.url || window.location.origin) + url;
    }
    
    return url;
  };
}

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});