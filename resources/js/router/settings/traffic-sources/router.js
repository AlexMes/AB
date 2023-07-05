export default [
    {
        path: "/traffic-sources",
        name: "traffic-sources.index",
        component: () => import(/* webpackChunkName: "settings-traffic-sources-index" */ "./Index.vue"),
    },
    {
        path: "/traffic-sources/create",
        name: "traffic-sources.create",
        component: () => import(/* webpackChunkName: "settings-traffic-sources-form" */ "./Form.vue"),
    },
    {
        path: "/traffic-sources/:id",
        name: "traffic-sources.show",
        component: () => import(/* webpackChunkName: "settings-traffic-sources-show" */ "./Show.vue"),
        props: true,
        children: [
            {
                path: "",
                redirect: "domains",
                props: true
            },
            {
                path: "domains",
                name: "traffic-sources.domains",
                component: () => import(/* webpackChunkName: "settings-traffic-sources-domains" */ "./Domains.vue"),
                props: true
            }

        ]
    },
    {
        path: "/traffic-sources/:id/edit",
        name: "traffic-sources.update",
        component: () => import(/* webpackChunkName: "settings-traffic-sources-form" */ "./Form.vue"),
        props: true
    }
];
