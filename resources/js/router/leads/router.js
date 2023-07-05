export default [
    {
        path: '/results/leads',
        name: 'leads.index',
        component: () => import(/* webpackChunkName: "leads-show"*/ './Index.vue'),
        props: true,
    },
    {
        path: '/results/leads/:id',
        name: 'leads.show',
        component: () => import(/* webpackChunkName: "leads-show"*/ './Show.vue'),
        props: true,
    },
];
