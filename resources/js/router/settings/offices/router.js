export default [
    {
        path: "/offices",
        name: "offices.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-offices-index" */ "./Index.vue"
            )
    },
    {
        path: "/offices/create",
        name: "offices.create",
        component: () =>
            import(/* webpackChunkName: "settings-offices-form" */ "./Form.vue")
    },
    {
        path: "/offices/:id",
        name: "offices.show",
        component: () =>
            import(
                /* webpackChunkName: "settings-offices-show" */ "./Show.vue"
            ),
        props: true,
        children: [
            {
                path: "",
                redirect: "users",
                props: true
            },
            {
                path: "users",
                name: "offices.users",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-users" */ "./Users.vue"
                    ),
                props: true
            },
            {
                path: "managers",
                name: "offices.managers",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-managers" */ "./Managers.vue"
                    ),
                props: true
            },
            {
                path: "orders",
                name: "offices.orders",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-orders" */ "./Orders.vue"
                    ),
                props: true
            },
            {
                path: "deposits",
                name: "offices.deposits",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-deposits" */ "./Deposits.vue"
                    ),
                props: true
            },
            {
                path: "statuses",
                name: "offices.statuses",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-statuses" */ "./Statuses.vue"
                    ),
                props: true
            },
            {
                path: "status-configs",
                name: "offices.status-configs",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-status-configs" */ "./StatusConfigs.vue"
                    ),
                props: true
            },
            {
                path: "office-payments",
                name: "offices.office-payments",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-office-payments" */ "./OfficePayments.vue"
                    ),
                props: true
            },
            {
                path: "distribution-rules",
                name: "offices.distribution-rules",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-distribution-rules" */ "./DIstributionRules.vue"
                    ),
                props: true
            },
            {
                path: "morning-branches",
                name: "offices.morning-branches",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-offices-morning-branches" */ "./MorningBranches.vue"
                    ),
                props: true
            }
        ]
    },
    {
        path: "/offices/:id/edit",
        name: "offices.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-offices-form" */ "./Form.vue"
            ),
        props: true
    }
];
