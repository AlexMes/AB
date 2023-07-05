
export default [
    {
        path: '/facebook/campaigns',
        name: 'campaigns.index',
        component: () => import( /* webpackChunkName: "facebook-campaigns-index" */ './Index.vue'),
    },
    {
        path:'/facebook/campaigns/create',
        name:'campaigns.create',
        component:()=> import( /* webpackChunkName: "facebook-campaigns-index" */ './Form.vue'),
    },
    {
        path:'/facebook/campaigns/:id',
        name:'campaigns.show',
        component:()=>import( /* webpackChunkName: "facebook-campaigns-index" */ './Show.vue'),
        props:true,
    },
    {
        path:'/facebook/campaigns/:id/edit',
        name:'campaigns.update',
        component:()=>import( /* webpackChunkName: "facebook-campaigns-index" */ './Form.vue'),
        props:true,
    },
];
