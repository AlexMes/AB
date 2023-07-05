export default [
    {
        path: '/manual-accounts',
        name: 'manual-accounts.index',
        component: ()=> import( /* webpackChunkName: "manual-accounts-index" */ './Index.vue'),
    },
    {
        path:'/manual-accounts/create',
        name:'manual-accounts.create',
        component:()=> import( /* webpackChunkName: "manual-accounts-form" */ './Form.vue'),
    },
    {
        path: "/manual-accounts/:id",
        name: "manual-accounts.show",
        component: () => import(/* webpackChunkName: "manual-accounts-show" */ "./Show.vue"),
        props: true,
    },
    {
        path:'/manual-accounts/:id/edit',
        name:'manual-accounts.update',
        component: () => import( /* webpackChunkName: "manual-accounts-form" */ './Form.vue'),
        props:true,
    },
];
