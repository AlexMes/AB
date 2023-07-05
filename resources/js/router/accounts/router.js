export default [
    {
        path: '/facebook/accounts',
        name: 'accounts.index',
        component: () => import( /* webpackChunkName: "facebook-accounts-index" */ './Index.vue')
    },
    {
        path: '/facebook/accounts/:id',
        name: 'accounts.show',
        component: () => import(/* webpackChunkName: "facebook-accounts-show" */ './Show.vue')
    },
];
