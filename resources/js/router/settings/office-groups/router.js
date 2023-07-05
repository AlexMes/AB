export default [
    {
        path: "/office-groups",
        name: "office-groups.index",
        component: () =>
            import(
                /* webpackChunkName: "settings-office-groups-index" */ "./Index.vue"
            )
    },
    {
        path: "/office-groups/create",
        name: "office-groups.create",
        component: () =>
            import(
                /* webpackChunkName: "settings-office-groups-form" */ "./Form.vue"
            )
    },
    {
        path: "/office-groups/:id",
        component: () =>
            import(
                /* webpackChunkName: "settings-office-groups-show" */ "./Show.vue"
            ),
        props: true,
        children: [
            {
                path: "",
                redirect: "offices",
                props: true
            },
            {
                path: "offices",
                name: "office-groups.offices",
                component: () =>
                    import(
                        /* webpackChunkName: "settings-office-groups-offices" */ "./Offices.vue"
                        ),
                props: true
            },
        ],
    },
    {
        path: "/office-groups/:id/edit",
        name: "office-groups.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-office-groups-form" */ "./Form.vue"
            ),
        props: true
    }
];
