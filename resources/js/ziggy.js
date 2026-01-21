const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"dashboard":{"uri":"\/","methods":["GET","HEAD"]},"projects.index":{"uri":"projects","methods":["GET","HEAD"]},"tasks.index":{"uri":"tasks","methods":["GET","HEAD"]},"calendar":{"uri":"calendar","methods":["GET","HEAD"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
