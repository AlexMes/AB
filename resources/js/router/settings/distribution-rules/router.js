export default [
    {
        path: "/distribution-rules",
        name: "distribution-rules.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-distribution-rules-index" */ "./Index.vue"
            )
    },
    {
        path: "/distribution-rules/create",
        name: "distribution-rules.create",
        component: () =>
            import(
                /* webpackChunkName: "settings-distribution-rules-form" */ "./Form.vue"
            )
    },
    {
        path: "/distribution-rules/:id/edit",
        name: "distribution-rules.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-distribution-rules-form" */ "./Form.vue"
            ),
        props: true
    },
];
