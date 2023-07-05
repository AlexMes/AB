import Index from "./Index.vue";
export default [
    {
        path: "/logs",
        component: Index,
        children: [
            {
                path: "",
                redirect: "/logs/payment-fails"
            },
            {
                path: "/logs/payment-fails",
                component: () =>
                    import(
                        /* webpackChunkName: "logs-payment-fails" */ "./PaymentFails.vue"
                    ),
                name: "logs.payment-fails",
                props: true
            },
            {
                path: "/logs/ads-disapprovals",
                component: () =>
                    import(
                        /* webpackChunkName: "logs-ads-disapprovals" */ "./AdsDisapprovals.vue"
                        ),
                name: "logs.ads-disapprovals",
                props: true
            },
            {
                path: "/logs/stopped-adsets",
                component: () =>
                    import(
                        /* webpackChunkName: "logs-stopped-adsets" */ "./StoppedAdsets.vue"
                        ),
                name: "logs.stopped-adsets",
                props: true
            },
            {
                path: "/logs/sms-messages",
                component: () =>
                    import(
                        /* webpackChunkName: "logs-sms-messages" */ "./SmsMessages"
                        ),
                name: "logs.sms-messages",
                props: true
            },
            {
                path: "/logs/assignment-delivery-fails",
                component: () =>
                    import(
                        /* webpackChunkName: "logs-assignment-delivery-fails" */ "./AssignmentDeliveryFails.vue"
                        ),
                name: "logs.assignment-delivery-fails",
                props: true
            },
        ]
    }

];
