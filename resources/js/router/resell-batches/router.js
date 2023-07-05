export default [
    {
        path: "/batches",
        name: "resell-batches.index",
        component: () => import("./Index.vue")
    },
    {
        path: "/batches/create",
        name: "resell-batches.create",
        component: () => import("./Form.vue")
    },
    {
        path: "/batches/:id",
        name: "resell-batches.show",
        component: () => import("./Show.vue"),
        props: true
    },
    {
        path: "/batches/:id/edit",
        name: "resell-batches.update",
        component: () => import("./Form.vue"),
        props: true
    }
];
