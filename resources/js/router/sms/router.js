export default [
    {
        path: '/sms',
        redirect: '/sms/campaigns',
    },
    {
        path: '/sms/campaigns',
        component: () => import(/* webpackChunkName: "sms-campaigns-index" */ './CampaignIndex.vue'),
        name: 'sms.campaigns.index',
        props: true,
    },
    {
        path:'/sms/campaigns/create',
        name:'sms.campaigns.create',
        component: () => import(/* webpackChunkName: "sms-campaigns-form" */ './CampaignForm.vue'),
    },
    {
        path:'/sms/campaigns/:id',
        name: 'sms.campaigns.show',
        component: () => import(/* webpackChunkName: "sms-campaigns-show" */ './CampaignShow.vue'),
        props: true,
    },
    {
        path:'/sms/campaigns/:id/edit',
        name:'sms.campaigns.update',
        component:() => import(/* webpackChunkName: "sms-campaigns-form" */ './CampaignForm.vue'),
        props:true,
    },
];
