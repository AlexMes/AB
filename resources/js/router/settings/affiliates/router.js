export default [
    {
        path: '/affiliates',
        name: 'affiliates.index',
        component: () => import(/* webpackChunkName: "settings-affiliates-index" */ './Index.vue'),
    },
    {
        path:'/affiliates/create',
        name:'affiliates.create',
        component: () => import(/* webpackChunkName: "settings-affiliates-form" */ './Form.vue'),
    },
    {
        path:'/affiliates/:id',
        name:'affiliates.show',
        component:() => import(/* webpackChunkName: "settings-affiliates-show" */ './Show.vue'),
        props:true,
    },
    {
        path:'/affiliates/:id/edit',
        name:'affiliates.update',
        component:() => import(/* webpackChunkName: "settings-affiliates-form" */ './Form.vue'),
        props:true,
    },
];
