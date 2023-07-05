export default [
    {
        path: "/pages",
        name: "pages.index",
        component: () => import(/* webpackChunkName: "settings-pages-index" */ "./Index.vue"),
    },
    {
        path: "/pages/create",
        name: "pages.create",
        component: () => import(/* webpackChunkName: "settings-pages-form" */ "./Form.vue"),
    },
    {
        path: "/pages/:id",
        name: "pages.show",
        component: () => import(/* webpackChunkName: "settings-pages-show" */ "./Show.vue"),
        props: true
    },
    {
        path: "/pages/:id/edit",
        name: "pages.update",
        component: () => import(/* webpackChunkName: "settings-pages-form" */ "./Form.vue"),
        props: true
    },
];
