import Index from './Index.vue';
import Show from './Show.vue';
import Form from './Form.vue';

export default [
    {
        path: '/tech-costs',
        name: 'tech-costs.index',
        component: Index,
    },
    {
        path: '/tech-costs/create',
        name: 'tech-costs.create',
        component: Form,
    },
    {
        path: '/tech-costs/:id/edit',
        name: 'tech-costs.update',
        component: Form,
        props: true,
    },
    {
        path: '/tech-costs/:id',
        name: 'tech-costs.show',
        component: Show,
        props: true,
    },
];
