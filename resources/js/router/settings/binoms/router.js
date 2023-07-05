export default [
    {
        path: "/binoms",
        name: "binoms.index",
        component: () => import(/* webpackChunkName: "settings-binoms-index" */ "./Index.vue"),
    },
    {
        path: "/binoms/create",
        name: "binoms.create",
        component: () => import(/* webpackChunkName: "settings-binoms-form" */ "./Form.vue"),
    },
    {
        path: "/binoms/:id",
        name: "binoms.show",
        component: () => import(/* webpackChunkName: "settings-binoms-show" */ "./Show.vue"),
        props: true
    },
    {
        path: "/binoms/:id/edit",
        name: "binoms.update",
        component: () => import(/* webpackChunkName: "settings-binoms-form" */ "./Form.vue"),
        props: true
    },
];
