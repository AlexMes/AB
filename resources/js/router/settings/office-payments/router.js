export default [
    {
        path: "/office-payments/create",
        name: "office-payments.create",
        component: () => import(/* webpackChunkName: "settings-office-payments-form" */ "./Form.vue"),
    },
    {
        path: "/office-payments/:id/edit",
        name: "office-payments.update",
        component: () => import(/* webpackChunkName: "settings-office-payments-form" */ "./Form.vue"),
        props: true,
    },
];
