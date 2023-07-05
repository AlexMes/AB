export default [
    {
        path: "/tags",
        name: "tags.index",
        component: () => import(/* webpackChunkName: "settings-tags-index" */ "./Index.vue"),
    },
    {
        path: "/tags/create",
        name: "tags.create",
        component: () => import(/* webpackChunkName: "settings-tags-form" */ "./Form.vue"),
    },
    {
        path: "/tags/:id",
        name: "tags.show",
        component: () => import(/* webpackChunkName: "settings-tags-show" */ "./Show.vue"),
        props: true
    },
    {
        path: "/tags/:id/edit",
        name: "tags.update",
        component: () => import(/* webpackChunkName: "settings-tags-form" */ "./Form.vue"),
        props: true
    },
];
