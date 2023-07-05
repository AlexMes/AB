export default [
    {
        path: "/branches",
        name: "branches.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-branches-index" */ "./Index.vue"
            )
    },
    {
        path: "/branches/create",
        name: "branches.create",
        component: () =>
            import(
                /* webpackChunkName: "settings-branches-form" */ "./Form.vue"
            )
    },
    {
        path: "/branches/:id",
        component: () =>
            import(
                /* webpackChunkName: "settings-branches-show" */ "./Show.vue"
            ),
        props: true,
        children: [
            {
                path: "",
                redirect: "teams",
                props: true
            },
            {
                path: "teams",
                name: "branches.teams",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-branches-teams" */ "./Teams.vue"
                    ),
                props: true
            },
            {
                path: "users",
                name: "branches.users",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-branches-users" */ "./Users.vue"
                    ),
                props: true
            },
            {
                path: "offices",
                name: "branches.offices",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-branches-offices" */ "./Offices.vue"
                    ),
                props: true
            },
            {
                path: "allowed-users",
                name: "branches.allowed-users",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-branches-allowed-users" */ "./AllowedUsers.vue"
                    ),
                props: true
            },
            {
                path: "black-leads",
                name: "branches.black-leads",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-branches-black-leads" */ "./BlackLeads.vue"
                    ),
                props: true
            }
        ]
    },
    {
        path: "/branches/:id/edit",
        name: "branches.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-branches-form" */ "./Form.vue"
            ),
        props: true
    }
];
