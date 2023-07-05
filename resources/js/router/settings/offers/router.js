export default [
    {
        path: "/offers",
        name: "offers.index",
        component: () => import(/* webpackChunkName: "settings-offers-index" */ "./Index.vue"),
    },
    {
        path: "/offers/create",
        name: "offers.create",
        component: () => import(/* webpackChunkName: "settings-offers-form" */ "./Form.vue"),
    },
    {
        path: "/offers/:id",
        component: () => import(/* webpackChunkName: "settings-offers-show" */ "./Show.vue"),
        props: true,
        children: [
            {
                path: "",
                redirect: "allowed-users",
                props: true
            },
            {
                path: "allowed-users",
                name: "offers.allowed-users",
                component: () => import(/* webpackChunkName: "settings-offers-allowed-users" */ "./AllowedUsers.vue"),
                props: true
            },
        ]
    },
    {
        path: "/offers/:id/edit",
        name: "offers.update",
        component: () => import(/* webpackChunkName: "settings-offers-form" */ "./Form.vue"),
        props: true
    },
];
