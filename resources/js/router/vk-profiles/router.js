import Show from './Show.vue';

export default [
    {
        path: '/vk/profiles',
        name:'vk-profiles.index',
        component: () => import(/* webpackChunkName: "vk-profiles-index" */ './Index.vue'),
    },
    {
        path:'/vk/profiles/:id',
        component: Show,
        props: true,
        children:[
            {
                path:'',
                redirect: 'general',
            },
            {
                path: 'general',
                component: () =>import(/* webpackChunkName: "vk-profiles-general" */ './GeneralInfo.vue'),
                name: 'vk-profiles.general',
                props: true,
            },
        ],
    },
];
