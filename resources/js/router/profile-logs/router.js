export default [
    {
        path: '/facebook/profile-logs',
        name: 'profile-logs.index',
        component: ()=>import( /* webpackChunkName: "facebook-profile-logs-index" */ './Index.vue'),
    },
    {
        path:'/facebook/profile-logs/create',
        name:'profile-logs.create',
        component:()=> import( /* webpackChunkName: "facebook-profile-logs-form" */ './Form.vue'),
    },
    {
        path:'/facebook/profile-logs/:id',
        name:'profile-logs.show',
        component: () => import( /* webpackChunkName: "facebook-profile-logs-show" */ './Show.vue'),
        props:true,
    },
    {
        path:'/facebook/profile-logs/:id/edit',
        name:'profile-logs.update',
        component: () => import( /* webpackChunkName: "facebook-profile-logs-form" */ './Form.vue'),
        props:true,
    },
];
