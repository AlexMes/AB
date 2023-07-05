import Index from "./Index.vue";
import Offices from "./offices/router.js";
import Users from "./users/router.js";
import TrafficSources from "./traffic-sources/router.js";
import Teams from "./teams/router.js";
import Tenants from "./tenants/router.js";
import Branches from "./branches/router.js";
import Offers from "./offers/router.js";
import Forms from "./forms/router.js";
import Pages from "./pages/router.js";
import Groups from "./groups/router.js";
import Tags from "./tags/router.js";
import Suppliers from "./suppliers/router.js";
import Affiliates from "./affiliates/router.js";
import Binoms from "./binoms/router.js";
import StatusConfigs from "./status-configs/router.js";
import OfficePayments from "./office-payments/router.js";
import LeadsDestinations from "./leads-destinations/router.js";
import DistributionRules from "./distribution-rules/router.js";
import Proxies from "./proxies/router.js";
import LeadPaymentConditions from "./lead-payment-conditions/router.js";
import OfficeGroups from "./office-groups/router.js";
export default [
    {
        path: "/settings",
        component: Index,
        children: [
            {
                path: "",
                redirect: "/users"
            },
            {
                path: "/app-users",
                component: () => import(/* webpackChunkName: "dev-app-users" */ "./AppUsers"),
            },
            ...Offers,
            ...Forms,
            ...Pages,
            ...Groups,
            ...Tags,
            ...Suppliers,
            ...Affiliates,
            ...Binoms,
            ...Offices,
            ...Users,
            ...TrafficSources,
            ...Teams,
            ...Tenants,
            ...Branches,
            ...StatusConfigs,
            ...OfficePayments,
            ...LeadsDestinations,
            ...DistributionRules,
            ...Proxies,
            ...LeadPaymentConditions,
            ...OfficeGroups,
        ]
    }

];
