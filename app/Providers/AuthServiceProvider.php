<?php

namespace App\Providers;

use App\Access;
use App\AccessSupplier;
use App\Affiliate;
use App\Binom;
use App\BlackLead;
use App\Branch;
use App\Bundle;
use App\Comment;
use App\CRM\Tenant;
use App\Deposit;
use App\DistributionRule;
use App\Domain;
use App\Facebook\Account;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Facebook\FacebookApp;
use App\Facebook\Profile;
use App\Facebook\ProfilePage;
use App\Group;
use App\Lead;
use App\LeadDestinationDriver;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\LeadPaymentCondition;
use App\LeadsOrder;
use App\Manager;
use App\ManualAccount;
use App\ManualApp;
use App\ManualBundle;
use App\ManualCampaign;
use App\ManualCreditCard;
use App\ManualGroup;
use App\ManualInsight;
use App\ManualPour;
use App\ManualSupplier;
use App\ManualTrafficSource;
use App\Offer;
use App\Office;
use App\OfficeGroup;
use App\OfficePayment;
use App\Order;
use App\Page;
use App\Policies\AccessPolicy;
use App\Policies\AccessSupplierPolicy;
use App\Policies\AccountPolicy;
use App\Policies\AdsetPolicy;
use App\Policies\AffiliatePolicy;
use App\Policies\BinomPolicy;
use App\Policies\BranchPolicy;
use App\Policies\BundlePolicy;
use App\Policies\CampaignPolicy;
use App\Policies\CommentPolicy;
use App\Policies\DepositPolicy;
use App\Policies\DistributionRulePolicy;
use App\Policies\DomainsPolicy;
use App\Policies\FacebookAppPolicy;
use App\Policies\GroupPolicy;
use App\Policies\LeadDestinationDriverPolicy;
use App\Policies\LeadOrderAssignmentPolicy;
use App\Policies\LeadPaymentConditionPolicy;
use App\Policies\LeadPolicy;
use App\Policies\LeadsOrderPolicy;
use App\Policies\LeadsOrderRoutePolicy;
use App\Policies\ManagerPolicy;
use App\Policies\ManualAccountPolicy;
use App\Policies\ManualAppPolicy;
use App\Policies\ManualBundlePolicy;
use App\Policies\ManualCampaignPolicy;
use App\Policies\ManualCreditCardPolicy;
use App\Policies\ManualGroupPolicy;
use App\Policies\ManualInsightPolicy;
use App\Policies\ManualPourPolicy;
use App\Policies\ManualSupplierPolicy;
use App\Policies\ManualTrafficSourcePolicy;
use App\Policies\OfferPolicy;
use App\Policies\OfficeGroupPolicy;
use App\Policies\OfficePaymentPolicy;
use App\Policies\OfficePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProfileLogPolicy;
use App\Policies\ProfilePagePolicy;
use App\Policies\ProfilePolicy;
use App\Policies\ProxyPolicy;
use App\Policies\ResellBatchPolicy;
use App\Policies\ResultPolicy;
use App\Policies\SmsCampaignPolicy;
use App\Policies\SmsMessagePolicy;
use App\Policies\StatusConfigPolicy;
use App\Policies\TagPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TelegramChannelPolicy;
use App\Policies\TenantPolicy;
use App\Policies\TrafficSourcePolicy;
use App\Policies\UnityAppPolicy;
use App\Policies\UnityCampaignPolicy;
use App\Policies\UnityInsightPolicy;
use App\Policies\UnityOrganizationPolicy;
use App\Policies\UsersPolicy;
use App\ProfileLog;
use App\Proxy;
use App\ResellBatch;
use App\Result;
use App\SmsCampaign;
use App\SmsMessage;
use App\StatusConfig;
use App\Tag;
use App\Team;
use App\TelegramChannel;
use App\TrafficSource;
use App\UnityApp;
use App\UnityCampaign;
use App\UnityInsight;
use App\UnityOrganization;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class                   => UsersPolicy::class,
        Lead::class                   => LeadPolicy::class,
        Office::class                 => OfficePolicy::class,
        Order::class                  => OrderPolicy::class,
        Domain::class                 => DomainsPolicy::class,
        Profile::class                => ProfilePolicy::class,
        Deposit::class                => DepositPolicy::class,
        Result::class                 => ResultPolicy::class,
        Account::class                => AccountPolicy::class,
        Access::class                 => AccessPolicy::class,
        Campaign::class               => CampaignPolicy::class,
        SmsCampaign::class            => SmsCampaignPolicy::class,
        SmsMessage::class             => SmsMessagePolicy::class,
        Group::class                  => GroupPolicy::class,
        TelegramChannel::class        => TelegramChannelPolicy::class,
        Bundle::class                 => BundlePolicy::class,
        Tag::class                    => TagPolicy::class,
        LeadsOrder::class             => LeadsOrderPolicy::class,
        LeadOrderRoute::class         => LeadsOrderRoutePolicy::class,
        LeadOrderAssignment::class    => LeadOrderAssignmentPolicy::class,
        AdSet::class                  => AdsetPolicy::class,
        AccessSupplier::class         => AccessSupplierPolicy::class,
        Affiliate::class              => AffiliatePolicy::class,
        ProfileLog::class             => ProfileLogPolicy::class,
        Page::class                   => PagePolicy::class,
        TrafficSource::class          => TrafficSourcePolicy::class,
        Binom::class                  => BinomPolicy::class,
        Team::class                   => TeamPolicy::class,
        FacebookApp::class            => FacebookAppPolicy::class,
        ProfilePage::class            => ProfilePagePolicy::class,
        ManualAccount::class          => ManualAccountPolicy::class,
        ManualBundle::class           => ManualBundlePolicy::class,
        ManualCampaign::class         => ManualCampaignPolicy::class,
        ManualInsight::class          => ManualInsightPolicy::class,
        ManualGroup::class            => ManualGroupPolicy::class,
        ManualPour::class             => ManualPourPolicy::class,
        ManualCreditCard::class       => ManualCreditCardPolicy::class,
        ManualSupplier::class         => ManualSupplierPolicy::class,
        ManualApp::class              => ManualAppPolicy::class,
        Tenant::class                 => TenantPolicy::class,
        ResellBatch::class            => ResellBatchPolicy::class,
        Branch::class                 => BranchPolicy::class,
        Manager::class                => ManagerPolicy::class,
        Comment::class                => CommentPolicy::class,
        Offer::class                  => OfferPolicy::class,
        StatusConfig::class           => StatusConfigPolicy::class,
        OfficePayment::class          => OfficePaymentPolicy::class,
        DistributionRule::class       => DistributionRulePolicy::class,
        Proxy::class                  => ProxyPolicy::class,
        LeadPaymentCondition::class   => LeadPaymentConditionPolicy::class,
        \App\VK\Models\Profile::class => \App\Policies\VK\ProfilePolicy::class,
        OfficeGroup::class            => OfficeGroupPolicy::class,
        UnityOrganization::class      => UnityOrganizationPolicy::class,
        UnityApp::class               => UnityAppPolicy::class,
        UnityCampaign::class          => UnityCampaignPolicy::class,
        UnityInsight::class           => UnityInsightPolicy::class,
        LeadDestinationDriver::class  => LeadDestinationDriverPolicy::class,
        BlackLead::class              => BlackLeadPolicy::class,
        ManualTrafficSource::class    => ManualTrafficSourcePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::enableImplicitGrant();
        Passport::tokensExpireIn(now()->addDays(7));
        Passport::refreshTokensExpireIn(now()->addDays(14));

        Passport::personalAccessClientId(config('passport.personal_access_client.id'));
        Passport::personalAccessClientSecret(config('passport.personal_access_client.secret'));

        $this->setupAdminPrivileges();
    }

    /**
     * Allow users defined as admins,
     * to access all features in the app
     *
     * @return void
     */
    private function setupAdminPrivileges()
    {
        Gate::before(
            function (Authenticatable $user, $ability) {
                // Read only access
                if (auth()->id() === 230 && !in_array($ability, ['viewAny','view'])) {
                    return false;
                }

                if ($user instanceof User && $user->isAdmin()) {
                    return true;
                }
            }
        );
    }
}
