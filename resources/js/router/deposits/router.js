export default [
    {
        path: '/results/deposits',
        name: 'deposits.index',
        component: ()=>import( /* webpackChunkName: "deposits-index" */ './Index.vue'),
    },
    {
        path:'/results/deposits/create',
        name:'deposits.create',
        component:()=> import( /* webpackChunkName: "deposits-form" */ './Form.vue'),
    },
    {
        path:'/results/deposits/import',
        name:'deposits.import',
        component: () => import( /* webpackChunkName: "deposits-import" */ './Import.vue'),
    },
    {
        path:'/results/deposits/:id',
        name:'deposits.show',
        component: () => import( /* webpackChunkName: "deposits-show" */ './Show.vue'),
        props:true,
    },
    {
        path:'/results/deposits/:id/edit',
        name:'deposits.update',
        component: () => import( /* webpackChunkName: "deposits-form" */ './Form.vue'),
        props:true,
    },
];
