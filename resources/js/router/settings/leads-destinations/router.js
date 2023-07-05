export default [
    {
        path: "/leads-destinations",
        name: "leads-destinations.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-leads-destinations-index" */ "./Index.vue"
            )
    },
    {
        path: "/leads-destinations/create",
        name: "leads-destinations.create",
        component: () =>
            import(
                /* webpackChunkName: "settings-leads-destinations-form" */ "./Form.vue"
            )
    },
    {
        path: "/leads-destinations/:id",
        name: "leads-destinations.show",
        component: () =>
            import(
                /* webpackChunkName: "settings-leads-destinations-show" */ "./Show.vue"
            ),
        props: true
    },
    {
        path: "/leads-destinations/:id/edit",
        name: "leads-destinations.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-leads-destinations-form" */ "./Form.vue"
            ),
        props: true
    }
];
