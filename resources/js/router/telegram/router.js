export default [
    {
        path: "/telegram",
        redirect: "/telegram/channels"
    },
    {
        path: "/telegram/channels",
        name: "telegram.channels.index",
        component: () =>
            import(
                /* webpackChunkName : "telegram-channels-index" */ "./IndexChannels.vue"
            )
    },
    {
        path: "/telegram/channels/:id",
        name: "telegram.channels.show",
        component: () =>
            import(
                /* webpackChunkName : "telegram-channels-show" */ "./ShowChannel.vue"
            ),
        props: true
    },
    {
        path: "/telegram/stats",
        name: "telegram.stats.index",
        component: () =>
            import(
                /* webpackChunkName : "telegram-stats-index" */ "./IndexStats.vue"
            )
    },
    {
        path: "/telegram/stats/bulk",
        name: "telegram.stats.bulk",
        component: () =>
            import(
                /* webpackChunkName : "telegram-stats-bulk" */ "./CreateStats.vue"
            )
    }
];
