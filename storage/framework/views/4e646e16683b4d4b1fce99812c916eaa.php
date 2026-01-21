<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia><?php echo e(config('app.name', 'Planif IA')); ?></title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <!-- Routes minimales pour Inertia -->
    <script>
        window.Ziggy = {
            url: '<?php echo e(config('app.url', 'http://localhost:8000')); ?>',
            port: null,
            defaults: {},
            routes: {
                'dashboard': { 
                    uri: '/', 
                    methods: ['GET', 'HEAD'],
                    parameters: []
                },
                'projects.index': { 
                    uri: '/projects', 
                    methods: ['GET', 'HEAD'],
                    parameters: []
                },
                'tasks.index': { 
                    uri: '/tasks', 
                    methods: ['GET', 'HEAD'],
                    parameters: []
                },
                'calendar': { 
                    uri: '/calendar', 
                    methods: ['GET', 'HEAD'],
                    parameters: []
                }
            }
        };
        
        // Fonction route() simple
        window.route = function(name, params = {}, absolute = false) {
            const route = window.Ziggy.routes[name];
            if (!route) return '/';
            
            let url = route.uri;
            
            // Remplace les paramètres
            if (params) {
                Object.keys(params).forEach(key => {
                    url = url.replace(`{${key}}`, params[key]);
                    url = url.replace(`:${key}`, params[key]);
                });
            }
            
            if (absolute) {
                return window.Ziggy.url + url;
            }
            
            return url;
        };
    </script>
    
    <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>
</head>
<body class="font-sans antialiased bg-gray-50">
    <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } elseif (config('inertia.use_script_element_for_initial_page')) { ?><script data-page="app" type="application/json"><?php echo json_encode($page); ?></script><div id="app"></div><?php } else { ?><div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div><?php } ?>
</body>
</html><?php /**PATH C:\Users\HP ELITEBOOK\Desktop\dev\planif-ia\resources\views\app.blade.php ENDPATH**/ ?>