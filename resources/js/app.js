/* eslint-disable */

require("./bootstrap");
import Vue from "vue";
import router from "./router/index";
import Multiselect from "vue-multiselect";
import VueJSModal from "vue-js-modal";
import "vue-multiselect/dist/vue-multiselect.min.css";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import Toast from "cxlt-vue2-toastr";
import "cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css";
import Clipboard from "v-clipboard";
import Mousetrap from "mousetrap";

Vue.component("fa-icon", FontAwesomeIcon);
Vue.component("mutiselect", Multiselect);

import flatpickr from "flatpickr";
import { Russian } from "flatpickr/dist/l10n/ru.js";
flatpickr.localize(Russian);

Vue.use(VueJSModal, { dialog: true });
Vue.use(Toast, {
    position: "bottom right",
    showDuration: 2500
});
Vue.use(Clipboard);

const files = require.context("./components", true, /\.vue$/i);
files.keys().map(key =>
    Vue.component(
        key
            .split("/")
            .pop()
            .split(".")[0],
        files(key).default
    )
);

Vue.config.productionTip = false;

Vue.prototype.$eventHub = new Vue();

router.beforeEach((to, from, next) => {
    // its supposed 'notifications' route is always allowed everywhere
    const users = [
        {
            email: undefined,
            branch_id: 19,
            denied: [],
            allowed: ["reports/dtd-cr", "batches", "reports/resell-doubles", "reports/rejected-unique"]
        },
        {
            email: undefined,
            branch_id: 20,
            denied: [],
            allowed: ["batches"]
        },
        {
            email: undefined,
            branch_id: 16,
            denied: [],
            allowed: ["batches"]
        },
    ];
    const roles = [
        {
            role: "buyer",
            home: "/dashboard",
            denied: [],
            allowed: [
                "dashboard",
                "facebook",
                "logs",
                "reports/performance",
                "reports/statistics",
                "reports/placements",
                "reports/accounts-banned",
                "reports/gender",
                "reports/regions",
                "reports/os",
                "domains",
                "domains/orders",
                "vk",
            ]
        },
        {
            role: "teamlead",
            home: "/dashboard",
            denied: [],
            allowed: [
                "dashboard",
                "facebook",
                "logs",
                "reports/performance",
                "reports/statistics",
                "reports/placements",
                "reports/gender",
                "reports/regions",
                "reports/os",
                "reports/lead-stats",
                "reports/revise",
                "reports/offer-source",
                "domains",
                "domains/orders",
                "results/deposits",
                "vk",
            ]
        },
        {
            role: "head",
            home: "/dashboard",
            denied: [],
            allowed: [
                "dashboard",
                "facebook",
                "logs",
                "reports/performance",
                "reports/statistics",
                "reports/placements",
                "reports/accounts-banned",
                "reports/gender",
                "reports/affiliates",
                "reports/regions",
                "reports/os",
                "reports/revise",
                "domains",
                "domains/orders",
                "results/deposits",
                "results/leads",
                "reports/conversion-timeline",
                "reports/lead-stats",
                "reports/office-performance",
                "reports/office-performance-copy",
                "reports/office-affiliate-performance",
                "reports/resell-doubles",
                "reports/lead-duplicates",
                "leads-orders",
                "office-payments",
                "affiliates",
                "vk",
                "office-groups",
                "leftovers-by-buyers",
            ]
        },
        {
            role: "verifier",
            home: "/dashboard",
            denied: [],
            allowed: [
                "dashboard",
                "facebook",
                "notifications",
                "facebook/profiles",
                "facebook/accounts",
                "facebook/campaigns",
                "facebook/adsets",
                "facebook/pages",
                "reports/statistics",
                "reports/placements",
                "reports/facebook-performance",
                "reports/performance",
                "vk",
            ]
        },
        {
            role: "support",
            home: "/dashboard",
            denied: [],
            allowed: [
                "dashboard",
                "results/deposits",
                "results/leads",
                "notifications",
                "reports/lead-manager-assignments",
                "reports/affiliates",
                "reports/lead-office-assignments",
                "reports/daily-opt",
                "reports/office-performance",
                "reports/office-performance-copy",
                "reports/office-affiliate-performance",
                "reports/leads-received",
                "reports/lead-stats",
                "reports/night-shift",
                "reports/resell-doubles",
                "reports/offer-source",
                "leads-orders",
                "offices",
                "affiliates",
                "managers",
                "offers",
                "office-payments",
                "reports/conversion-timeline",
                "reports/revise",
                "distribution-rules",
                "proxies",
                "lead-payment-conditions",
                "leads-destinations",
                "office-groups",
                "leftovers-by-buyers",
            ]
        },
        {
            role: "subsupport",
            home: "/dashboard",
            denied: [],
            allowed: [
                "dashboard",
                "results/deposits",
                "results/leads",
                "notifications",
                "reports/lead-manager-assignments",
                "reports/affiliates",
                "reports/lead-office-assignments",
                "reports/daily-opt",
                "reports/office-performance",
                "reports/office-performance-copy",
                "reports/office-affiliate-performance",
                "reports/leads-received",
                "reports/lead-stats",
                "reports/night-shift",
                "leads-orders",
                "offices",
                "affiliates",
                "managers",
                "offers",
                "office-payments",
                "reports/conversion-timeline"
            ]
        },
        {
            role: "developer",
            home: "/results/leads",
            denied: [],
            allowed: [
                "notifications",
                "results/leads",
                "results/deposits",
                "domains",
                "sms/campaigns",
                "leads-orders",
                "offices",
                "offers",
                "offers",
                "binoms",
                "teams",
                "branches",
                "users",
                "distribution-rules",
                "proxies",
                "office-groups",
            ]
        }
    ];

    router.app.$nextTick(() => {
        let user = users.find(user => user.email === router.app.user.email);
        if (!user) {
            user = users.find(
                user => user.branch_id === router.app.user.branch_id
            );
        }

        if (
            !!user &&
            user.allowed.some(path => to.fullPath.match(`/${path}`))
        ) {
            next();
        } else if (
            !!user &&
            user.denied.some(path => to.fullPath.match(`/${path}`))
        ) {
            next("/notifications");
        } else if (router.app.user.role === "admin") {
            next();
        } else {
            const role = roles.find(item => item.role === router.app.user.role);

            if (!role || to.fullPath === role.home) {
                next();
            } else if (
                role.denied.some(path => to.fullPath.match(`/${path}`))
            ) {
                next(role.home);
            } else if (
                role.allowed.length > 0 &&
                !role.allowed.some(path => to.fullPath.match(`/${path}`))
            ) {
                next(role.home);
            } else {
                next();
            }
        }
    });
});

// Ask user permissions to send desktop notifications
try {
    Notification.requestPermission();
} catch (e) {
    // Do nothing.
}

new Vue({
    router: router,
    computed: {
        user() {
            return this.$refs.auth.user;
        },
        isAdmin() {
            return this.user !== null;
            // return this.user && this.user.role === 'admin';
        }
    },
    mounted() {
        this.listen();
    },
    methods: {
        listen() {
            Echo.private(`App.User.${this.user.id}`).notification(
                notification => {
                    if (Notification.permission === "granted") {
                        new Notification(notification.title, {
                            tag: notification.id,
                            body: notification.body,
                            dir: "auto",
                            requireInteraction: true
                        });
                        this.$eventHub.$emit("db-notification", notification);
                    }
                }
            );
        }
    }
}).$mount("#app");

Mousetrap.bind("/", function(e) {
    const el = document.getElementById("search");
    if (el) {
        e.preventDefault();
        el.focus();
    }
});
