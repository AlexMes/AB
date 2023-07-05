export default [
    {
        path: "/users",
        name: "users.index",
        component: () => import(/* webpackChunkName: "settings-users-index" */ "./Index.vue"),
    },
    {
        path: "/users/create",
        name: "users.create",
        component: () => import(/* webpackChunkName: "settings-users-form" */ "./Form.vue"),
    },
    {
        path: "/users/:id",
        name: "users.show",
        component: () => import(/* webpackChunkName: "settings-users-show" */ "./Show.vue"),
        props: true,
        children: [
            {
                path: "",
                redirect: "profiles",
                props: true
            },
            {
                path: "profiles",
                name: "users.profiles",
                component: () => import(/* webpackChunkName: "settings-users-profiles" */ "./Profiles.vue"),
                props: true
            },
            {
                path: "accounts",
                name: "users.accounts",
                component: () => import(/* webpackChunkName: "settings-users-accounts" */ "./Accounts.vue"),
                props: true
            },
            {
                path: "deposits",
                name: "users.deposits",
                component: () => import(/* webpackChunkName: "settings-users-deposits" */ "./Deposits.vue"),
                props: true
            },
            {
                path: "accesses",
                name: "users.accesses",
                component: () => import(/* webpackChunkName: "settings-users-accesses" */ "./Accesses.vue"),
                props: true
            },
            {
                path: "leads",
                name: "users.leads",
                component: () => import(/* webpackChunkName: "settings-users-leads" */ "./Leads.vue"),
                props: true
            },
            {
                path: "pages",
                name: "users.pages",
                component: () => import(/* webpackChunkName: "settings-users-pages" */ "./Pages.vue"),
                props: true
            },
            {
                path: "firewall",
                name: "users.firewall",
                component: () => import(/* webpackChunkName: "settings-users-firewall" */ "./Firewall.vue"),
                props: true
            },
            {
                path: "settings",
                name: "users.settings",
                component: () => import(/* webpackChunkName: "settings-users-settings" */ "./Settings.vue"),
                props: true
            },
            {
                path: "allowed-offers",
                name: "users.allowed-offers",
                component: () => import(/* webpackChunkName: "settings-users-allowed-offers" */ "./AllowedOffers.vue"),
                props: true
            },
            {
                path: "allowed-branches",
                name: "users.allowed-branches",
                component: () => import(/* webpackChunkName: "settings-users-allowed-branches" */ "./AllowedBranches.vue"),
                props: true
            },
            {
                path: "shared-users",
                name: "users.shared-users",
                component: () => import(/* webpackChunkName: "settings-users-shared-users" */ "./SharedUsers.vue"),
                props: true
            },
        ]
    },
    {
        path: "/users/:id/edit",
        name: "users.update",
        component: () => import(/* webpackChunkName: "settings-users-form" */ "./Form.vue"),
        props: true
    }
];
