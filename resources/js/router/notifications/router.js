export default [
    {
        path: '/notifications',
        name: 'notifications.index',
        component: () => import( /* webpackChunkName: "notifications-index" */ './Index.vue'),
    }
];
