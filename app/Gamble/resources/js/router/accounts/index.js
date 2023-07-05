import Index from './Index.vue';
import Show from './Show.vue';
import Form from './Form.vue';

export default [
    {
        path: '/accounts',
        name: 'accounts.index',
        component: Index,
    },
    {
        path: '/accounts/create',
        name: 'accounts.create',
        component: Form,
    },
    {
        path: '/accounts/:id/edit',
        name: 'accounts.update',
        component: Form,
        props: true,
    },
    {
        path: '/accounts/:id',
        name: 'accounts.show',
        component: Show,
        props: true,
    },
];
