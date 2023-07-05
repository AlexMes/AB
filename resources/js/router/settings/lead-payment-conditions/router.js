export default [
    {
        path: "/lead-payment-conditions",
        name: "lead-payment-conditions.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-lead-payment-conditions-index" */ "./Index.vue"
            )
    },
    {
        path: "/lead-payment-conditions/create",
        name: "lead-payment-conditions.create",
        component: () =>
            import(
                /* webpackChunkName: "settings-lead-payment-conditions-form" */ "./Form.vue"
            )
    },
    {
        path: "/lead-payment-conditions/:id",
        name: "lead-payment-conditions.show",
        component: () =>
            import(
                /* webpackChunkName: "settings-lead-payment-conditions-show" */ "./Show.vue"
            ),
        props: true
    },
    {
        path: "/lead-payment-conditions/:id/edit",
        name: "lead-payment-conditions.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-lead-payment-conditions-form" */ "./Form.vue"
            ),
        props: true
    }
];
