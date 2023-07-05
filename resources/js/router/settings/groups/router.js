export default [
    {
        path: "/groups",
        name: "groups.index",
        component: () => import(/* webpackChunkName: "settings-groups-index" */ "./Index.vue"),
    },
    {
        path: "/groups/create",
        name: "groups.create",
        component: () => import(/* webpackChunkName: "settings-groups-form" */ "./Form.vue"),
    },
    {
        path: "/groups/:id/edit",
        name: "groups.update",
        component: () => import(/* webpackChunkName: "settings-groups-form" */ "./Form.vue"),
        props: true
    },
];
