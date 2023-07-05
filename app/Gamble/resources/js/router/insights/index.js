import Index from './Index.vue';
import Show from './Show.vue';
import Form from './Form.vue';

export default [
    {
        path: '/insights',
        name: 'insights.index',
        component: Index,
    },
    {
        path: '/insights/create',
        name: 'insights.create',
        component: Form,
    },
    {
        path: '/insights/:id/edit',
        name: 'insights.update',
        component: Form,
        props: true,
    },
    {
        path: '/insights/:id',
        name: 'insights.show',
        component: Show,
        props: true,
    },
];
