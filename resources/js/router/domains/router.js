export default [
    {
        path: '/domains',
        name: 'domains.index',
        component: ()=>import( /* webpackChunkName: "domains-index" */ './Index.vue'),
    },
    {
        path:'/domains/create',
        name:'domains.create',
        component:()=> import( /* webpackChunkName: "domains-form" */ './DomainsForm.vue'),
    },
    {
        path: "/domains/:id",
        name: "domains.show",
        component: () =>
            import(/* webpackChunkName: "domains-show" */ "./DomainShow.vue"),
        props: true,
        children: [
            {
                path: "",
                redirect: "ads",
                props: true
            },
            {
                path: "ads",
                name: "domains.ads",
                component: () => import(/* webpackChunkName: "domains-ads-show" */ "./DomainAds.vue"),
                props: true
            },
            {
                path: "comments",
                name: "domains.comments",
                component: () => import(/* webpackChunkName: "domains-comments-show" */ "./DomainComments.vue"),
                props: true
            },
            {
                path: "sms-campaigns",
                name: "domains.sms-campaigns",
                component: () => import(/* webpackChunkName: "domains-sms-campaigns-show" */ "./DomainSMSCampaigns.vue"),
                props: true
            },
        ]
    },
    {
        path:'/domains/:id/edit',
        name:'domains.update',
        component: () => import( /* webpackChunkName: "domains-form" */ './DomainsForm.vue'),
        props:true,
    },
];
