export default [
    {
        path: '/servers',
        name: 'servers.index',
        component: ()=>import( /* webpackChunkName: "servers-index" */ './Index.vue'),
    },
    {
        path:'/servers/:id',
        name:'servers.show',
        component: () => import( /* webpackChunkName: "servers-show" */ './Show.vue'),
        props:true,
    },
];
