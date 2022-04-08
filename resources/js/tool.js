Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'LogsTool',
            path: '/logs',
            component: require('./components/LogsTool.vue')
        }
    ]);
});
