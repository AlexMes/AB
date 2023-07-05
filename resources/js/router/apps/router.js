export default [
    {
        path: '/apps',
        name: 'apps.index',
        component: () => import(/* webpackChunkName: "apps-index" */ './Index.vue'),
    },
    {
        path:'/apps/create',
        name:'apps.create',
        component: () => import(/* webpackChunkName: "apps-create" */ './Form.vue'),
    },
    {
        path:'/apps/:id/edit',
        name:'apps.edit',
        component: () => import(/* webpackChunkName: "apps-edit" */ './Form.vue'),
        props: true,
    },
];
