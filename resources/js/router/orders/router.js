import Index from './Index.vue';
import Form from './Form.vue';
import Show from './Show.vue';


export default [
    {
        path: '/domains/orders',
        name: 'orders.index',
        component: () => import( /* webpackChunkName: "domains-orders-index" */ './Index.vue'),
    },
    {
        path:'/domains/orders/create',
        name:'orders.create',
        component: () => import( /* webpackChunkName: "domains-orders-form" */ './Form.vue'),
    },
    {
        path:'/domains/orders/:id',
        name:'orders.show',
        component: () => import( /* webpackChunkName: "domains-orders-show" */ './Show.vue'),
        props:true,
    },
    {
        path:'/domains/orders/:id/edit',
        name:'orders.update',
        component:() => import( /* webpackChunkName: "domains-orders-form" */ './Form.vue'),
        props:true,
    },
];
