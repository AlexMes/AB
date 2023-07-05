import Vue from "vue";
import Router from "vue-router";
import Dashboard from "./Dashboard";
import Profiles from "./profiles/router.js";
import Accounts from "./accounts/router.js";
import Settings from "./settings/router.js";
import Campaigns from "./campaigns/router.js";
import Orders from "./orders/router.js";
import Domains from "./domains/router.js";
import Reports from "./reports/router.js";
import Adsets from "./adsets/router.js";
import Leads from "./leads/router.js";
import Deposits from "./deposits/router.js";
import Results from "./results/router.js";
import Accesses from "./accesses/router.js";
import Sms from "./sms/router.js";
import ProfilePages from "./profile-pages/router.js";
import Telegram from "./telegram/router.js";
import Bundles from "./bundles/router.js";
import LeadOrders from "./lead-orders/router.js";
import Notifications from "./notifications/router.js";
import ProfileLogs from "./profile-logs/router.js";
import Logs from "./logs/router.js";
import Servers from "./servers/router.js";
import Apps from "./apps/router.js";
import FacebookApps from "./apps/facebook/router.js";
import ManualAccounts from "./manual-accounts/router.js";
import Managers from "./managers/router.js";
import ResellBatches from "./resell-batches/router.js";
import VKProfiles from "./vk-profiles/router.js";

Vue.use(Router);

const modules = [
    ...Profiles,
    ...Campaigns,
    ...Settings,
    ...Orders,
    ...Domains,
    ...Reports,
    ...Accounts,
    ...Adsets,
    ...Leads,
    ...Deposits,
    ...Results,
    ...Accesses,
    ...Sms,
    ...ProfilePages,
    ...Telegram,
    ...Bundles,
    ...LeadOrders,
    ...Notifications,
    ...ProfileLogs,
    ...Logs,
    ...Servers,
    ...Apps,
    ...FacebookApps,
    ...ManualAccounts,
    ...Managers,
    ...ResellBatches,
    ...VKProfiles,
];

const routes = [
    { path: "", redirect: "dashboard" },
    { path: "/dashboard", component: Dashboard, name: "dashboard" },
    { path: "/reports", redirect: "/reports/statistics" },
    ...modules
];

export default new Router({
    mode: "history",
    base: "/",
    hashbang: false,
    routes: routes
});
