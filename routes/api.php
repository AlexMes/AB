<?php

Route::prefix('users')->group(static function () {
    Route::put('/{user}/reset-password', 'Users\ResetPassword')->name('api.users.reset-password');
    Route::get('/{user}/profiles', 'Users\Profiles');
    Route::get('/{user}/accounts', 'Users\Accounts');
    Route::get('/{user}/deposits', 'Users\Deposits');
    Route::get('/{user}/leads', 'Users\Leads');
    Route::get('/{user}/pages', 'Users\Pages');
    Route::get('/{user}/accesses', 'Users\Accesses');
    Route::as('api.')->group(static function () {
        Route::apiResource('/{user}/denied-telegram-notifications', 'Users\DeniedTelegramNotificationController')
            ->only(['store', 'destroy', 'index']);
        Route::apiResource('/{user}/firewall', 'Users\Firewall')
            ->only(['index', 'store', 'destroy']);
    });
    Route::apiResource('/{user}/notifications', 'Users\Notifications')->only('index');
    Route::post('/{user}/notifications/mark-all-read', 'Users\MarkNotificationsRead');
    Route::post('/{user}/notifications/{notification}/mark-read', 'Users\MarkNotificationRead');
    Route::as('api.')->group(static function () {
        Route::apiResource('/{user}/allowed-offers', 'Users\AllowedOffersController')
            ->only(['index', 'store', 'destroy']);
    });
    Route::as('api.')->group(static function () {
        Route::apiResource('/{user}/shared-users', 'Users\SharedUsersController')
            ->only(['index', 'store', 'destroy']);
    });

    Route::get('/{user}/google-tfa-secret', 'Users\GoogleTFASecret@show');
    Route::post('/{user}/google-tfa-secret', 'Users\GoogleTFASecret@store');
    Route::delete('/{user}/google-tfa-secret', 'Users\GoogleTFASecret@destroy');
});
Route::apiResource('users', 'Users\UsersController');
Route::get('/report-buyers', 'ReportBuyersController');
Route::get('/report-accounts', 'ReportAccountsController');

Route::prefix('campaigns')->namespace('Campaigns')->as('api.campaigns.')->group(static function () {
    Route::post('/{campaign}/status', 'Start')->name('start');
    Route::delete('/{campaign}/status', 'Stop')->name('stop');
    Route::post('/stop', 'MassStop')->name('mass-stop');
});
Route::apiResource('campaigns', 'Campaigns\CampaignsController')->only('index');

Route::prefix('reports')->namespace('Reports')
    ->middleware(\App\Http\Middleware\AccessToReports::class)
    ->group(static function () {
        Route::get('/statistic', 'Statistic');
        Route::get('/daily', 'Daily');
        Route::get('/daily-opt', 'DailyOpt');
        Route::get('/accounts-daily', 'AccountsDaily');
        Route::get('/quick-stats', 'QuickStats')->withoutMiddleware(\App\Http\Middleware\AccessToReports::class);
        Route::get('/performance', 'Performance');
        Route::get('/campaign-stats', 'CampaignStats');
        Route::get('/traffic-loss', 'TrafficLoss');
        Route::get('/placements', 'Placements');
        Route::get('/facebook-performance', 'FacebookPerformance');
        Route::get('/buyers-accounts-stats', 'BuyersAccountsStats')
            ->withoutMiddleware(\App\Http\Middleware\AccessToReports::class);
        Route::get('/buyers-month-stats', 'BuyersMonthStats');
        Route::get('/office-stats', 'OfficeStats');
        Route::get('/sms-stats', 'SmsStats');
        Route::get('/reach-status', 'ReachStatus');
        Route::get('/telegram-performance', 'TelegramPerformance');
        Route::get('/accounts-banned', 'AccountsBanned');
        Route::get('/gender', 'Gender');
        Route::get('/regions', 'Regions');
        Route::get('/conversion', 'Conversion');
        Route::get('/buyers-performance-stats', 'BuyersPerformanceStats')
            ->withoutMiddleware(\App\Http\Middleware\AccessToReports::class);
        Route::get('/os', 'OperatingSystems');
        Route::get('/average-spend', 'AverageSpend');
        Route::get('/designer-performance', 'DesignerPerformance');
        Route::get('/lead-stats', 'LeadStats');
        Route::get('/current-rates', 'CurrentRates');
        Route::get('/account-stats', 'AccountStats');
        Route::get('/ad-disapproval-reason', 'AdDisapprovalReason');
        Route::get('/week-days-conversion', 'WeekDaysConversion');
        Route::get('/leads-received', 'LeadsReceived');
        Route::get('/lead-manager-assignments', 'LeadManagerAssignments');
        Route::get('/office-performance', 'OfficePerformance');
        Route::get('/office-performance-copy', 'OfficePerformanceCopy');
        Route::get('/office-affiliate-performance', 'OfficeAffiliatePerformance');
        Route::get('/revenue', 'Revenue');
        Route::post('/offer-source', 'OfferSource');

        Route::prefix('exports')->namespace('Exports')->group(static function () {
            Route::get('/performance', 'Performance');
            Route::get('/facebook-performance', 'FacebookPerformance');
            Route::get('/lead-manager-assignments', 'LeadManagerAssignments');
            Route::get('/conversion-stats', 'ConversionStats');
            Route::get('/monthly-dtd-noa', 'MonthlyDtdNOA');
            Route::get('/monthly-offices-noa', 'MonthlyOfficesNOA');
            Route::get('/monthly-office-cr', 'MonthlyOfficeCR');
            Route::get('/monthly-offer-cr', 'MonthlyOfferCR');
            Route::get('/assignment-labels', 'AssignmentLabels');
            Route::get('/revise', 'Revise');
            Route::get('/revise-v2', 'Revise2');
            Route::get('/lead-stats', 'LeadStats');
        });

        Route::get('/offer-stats', 'OfferStats');
        Route::get('/affiliates', 'Affiliates');
        Route::get('/profiles-banned', 'ProfilesBanned');
        Route::get('/facebook-stats', 'FacebookStats')
            ->withoutMiddleware(\App\Http\Middleware\AccessToReports::class);
        Route::get('/our-assignments', 'OurAssignments')
            ->withoutMiddleware(\App\Http\Middleware\AccessToReports::class);
        Route::get('/lead-office-assignments', 'LeadOfficeAssignments');
        Route::get('/night-shift', 'NightShift');
        Route::get('/conversion-stats', 'ConversionStats');
        Route::get('/monthly-dtd-noa', 'MonthlyDtdNOA');
        Route::get('/monthly-offices-noa', 'MonthlyOfficesNOA');
        Route::get('/monthly-office-cr', 'MonthlyOfficeCR');
        Route::get('/monthly-offer-cr', 'MonthlyOfferCR');
        Route::get('/assignment-labels', 'AssignmentLabels');
        Route::get('/conversion-timeline', 'ConversionTimeline');
        Route::get('/revise', 'Revise');
        Route::get('/revise-v2', 'Revise2');
        Route::get('/dtd-cr', 'DtDCR');
        Route::get('/resell-doubles', 'ResellDoubles');
        Route::get('/lead-duplicates', 'LeadDuplicates');
        Route::get('/rejected-unique', 'RejectedUnique');
        Route::get('/leftovers-by-buyers', 'LeftoversByBuyers');
    });

Route::prefix('enums')->namespace('Enums')->group(static function () {
    Route::get('/offices', 'Offices');
    Route::get('/devices', 'Devices');
    Route::get('/os', 'Os');
    Route::get('/leads-markers', 'LeadsMarkers');
});

Route::as('api')->post('/lead-destinations/configuration', 'LeadDestinationsConfigController');

Route::apiResource('leads', 'LeadsController');
Route::post('/leads/{lead}/restore', 'LeadRestoreController');
Route::post('/leads/{lead}/mark-leftover', 'MarkLeadLeftoverController')->name('leads.mark-leftover');
Route::post('/leads/{lead}/mark-unpayable', 'MarkLeadUnpayableController')->name('leads.mark-unpayable');
Route::apiResource('offices', 'OfficesController');
Route::get('/offices/{office}/users', 'OfficeUsers');
Route::apiResource('offices.managers', 'OfficeManagers')->only('index', 'store', 'destroy');
Route::apiResource('offices.orders', 'OfficeOrders')->only('index');
Route::as('api')->apiResource('offices.status-configs', 'OfficeStatusConfigs')->only('index');
Route::as('api.')->namespace('StatusConfigs')->group(static function () {
    Route::apiResource('status-configs', 'StatusConfigController')->except(['index']);
    Route::post('status-configs/{status_config}/run', 'RunController')->name('status-configs.run');
});
Route::as('api')->apiResource('offices.office-payments', 'OfficePayments')->only('index');
Route::as('api.')->namespace('OfficePayments')->group(static function () {
    Route::apiResource('office-payments', 'OfficePaymentController')->except(['index']);
});
Route::apiResource('offices.morning-branches', 'OfficeMorningBranches')->only('index', 'store', 'destroy');

Route::apiResource('offers', 'OffersController');
Route::as('api.')->group(static function () {
    Route::apiResource('/offers/{offer}/allowed-users', 'Offers\AllowedUsersController')
        ->only(['index', 'store', 'destroy']);
});
Route::apiResource('results', 'ResultsController');

Route::apiResource('orders', 'OrdersController');
Route::get('/orders/{order}/reject-reason-stats', 'OrderRejectReasonStats');
Route::get('orders/{order}/domains', 'OrderDomains');
Route::post('orders/{order}/domains', 'Orders\MassStoreDomains');
Route::post('orders/{order}/transfer-domains', 'TransferOrderDomains')->name('order-transfer-domains');

Route::post('domains/change-bayer-domains', 'ChangeBayerDomains')->name('change-bayer-domains');
Route::get('domains/{domain}/ads', 'DomainAdsController');
Route::apiResource('domains', 'DomainsController');
Route::apiResource('domains.sms-campaigns', 'DomainSmsCampaignsController')->only(['index', 'store', 'update']);

Route::prefix('profiles')->namespace('Profiles')->group(static function () {
    Route::get('{profile}/accounts', 'Accounts');
    Route::get('{profile}/campaigns', 'Campaigns');
    Route::get('{profile}/adsets', 'Adsets');
    Route::get('{profile}/ads', 'Ads');
    Route::post('{profile}/sync', 'SyncProfile');
    Route::get('{profile}/pages', 'Pages');
});
Route::apiResource('profiles', 'Profiles\ProfilesController')->except('store');
Route::apiResource('accounts', 'AccountsController')->except('store', 'destroy');
Route::get('payment-fails', 'PaymentFailsController');
Route::apiResource('profile-pages', 'ProfilePagesController')->only('index', 'show');
Route::get('profile-pages/{page}/fb-comments', 'Profiles\FacebookComments');

Route::apiResource('adsets', 'Adsets\AdsetsController')->only('index');
Route::post('/adsets/{adset}/status', 'Adsets\Start');
Route::delete('/adsets/{adset}/status', 'Adsets\Stop');
Route::post('/adsets/stop', 'Adsets\MassStop');
Route::post('/adsets/{adset}/start-midnight', 'Adsets\StartMidnight@store');
Route::delete('/adsets/{adset}/start-midnight', 'Adsets\StartMidnight@destroy');
Route::apiResource('stopped-adsets', 'Adsets\Stopped')->only(['index']);

Route::apiResource('deposits', 'DepositsController');
Route::apiResource('accesses', 'AccessController')->except('destroy');

Route::get('/accesses/{access}/ap', 'RevealAccessAccountPassword');
Route::get('/accesses/{access}/ep', 'RevealAccessEmailPassword');

Route::apiResource('suppliers', 'SuppliersController')->except('destroy');
Route::apiResource('pages', 'PagesController');
Route::apiResource('cloaks', 'CloaksController');
Route::apiResource('registrars', 'RegistrarsController');
Route::apiResource('hostings', 'HostingsController');


Route::prefix('sms')->namespace('Sms')->group(static function () {
    Route::get('messages', 'MessageController');
    Route::apiResource('campaigns', 'CampaignController');
    Route::get('drivers', 'DriverController')->name('sms.drivers');
});

Route::get('utm-campaigns', 'UtmCampaigns')->name('utm-campaigns.index');


Route::apiResource('domains.comments', 'Comments\DomainComments')
    ->only('index', 'store', 'update', 'destroy');

Route::prefix('imports')->namespace('Imports')->as('api.imports.')->group(static function () {
    Route::post('deposits', 'DepositsController@store')->name('deposits.store');
    Route::get('deposits/leads', 'DepositImportLeadsController')->name('deposits.leads');
    Route::apiResource('leads', 'Leads\Import')
        ->parameter('leads', 'filename')
        ->except('index', 'show');
    Route::post('upload-leads', 'Leads\LeadsUploadController')->name('uploads.leads');
});

Route::prefix('exports')->namespace('Exports')->as('api.exports.')->group(static function () {
    Route::get('deposits', 'DepositsExportController')->name('deposits');
});

Route::apiResource('groups', 'GroupController');
Route::post('/dev/binom-check', 'RunBinomCheck');

Route::prefix('telegram')->as('telegram.')->group(function () {
    Route::apiResource('subjects', 'TelegramChannelSubjectController')->only('index', 'store');
    Route::apiResource('channels', 'TelegramChannelController');
    Route::apiResource('channels.stats', 'TelegramChannelStatisticController');
    Route::get('/statistics', 'TelegramStatsController');
    Route::post('/statistics-bulk', 'StoreTelegramChannelsBulkStatistics');
});

Route::apiResource('bundles', 'BundleController')->except(['destroy']);
Route::apiResource('bundles.comments', 'Comments\BundleController')->except(['show']);
Route::apiResource('bundles.files', 'Files\BundleController')->except(['update']);

Route::get('/ads', 'Ads\AdsController');
Route::get('/ads/disapprovals', 'Ads\AdsDisapprovalsController');
Route::get('/ads/disapproval-reasons', 'Ads\DisapprovalReasonController');

Route::apiResource('placements', 'PlacementController')->only(['index']);

Route::apiResource('tags', 'TagsController');

Route::apiResource('tags.ads', 'Tags\AdsController')->except(['show', 'update']);
Route::get('/leads-orders/leftovers-stats', 'LeadsOrdersLeftoversStats');
Route::apiResource('leads-orders', 'LeadsOrdersController');

Route::get('/leads-orders/{order}/routes', 'IndexOrderRoutes');
Route::post('/leads-orders/{order}/routes', 'CreateOrderRoute');
Route::put('/leads-order-routes/{route}', 'UpdateOrderRoute');
Route::delete('/leads-order-routes/{route}', 'DestroyOrderRoute');
Route::post('/leads-order-routes/{route}/restore', 'RestoreLeadOrderRoute');
Route::get('/leads-order-routes/{route}/assignments', 'IndexRouteAssignments');

Route::post('/leads-order-routes/{route}/transfer', 'TransferRoute');
Route::post('/leads-order-routes/{route}/change-offer', 'LeadOrderRoute\ChangeOffer');

Route::post('/leads-order-routes/{route}/start', 'LeadOrderRoute\Start');
Route::post('/leads-order-routes/{route}/pause', 'LeadOrderRoute\Pause');
Route::post('/leads-order-routes/{route}/stop', 'LeadOrderRoute\Stop');
Route::post('/leads-order-routes/{route}/remove-delayed-assignments', 'LeadOrderRoute\RemoveDelayedAssignments');
Route::post('/leads-order-routes/{route}/remove-undelivered-assignments', 'LeadOrderRoute\RemoveUndeliveredAssignments');
Route::post('/leads-order-routes/start', 'LeadOrderRoute\StartMass');
Route::post('/leads-order-routes/pause', 'LeadOrderRoute\PauseMass');
Route::post('/leads-order-routes/stop', 'LeadOrderRoute\StopMass');
Route::post('/leads-order/{order}/start', 'LeadsOrder\Start');
Route::post('/leads-order/{order}/pause', 'LeadsOrder\Pause');
Route::post('/leads-order/{order}/stop', 'LeadsOrder\Stop');
Route::post('/leads-order/{order}/clone', 'LeadsOrder\CloneLeadOrder');
Route::get('/leads-order/{order}/managers', 'LeadsOrder\Managers');
Route::post('/leads-order/{order}/transfer', 'LeadsOrder\TransferRoutes');
Route::get('/leads-order/{order}/offers', 'LeadsOrder\Offers');
Route::post('/leads-order/{order}/change-offer', 'LeadsOrder\ChangeRoutesOffer');
Route::post('/leads-order/{order}/remove-delayed-assignments', 'LeadsOrder\RemoveDelayedAssignments');
Route::post('/leads-order/{order}/remove-undelivered-assignments', 'LeadsOrder\RemoveUndeliveredAssignments');

Route::post('/offers/{offer}/start-lead-order-routes', 'Offers\StartLeadOrderRoutes');
Route::post('/offers/{offer}/pause-lead-order-routes', 'Offers\PauseLeadOrderRoutes');
Route::post('/offers/{offer}/stop-lead-order-routes', 'Offers\StopLeadOrderRoutes');

Route::apiResource('notification-types', 'NotificationTypeController')->only(['index']);

Route::get('/leads-orders-stats/progress', 'LeadsAssignmentsStats')->name('leads-orders.assignment-stats');
Route::get('/leads-orders-leftovers', 'IndexOrdersLeftOvers');
Route::post('/leads-orders-leftovers', 'AssignOrdersLeftOvers');
Route::post('/leads-orders-leftovers-smooth', 'SmoothAssignOrdersLeftOvers');
Route::post('/pack-leftovers', 'PackLeftoversController');
Route::post('/unpack-leftovers', 'UnpackLeftoversController');
Route::post('/distribute-leftovers', 'DistributeLeftoversController');

Route::get('utm-content', 'UtmContent');
Route::get('utm-sources', 'UtmSource');

Route::patch('/facebook/campaigns/{campaign}', 'Campaigns\FacebookCampaigns@update');
Route::patch('/facebook/adsets/{adset}', 'Adsets\FacebookAdsets@update');

Route::get('/me', 'Users\MyProfile')->name('me');

Route::get('/placement-list', 'PlacementListController');

Route::apiResource('managers', 'ManagerController')->except(['store', 'destroy']);
Route::put('/managers/{manager}/password', 'ResetManagerPassword');
Route::post('/managers/{manager}/change-office', 'ChangeManagerOfficeController');

Route::post('/assignments/{assignment}/transfer', 'TransferAssignment');
Route::post('/assignments/{assignment}/mark-unpayable', 'MarkAssignmentUnpayableController');
Route::delete('/assignments/{assignment}', 'DestroyOrderRouteAssignment')->name('api.assignments.destroy');
Route::put('/assignments/{assignment}', 'UpdateOrderRouteAssignment')->name('api.assignments.update');
Route::get('/assignment-delivery-fails', 'AssignmentDeliveryFails');
Route::post('/resend-assignment-delivery-fail/{assignment}', 'ResendAssignmentDeliveryFail');

Route::get('affiliates/{affiliate}/leads', 'AffiliateLeadsController');
Route::get('affiliates/{affiliate}/export-leads', 'Exports\AffiliateLeadsController')
    ->name('affiliates.export-leads');
Route::get('affiliates/{affiliate}/export-leads-hide-deps', 'Exports\AffiliateLeadsHideDepsController')
    ->name('affiliates.export-leads-hide-deps');
Route::apiResource('affiliates', 'AffiliateController');
Route::apiResource('binoms', 'BinomController');
Route::post('/affiliates/{affiliate}/import', 'ImportAffiliateLeads');
Route::post('/affiliates/{affiliate}/{filename}/continue-import', 'ContinueAffiliateLeadsImport');
Route::apiResource('leads-destinations', 'LeadDestinationController');
Route::post('/leads-destinations/{leads_destination}/test', 'TestLeadDestinationController')
    ->name('leads-destinations.test');
Route::post('/leads-destinations/{leads_destination}/collect-statuses', 'CollectLeadDestinationStatusController')
    ->name('leads-destinations.collect-statuses');
Route::post('/leads-destinations/{leads_destination}/collect-results', 'CollectLeadDestinationResultsController')
    ->name('leads-destinations.collect-results');
Route::get('/lead-destination-drivers', 'LeadDestinationDriverController');
Route::apiResource('traffic-sources', 'TrafficSourcesController')->except('destroy');
Route::get('/traffic-sources/{traffic_source}/domains', 'TrafficSources\Domains');

Route::apiResource('profile-logs', 'ProfileLogController');

Route::apiResource('/binom/campaigns', 'Binom\CampaignsController')->only('index', 'show', 'update');
Route::apiResource('servers', 'ServersController')->only('index', 'show');
Route::post('/servers/{server}/sites', 'Forge\CacheSites')->name('servers.cache-sites');

Route::apiResource('leads.events', 'LeadsEventsController')->only('index');
Route::apiResource('leads.markers', 'LeadsMarkersController')->only(['index', 'destroy']);

Route::get('/external/accounts', 'External\AccountList');

Route::apiResource('apps', 'GoogleAppController')->except('destroy');
Route::post('apps/{app}/status', 'GoogleAppStatusController@store')->name('app.enable');
Route::delete('apps/{app}/status', 'GoogleAppStatusController@destroy')->name('app.disable');

Route::apiResource('teams.users', 'TeamUsersController')->only(['index', 'store', 'destroy']);
Route::apiResource('teams', 'TeamController');
Route::as('api')->apiResource('leads.events', 'LeadsEventsController')->only('index');
Route::delete('/facebook/apps/{facebook_app}/cache', 'FacebookAppsCacheController')->name('facebook.apps.cache.destroy');
Route::apiResource('/facebook/apps', 'FacebookAppsController')->only('index', 'store')
    ->names(['index' => 'facebook.apps.index', 'store' => 'facebook.apps.store']);
Route::put('/facebook/apps/{facebook_app}', 'FacebookAppsController@update')->name('facebook.apps.update');

Route::apiResource('manual-accounts', 'ManualAccountController');

Route::put('/binom-traffic-sources/{binom_traffic_source}/traffic-source', 'BinomTrafficSources\TrafficSource');

Route::apiResource('labels', 'Labels')->only(['index']);
Route::apiResource('tenants', 'TenantController');
Route::post('/tenants/{tenant}/generate-api-token', 'Tenants\GenerateApiToken');
Route::post('/tenants/{tenant}/revoke-api-token', 'Tenants\RevokeApiToken');
Route::get('/statuses', 'StatusController');
Route::get('/ages', 'AgeController');

Route::get('/resell-batches/progress-stats', 'ResellBatches\ProgressStatsController')
    ->name('resell-batches.progress-stats');
Route::apiResource('resell-batches', 'ResellBatches\ResellBatchController')->except(['destroy']);
Route::apiResource('resell-batches.leads', 'ResellBatches\LeadController')->only(['store']);
Route::post('/resell-batches/{resell_batch}/start', 'ResellBatches\StartController')->name('start-resell-batch');
Route::post('/resell-batches/{resell_batch}/pause', 'ResellBatches\PauseController')->name('pause-resell-batch');
Route::post('/resell-batches/{resell_batch}/cancel', 'ResellBatches\CancelController')->name('cancel-resell-batch');
Route::get('/resell-batch-leads', 'ResellBatchLeadsController')->name('resell-batch-leads');

Route::apiResource('branches', 'BranchController');
Route::prefix('branches')->as('api.branches.')->group(static function () {
    Route::get('/{branch}/teams', 'BranchTeams')->name('teams.index');
    Route::get('/{branch}/users', 'BranchUsers')->name('users.index');
    Route::post('/{branch}/send-tg-message', 'SendBranchTgMessageController')
        ->name('send-tg-message');
});
Route::as('api')->apiResource('branches.offices', 'BranchOfficeController')
    ->only(['index', 'store', 'destroy']);

Route::as('api')->apiResource('allowed-branches', 'AllowedBranchesController')
    ->only(['index', 'store', 'destroy']);

Route::apiResource('office-statuses', 'OfficeStatusController')->except(['show']);

Route::get('/geo/countries', 'Geo\CountriesController')->name('geo.countries.index');

Route::apiResource('distribution-rules', 'DistributionRulesController');

Route::as('api.')->group(static function () {
    Route::apiResource('proxies', 'ProxyController');
    Route::post('proxies/{proxy}/check', 'ProxyCheckController')->name('proxies.check');
});

Route::as('api')->apiResource('lead-payment-conditions', 'LeadPaymentConditionController');

Route::as('api')->apiResource('vk-profiles', 'VK\ProfilesController')
    ->parameters(['vk-profiles' => 'profile'])
    ->only(['index', 'show', 'update']);

Route::post('/mass-set-lead-benefit', 'LeadsOrder\MassSetLeadBenefit')->name('mass-set-lead-benefit');

Route::get('/lead-apps', 'LeadAppsController')->name('lead-apps');

Route::as('api.')->group(static function () {
    Route::apiResource('office-groups', 'OfficeGroups\OfficeGroupController');
    Route::apiResource('office-groups.offices', 'OfficeGroups\OfficesController')->only(['index', 'store', 'destroy']);
});

Route::post('leads-copy-to-offer', 'LeadsCopyToOfferController')->name('leads-copy-to-offer');
Route::post('leads-pack-cold-base', 'LeadsPackColdBaseController')->name('leads-pack-cold-base');
Route::post('leads-delete-duplicates', 'LeadsDeleteDuplicatesController')->name('leads-delete-duplicates');
Route::post('/leads/export', 'LeadsExportController')->name('leads.export');
Route::post('leads-leftovers-change-offer', 'LeadsLeftoversChangeOfferController')->name('leads-delete-duplicates');

Route::as('api')->apiResource('black-leads', 'BlackLeadController')->only(['index', 'store', 'destroy']);
