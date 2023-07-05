export default [
    {
        path: "/tenants",
        name: "tenants.index",
        component: () => import(/* webpackChunkName: "settings-tenants-index" */ "./Index.vue"),
    },
    {
        path: "/tenants/create",
        name: "tenants.create",
        component: () => import(/* webpackChunkName: "settings-tenants-form" */ "./Form.vue"),
    },
    {
        path: "/tenants/:id",
        name: "tenants.show",
        component: () => import(/* webpackChunkName: "settings-tenants-show" */ "./Show.vue"),
        props: true,
    },
    {
        path: "/tenants/:id/edit",
        name: "tenants.update",
        component: () => import(/* webpackChunkName: "settings-tenants-form" */ "./Form.vue"),
        props: true
    }
];
