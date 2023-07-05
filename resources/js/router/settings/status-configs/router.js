export default [
    {
        path: "/status-configs/create",
        name: "status-configs.create",
        component: () => import(/* webpackChunkName: "settings-status-configs-form" */ "./Form.vue"),
    },
    {
        path: "/status-configs/:id/edit",
        name: "status-configs.update",
        component: () => import(/* webpackChunkName: "settings-status-configs-form" */ "./Form.vue"),
        props: true,
    },
];
