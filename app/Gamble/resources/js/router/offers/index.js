import Index from './Index.vue';
import Show from './Show.vue';
import Form from './Form.vue';

export default [
    {
        path: '/offers',
        name: 'offers.index',
        component: Index,
    },
    {
        path: '/offers/create',
        name: 'offers.create',
        component: Form,
    },
    {
        path: '/offers/:id/edit',
        name: 'offers.update',
        component: Form,
        props: true,
    },
    {
        path: '/offers/:id',
        name: 'offers.show',
        component: Show,
        props: true,
    },
];
