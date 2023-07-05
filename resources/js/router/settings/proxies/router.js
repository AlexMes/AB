export default [
    {
        path: "/proxies",
        name: "proxies.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-proxies-index" */ "./Index.vue"
            )
    },
    {
        path: "/proxies/create",
        name: "proxies.create",
        component: () =>
            import(
                /* webpackChunkName: "settings-proxies-form" */ "./Form.vue"
            )
    },
    {
        path: "/proxies/:id",
        name: "proxies.show",
        component: () =>
            import(
                /* webpackChunkName: "settings-proxies-show" */ "./Show.vue"
            ),
        props: true
    },
    {
        path: "/proxies/:id/edit",
        name: "proxies.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-proxies-form" */ "./Form.vue"
            ),
        props: true
    }
];
