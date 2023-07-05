export default [
    {
        path: "/forms",
        name: "forms.index",
        component: () => import(/* webpackChunkName: "settings-forms-index" */ "./Index.vue"),
    },
    {
        path: "/forms/create",
        name: "forms.create",
        component: () => import(/* webpackChunkName: "settings-forms-form" */ "./Form.vue"),
    },
    {
        path: "/forms/:id",
        name: "forms.show",
        component: () => import(/* webpackChunkName: "settings-forms-show" */ "./Show.vue"),
        props: true
    },
    {
        path: "/forms/:id/edit",
        name: "forms.update",
        component: () => import(/* webpackChunkName: "settings-forms-form" */ "./Form.vue"),
        props: true
    },
];
