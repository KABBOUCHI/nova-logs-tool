Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-logs-tool',
            path: '/nova-logs-tool',
            component: require('./components/LogsTool.vue'),
        },
    ])
})