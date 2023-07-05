import Index from './Index.vue';
import Show from './Show.vue';
import Form from './Form.vue';

export default [
    {
        path: '/groups',
        name: 'groups.index',
        component: Index,
    },
    {
        path: '/groups/create',
        name: 'groups.create',
        component: Form,
    },
    {
        path: '/groups/:id/edit',
        name: 'groups.update',
        component: Form,
        props: true,
    },
    {
        path: '/groups/:id',
        name: 'groups.show',
        component: Show,
        props: true,
    },
];
