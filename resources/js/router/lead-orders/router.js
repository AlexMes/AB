export default [
    {
        path: "/leads-orders",
        name: "leads-orders.index",
        component: () => import("./Index.vue")
    },
    {
        path: "/leads-orders/old-leftovers",
        name: "leads-orders.old-leftovers",
        component: () => import("./LeftOversStats.vue")
    },
    {
        path: "/leads-orders/create",
        name: "leads-orders.create",
        component: () => import("./Form.vue")
    },
    {
        path: "/leads-orders/:id",
        name: "leads-orders.show",
        component: () => import("./Show.vue"),
        props: true
    },
    {
        path: "/leads-orders/:id/edit",
        name: "leads-orders.update",
        component: () => import("./Form.vue"),
        props: true
    }
];
