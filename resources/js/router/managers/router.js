export default [
    {
        path: "/managers/:id/edit",
        name: "managers.update",
        component: () =>
            import(
                /* webpackChunkName: "settings-managers-form" */ "./ManagersForm.vue"
            ),
        props: true
    }
];
