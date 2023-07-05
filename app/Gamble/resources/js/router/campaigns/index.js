import Index from './Index.vue';
import Show from './Show.vue';
import Form from './Form.vue';

export default [
    {
        path: '/campaigns',
        name: 'campaigns.index',
        component: Index,
    },
    {
        path: '/campaigns/create',
        name: 'campaigns.create',
        component: Form,
    },
    {
        path: '/campaigns/:id/edit',
        name: 'campaigns.update',
        component: Form,
        props: true,
    },
    {
        path: '/campaigns/:id',
        name: 'campaigns.show',
        component: Show,
        props: true,
    },
];
