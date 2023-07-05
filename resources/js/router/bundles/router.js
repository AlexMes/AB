export default [
    {
        path: '/bundles',
        name: 'bundles.index',
        component: () => import( /* webpackChunkName: "bundles-index" */ './Index.vue'),
    },
    {
        path:'/bundles/create',
        name:'bundles.create',
        component: () => import( /* webpackChunkName: "bundles-form" */ './Form.vue'),
    },
    {
        path:'/bundles/:id',
        component: () => import( /* webpackChunkName: "bundles-show" */ './Show.vue'),
        props:true,
        children:[
            {
                path:'',
                redirect: 'general',
            },
            {
                path: 'general',
                name: 'bundles.general',
                component: () =>import(/* webpackChunkName: "bundles-general" */ './GeneralInfo.vue'),
                props: true,
            },
            {
                path: 'comments',
                name: 'bundles.comments',
                component: () =>import(/* webpackChunkName: "bundles-general" */ './Comments.vue'),
                props: true,
            },
            {
                path: 'files',
                name: 'bundles.files',
                component: () =>import(/* webpackChunkName: "bundles-general" */ './Files.vue'),
                props: true,
            },
            {
                path:'edit',
                name:'bundles.update',
                component:() => import( /* webpackChunkName: "bundles-form" */ './Form.vue'),
                props:true,
            },
        ]
    },
];
