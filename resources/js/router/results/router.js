export default [
    {
        path: '/results/results',
        name: 'results.index',
        component: () => import( /* webpackChunkName: "domains-orders-index" */ './Index.vue'),
    },
    {
        path:'/results/results/create',
        name:'results.create',
        component: () => import( /* webpackChunkName: "domains-orders-form" */ './Form.vue'),
    },
    {
        path:'/results/results/:id',
        name:'results.show',
        component: () => import( /* webpackChunkName: "domains-orders-show" */ './Show.vue'),
        props:true,
    },
    {
        path:'/results/results/:id/edit',
        name:'results.update',
        component:() => import( /* webpackChunkName: "domains-orders-form" */ './Form.vue'),
        props:true,
    },
];
