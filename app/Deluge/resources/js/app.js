require('alpinejs');
require('./utils');
// require('./socket')

import multiselect from './multiselect';

window.AccountsIndex = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
        },
        accountsToPour: [],
        pourOpened: false,
        init() {
            this.accountsToPour = Array.from(document.querySelectorAll('[name*=accounts]:checked'))
                .map(node => node.value);

            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.BundlesIndex = function () {
    return {
        isBusy: false,
        filters: {
            offer: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.BundlesForm = function () {
    return {
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.CampaignsIndex = function () {
    return {
        isBusy: false,
        filters: {
            bundle: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.CampaignsForm = function () {
    return {
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.InsightsIndex = function () {
    return {
        isBusy: false,
        filters: {
            account: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.InsightsForm = function () {
    return {
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.PerformanceReport = function () {
    return {
        isBusy: false,
        byUser: false,
        filters: {
            offer: null,
            user: null,
        },
        init() {
            if (this.$refs.by_user !== undefined) {
                this.byUser = this.$refs.by_user.hasAttribute('checked');
            }

            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.QuizReport = function () {
    return {
        isBusy: false,
        filters: {
            offer: null,
            user: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.AccountStatsReport = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.BuyerStatsReport = function () {
    return {
        isBusy: false,
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.DesignerConversionReport = function () {
    return {
        isBusy: false,
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.PoursIndex = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.CreditCardsIndex = function () {
    return {
        isBusy: false,
        filters: {
            buyer: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.AverageSpendReport = function () {
    return {
        isBusy: false,
        filters: {
            branches: null,
            teams: null,
            groups: null,
            suppliers: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.BuyerCostsReport = function () {
    return {
        isBusy: false,
        filters: {
            offers: null,
            users: null,
            teams: null,
            bundles: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.LeadStatsReport = function () {
    return {
        isBusy: false,
        filters: {
            offers: null,
            offices: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.DomainsIndex = function () {
    return {
        isBusy: false,
        filters: {
            users: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.LeadsIndex = function () {
    return {
        isBusy: false,
        filters: {
            offer: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.AppsIndex = function () {
    return {
        isBusy: false,
        filters: {
            status: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.UnityOrganizationsIndex = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.UnityAppsIndex = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
            organization: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.UnityCampaignsIndex = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
            organization: null,
            app: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};

window.UnityInsightsIndex = function () {
    return {
        isBusy: false,
        filters: {
            user: null,
            organization: null,
            app: null,
            campaign: null,
        },
        init() {
            this.multiselectInit();
        },
        ...multiselect,
    };
};
