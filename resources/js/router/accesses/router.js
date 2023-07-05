export default [
    {
        path: '/facebook/accesses',
        name: 'accesses.index',
        component: () => import(/* webpackChunkName:"facebook-accesses-index"  */ './Index.vue'),
    },
    {
        path: '/facebook/accesses/create',
        name: 'accesses.create',
        component: () => import(/* webpackChunkName:"facebook-accesses-create"  */ './Form.vue'),
    },
    {
        path: '/facebook/accesses/:id/edit',
        name: 'accesses.update',
        component: () => import(/* webpackChunkName:"facebook-accesses-update"  */ './Form.vue'),
        props: true,
    },
]
