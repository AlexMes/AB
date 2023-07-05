import Show from './Show.vue';

export default [
    {
        path: '/facebook/profiles',
        name:'profiles.index',
        component: () => import(/* webpackChunkName: "fb-profiles-index" */ './Index.vue'),
    },
    {
        path:'/facebook/profiles/:id',
        component: Show,
        props: true,
        children:[
            {
                path:'',
                redirect: 'general',
            },
            {
                path: 'general',
                component: () =>import(/* webpackChunkName: "fb-profiles-general" */ './GeneralInfo.vue'),
                name: 'profile.general',
                props: true,
            },
            {
                path: 'accounts',
                component: () =>import(/* webpackChunkName: "fb-profiles-accounts" */ './Accounts.vue'),
                name: 'profile.accounts',
                props: true,
            },
            {
                path: 'campaigns',
                component: () =>import(/* webpackChunkName: "fb-profiles-campaigns" */ './Campaigns.vue'),
                name: 'profile.campaigns',
                props: true,
            },
            {
                path: 'adsets',
                component: () =>import(/* webpackChunkName: "fb-profiles-adsets" */ './AdSets.vue'),
                name: 'profile.adsets',
                props: true,
            },
            {
                path: 'ads',
                component: () =>import(/* webpackChunkName: "fb-profiles-ads" */ './Ads.vue'),
                name: 'profile.ads',
                props: true,
            },
            {
                path: 'pages',
                component: () =>import(/* webpackChunkName: "fb-profiles-ads" */ './Pages.vue'),
                name: 'profile.pages',
                props: true,
            },
        ],
    },
];
