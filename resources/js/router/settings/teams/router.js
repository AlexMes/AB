export default [
    {
        path: "/teams",
        name: "teams.index",
        component: () => import(/* webpackChunkName: "settings-teams-index" */ "./Index.vue"),
    },
    {
        path: "/teams/create",
        name: "teams.create",
        component: () => import(/* webpackChunkName: "settings-teams-form" */ "./Form.vue"),
    },
    {
        path: "/teams/:id",
        name: "teams.show",
        component: () => import(/* webpackChunkName: "settings-teams-show" */ "./Show.vue"),
        props: true,
        children: [
            {
                path: "",
                redirect: "users",
                props: true
            },
            {
                path: "users",
                name: "teams.users",
                component: () => import(/* webpackChunkName: "settings-teams-users" */ "./Users.vue"),
                props: true
            },
        ]
    },
    {
        path: "/teams/:id/edit",
        name: "teams.update",
        component: () => import(/* webpackChunkName: "settings-teams-form" */ "./Form.vue"),
        props: true
    }
];
