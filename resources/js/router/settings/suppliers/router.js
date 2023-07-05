export default [
    {
        path: '/suppliers',
        name: 'suppliers.index',
        component: () => import(/* webpackChunkName: "settings-suppliers-index" */ './Index.vue'),
    },
    {
        path:'/suppliers/create',
        name:'suppliers.create',
        component: () => import(/* webpackChunkName: "settings-suppliers-form" */ './Form.vue'),
    },
    {
        path:'/suppliers/:id',
        name:'suppliers.show',
        component:() => import(/* webpackChunkName: "settings-suppliers-show" */ './Show.vue'),
        props:true,
    },
    {
        path:'/suppliers/:id/edit',
        name:'suppliers.update',
        component:() => import(/* webpackChunkName: "settings-suppliers-form" */ './Form.vue'),
        props:true,
    },
];
