export default [
    {
        path: '/facebook/apps',
        name: 'facebook.apps.index',
        component: () => import(/* webpackChunkName: "facebook-apps-index" */ './Index.vue'),
    },
    {
        path:'/facebook/apps/create',
        name:'facebook.apps.create',
        component: () => import(/* webpackChunkName: "facebook-apps-create" */ './Form.vue'),
    },
    {
        path:'/facebook/apps/:id/edit',
        name:'facebook.apps.edit',
        component: () => import(/* webpackChunkName: "facebook-apps-edit" */ './Form.vue'),
        props: true,
    },
];
