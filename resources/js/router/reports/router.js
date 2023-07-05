export default [
    {
        path: "/reports/statistics",
        component: () =>
            import(
                /* webpackChunkName: "reports-statistics" */ "./Statistics.vue"
            ),
        name: "reports.statistics"
    },
    {
        path: "/reports/daily",
        component: () =>
            import(/* webpackChunkName: "reports-daily" */ "./Daily.vue"),
        name: "reports.daily"
    },
    {
        path: "/reports/daily-opt",
        component: () =>
            import(
                /* webpackChunkName: "reports-daily-opt" */ "./DailyOpt.vue"
            ),
        name: "reports.daily-opt"
    },
    {
        path: "/reports/accounts-daily",
        component: () =>
            import(
                /* webpackChunkName: "reports-accounts-daily" */ "./AccountsDaily.vue"
            ),
        name: "reports.accounts-daily"
    },
    {
        path: "/reports/performance",
        component: () =>
            import(
                /* webpackChunkName: "reports-performance" */ "./Performance.vue"
            ),
        name: "reports.performance"
    },
    {
        path: "/reports/facebook-performance",
        component: () =>
            import(
                /* webpackChunkName: "reports-facebook-performance" */ "./FacebookPerformance.vue"
                ),
        name: "reports.facebook-performance"
    },
    {
        path: "/reports/placements",
        component: () =>
            import(
                /* webpackChunkName: "reports-placements" */ "./Placements.vue"
            ),
        name: "reports.placements"
    },
    {
        path: "/reports/reach-status",
        component: () =>
            import(
                /* webpackChunkName: "reports-reach-status" */ "./ReachStatus.vue"
            ),
        name: "reports.reach-status"
    },
    {
        path: "/reports/telegram-performance",
        component: () =>
            import(
                /* webpackChunkName: "reports-telegram-performance" */ "./TelegramPerformance.vue"
            ),
        name: "reports.telegram-performance"
    },
    {
        path: "/reports/accounts-banned",
        component: () =>
            import(
                /* webpackChunkName: "reports-accounts-banned" */ "./AccountsBanned.vue"
            ),
        name: "reports.accounts-banned"
    },
    {
        path: "/reports/gender",
        component: () =>
            import(/* webpackChunkName: "reports-gender" */ "./Gender.vue"),
        name: "reports.gender"
    },
    {
        path: "/reports/regions",
        component: () =>
            import(/* webpackChunkName: "reports-regions" */ "./Regions.vue"),
        name: "reports.regions"
    },
    {
        path: "/reports/conversion",
        component: () =>
            import(
                /* webpackChunkName: "reports-conversion" */ "./Conversion.vue"
            ),
        name: "reports.conversion"
    },
    {
        path: "/reports/os",
        component: () =>
            import(
                /* webpackChunkName: "reports-os" */ "./OperatingSystem.vue"
            ),
        name: "reports.os"
    },
    {
        path: "/reports/average-spend",
        component: () =>
            import(
                /* webpackChunkName: "reports-average-spend" */ "./AverageSpend.vue"
            ),
        name: "reports.average-spend"
    },
    {
        path: "/reports/designer-performance",
        component: () =>
            import(
                /* webpackChunkName: "reports-designer-performance" */ "./DesignerPerformance.vue"
            ),
        name: "reports.designer-performance"
    },
    {
        path: "/reports/lead-stats",
        component: () =>
            import(
                /* webpackChunkName: "reports-lead-stats" */ "./LeadStats.vue"
            ),

        name: "reports.lead-stats"
    },
    {
        path: "/reports/current-rates",
        component: () =>
            import(
                /* webpackChunkName: "reports-current-rates" */ "./CurrentRates.vue"
            ),
        name: "reports.current-rates"
    },
    {
        path: "/reports/account-stats",
        component: () =>
            import(
                /* webpackChunkName: "reports-account-stats" */ "./AccountStats.vue"
            ),
        name: "reports.account-stats"
    },
    {
        path: "/reports/ad-disapproval-reason",
        component: () =>
            import(
                /* webpackChunkName: "reports-ad-disapproval-reason" */ "./AdDisapprovalReason.vue"
            ),
        name: "reports.ad-disapproval-reason"
    },
    {
        path: "/reports/week-days-conversion",
        component: () =>
            import(
                /* webpackChunkName: "reports-week-days-conversion" */ "./WeekDaysConversion.vue"
            ),
        name: "reports.week-days-conversion"
    },
    {
        path: "/reports/leads-received",
        component: () =>
            import(
                /* webpackChunkName: "reports-leads-received" */ "./LeadsReceived.vue"
            ),
        name: "reports.leads-received"
    },
    {
        path: "/reports/lead-manager-assignments",
        component: () =>
            import(
                /* webpackChunkName: "reports-lead-manager-assignments" */ "./LeadManagerAssignments.vue"
            ),
        name: "reports.lead-manager-assignments"
    },
    {
        path: "/reports/affiliates",
        component: () =>
            import(
                /* webpackChunkName: "reports-affiliates" */ "./Affiliates.vue"
            ),
        name: "reports.affiliates"
    },
    {
        path: "/reports/profiles-banned",
        component: () =>
            import(
                /* webpackChunkName: "reports-profiles-banned" */ "./ProfilesBanned.vue"
            ),
        name: "reports.profiles-banned"
    },
    {
        path: "/reports/office-performance",
        component: () =>
            import(
                /* webpackChunkName: "reports-office-performance" */ "./OfficePerformance.vue"
            ),
        name: "reports.office-performance"
    },
    {
        path: "/reports/office-performance-copy",
        component: () =>
            import(
                /* webpackChunkName: "reports-office-performance-copy" */ "./OfficePerformanceCopy.vue"
            ),
        name: "reports.office-performance-copy"
    },
    {
        path: "/reports/office-affiliate-performance",
        component: () =>
            import(
                /* webpackChunkName: "reports-office-affiliate-performance" */ "./OfficeAffiliatePerformance.vue"
                ),
        name: "reports.office-affiliate-performance"
    },
    {
        path: "/reports/revenue",
        component: () =>
            import(
                /* webpackChunkName: "reports-office-revenue" */ "./Revenue.vue"
            ),
        name: "reports.revenue"
    },
    {
        path: "/reports/lead-office-assignments",
        component: () =>
            import(
                /* webpackChunkName: "reports-lead-office-assignments" */ "./LeadOfficeAssignments.vue"
            ),
        name: "reports.lead-office-assignments"
    },
    {
        path: "/reports/night-shift",
        component: () =>
            import(
                /* webpackChunkName: "reports-night-shift" */ "./NightShift.vue"
            ),
        name: "reports.night-shift"
    },
    {
        path: "/reports/conversion-stats",
        component: () =>
            import(
                /* webpackChunkName: "reports-conversion-stats" */ "./ConversionStats.vue"
            ),
        name: "reports.conversion-stats"
    },
    {
        path: "/reports/conversion-timeline",
        component: () =>
            import(
                /* webpackChunkName: "reports-conversion-timeline" */ "./ConversionTimeline.vue"
            ),
        name: "reports.conversion-timeline"
    },
    {
        path: "/reports/monthly-dtd-noa",
        component: () =>
            import(
                /* webpackChunkName: "monthly-dtd-noa" */ "./MonthlyDtdNOA.vue"
                ),
        name: "reports.monthly-dtd-noa"
    },
    {
        path: "/reports/monthly-offices-noa",
        component: () =>
            import(
                /* webpackChunkName: "monthly-offices-noa" */ "./MonthlyOfficesNOA.vue"
                ),
        name: "reports.monthly-offices-noa"
    },
    {
        path: "/reports/monthly-office-cr",
        component: () =>
            import(
                /* webpackChunkName: "monthly-office-cr" */ "./MonthlyOfficeCR.vue"
                ),
        name: "reports.monthly-office-cr"
    },
    {
        path: "/reports/monthly-offer-cr",
        component: () =>
            import(
                /* webpackChunkName: "monthly-offer-cr" */ "./MonthlyOfferCR.vue"
                ),
        name: "reports.monthly-offer-cr"
    },
    {
        path: "/reports/assignment-labels",
        component: () =>
            import(
                /* webpackChunkName: "assignment-labels" */ "./AssignmentLabels.vue"
                ),
        name: "reports.assignment-labels"
    },
    {
        path: "/reports/revise",
        component: () =>
            import(
                /* webpackChunkName: "revise" */ "./Revise.vue"
                ),
        name: "reports.revise"
    },
    {
        path: "/reports/revise-v2",
        component: () =>
            import(
                /* webpackChunkName: "revise" */ "./Revise2.vue"
                ),
        name: "reports.revise-v2"
    },
    {
        path: "/reports/dtd-cr",
        component: () =>
            import(
                /* webpackChunkName: "dtd-cr" */ "./DtDCR.vue"
                ),
        name: "reports.dtd-cr"
    },
    {
        path: "/reports/resell-doubles",
        component: () =>
            import(
                /* webpackChunkName: "resell-doubles" */ "./ResellDoubles.vue"
                ),
        name: "reports.resell-doubles"
    },
    {
        path: "/reports/lead-duplicates",
        component: () =>
            import(
                /* webpackChunkName: "lead-duplicates" */ "./LeadDuplicates.vue"
                ),
        name: "reports.lead-duplicates"
    },
    {
        path: "/reports/rejected-unique",
        component: () =>
            import(
                /* webpackChunkName: "rejected-unique" */ "./RejectedUnique.vue"
                ),
        name: "reports.rejected-unique"
    },
    {
        path: "/reports/offer-source",
        component: () =>
            import(
                /* webpackChunkName: "offer-source" */ "./OfferSource.vue"
                ),
        name: "reports.offer-source"
    },
    {
        path: "/reports/leftovers-by-buyers",
        component: () =>
            import(
                /* webpackChunkName: "offer-source" */ "./LeftoversByBuyers.vue"
                ),
        name: "reports.leftovers-by-buyers"
    },
];
