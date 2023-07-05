<?php

namespace App\Providers;

use App\Events\Access\Saved as AccessSaved;
use App\Events\AffiliateCreating;
use App\Events\AssignmentDeleted;
use App\Events\AssignmentSaved;
use App\Events\AssignmentTransferred;
use App\Events\AssignmentUpdating;
use App\Events\ClickCreated;
use App\Events\Deposits\Created as DepositCreated;
use App\Events\Deposits\Updated as DepositUpdated;
use App\Events\Domains\Saved as DomainSaved;
use App\Events\Domains\Saving as DomainSaving;
use App\Events\FirewallRuleCreated;
use App\Events\FirewallRuleDeleting;
use App\Events\Lead\Created as LeadCreated;
use App\Events\Lead\Updated as LeadUpdated;
use App\Events\LeadAssigned;
use App\Events\LeadAssigning;
use App\Events\LeadDestinationUpdated;
use App\Events\LeadPulled;
use App\Events\ManagerCreated;
use App\Events\OfferAllowed;
use App\Events\Offers\Created as OfferCreated;
use App\Events\Offers\Creating as OfferCreating;
use App\Events\Offices\Created as OfficeCreated;
use App\Events\Orders\Completed as OrderCompleted;
use App\Events\Orders\Created as OrderCreated;
use App\Events\Orders\Updated as OrderUpdated;
use App\Events\Sms\SmsMessageCreated;
use App\Events\Tenants\Creating as TenantCreating;
use App\Facebook\Events\Campaigns\CampaignCreated;
use App\Facebook\Events\Campaigns\CampaignSaving;
use App\Facebook\Listeners\AttachMissingLeads;
use App\Facebook\Listeners\AttachMissingStatistics;
use App\Facebook\Listeners\SetCampaignUtm;
use App\Listeners\Access\LoadNameFromFacebook;
use App\Listeners\AllowOfferToBuyers;
use App\Listeners\AllowOfferToHeads;
use App\Listeners\AllowOfferToSupport;
use App\Listeners\AttachStatusesToOffice;
use App\Listeners\CheckAssignmentRegisteredAt;
use App\Listeners\CheckForDuplicationOnFrx;
use App\Listeners\ConfirmAssignment;
use App\Listeners\CopyDataToColdBaseOffer;
use App\Listeners\CopyDataToDuplicateOffer;
use App\Listeners\CopyDataToLeftoverOffer;
use App\Listeners\CreateDepositIfNotCreated;
use App\Listeners\DecrementDelayedAssignments;
use App\Listeners\Deposits\CheckResult;
use App\Listeners\Deposits\FindDepositLead;
use App\Listeners\Deposits\NotifyDestination;
use App\Listeners\Deposits\UpdateResult;
use App\Listeners\DispatchSpreadSheetCreation;
use App\Listeners\DomainReporting;
use App\Listeners\FinishResellBatch;
use App\Listeners\FlushUserFirewallCache;
use App\Listeners\GenerateAffiliateApiToken;
use App\Listeners\GenerateTenantApiToken;
use App\Listeners\IncrementDelayedAssignments;
use App\Listeners\IncrementOfficePaymentAssigned;
use App\Listeners\Leads\RunDetectors;
use App\Listeners\Leads\RunSmsCampaign;
use App\Listeners\Leads\ValidatePhoneNumber;
use App\Listeners\LogTransferringAssignment;
use App\Listeners\MarkAssignmentOnResellPivot;
use App\Listeners\PerformPhoneNumberLookup;
use App\Listeners\PostbackTridentDeposit;
use App\Listeners\PostbackTridentLead;
use App\Listeners\PullIpInfo;
use App\Listeners\PullLeadIpInfo;
use App\Listeners\ReportFailedLoginAttempt;
use App\Listeners\RunOfferDetection;
use App\Listeners\SendPostbackLeadToBinom;
use App\Listeners\SendPostbackToAffiliate;
use App\Listeners\SendPostbackToBinom;
use App\Listeners\SetDepositBenefit;
use App\Listeners\SetDestinationAssignmentStatus;
use App\Listeners\SetDestinationId;
use App\Listeners\SetDomainFailedAt;
use App\Listeners\SetLeadBenefit;
use App\Listeners\SetLeadBenefitByCondition;
use App\Listeners\SetLeadDataToAssignment;
use App\Listeners\SetOfferUUID;
use App\Listeners\SMS\ScheduleCheckBalance;
use App\Listeners\SMS\ScheduleStatusUpdate;
use App\Listeners\SMS\UpdateCostService;
use App\Listeners\SyncOrder;
use App\Listeners\UnsetConfirmedOnAssignment;
use App\Listeners\UpdateReceivalTimestamp;
use App\Listeners\UpdateResultsRecords;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Registered as UserRegistered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserRegistered::class => [
            SendEmailVerificationNotification::class,
        ],
        CampaignCreated::class => [
            AttachMissingLeads::class,
            AttachMissingStatistics::class,
        ],
        DomainSaved::class => [
            SyncOrder::class
        ],
        DomainSaving::class => [
            SetDomainFailedAt::class,
        ],
        AccessSaved::class => [
            LoadNameFromFacebook::class
        ],
        OrderCreated::class => [
            \App\Notifications\Orders\Created::class
        ],
        OrderUpdated::class => [
            \App\Notifications\Orders\Updated::class,
        ],
        OrderCompleted::class => [
            \App\Notifications\Orders\Completed::class,
        ],
        DepositCreated::class => [
            FindDepositLead::class,
            UpdateResult::class,
            SetDepositBenefit::class,
            SendPostbackToBinom::class,
            PostbackTridentDeposit::class,
            NotifyDestination::class,
        ],
        DepositUpdated::class => [
            CheckResult::class,
        ],
        LeadCreated::class => [
            PerformPhoneNumberLookup::class,
            RunDetectors::class,
            PullIpInfo::class,
            ValidatePhoneNumber::class,
            RunSMSCampaign::class,
            PostbackTridentLead::class,
            DomainReporting::class,
        ],
        LeadUpdated::class => [
            PullLeadIpInfo::class,
        ],
        SmsMessageCreated::class => [
            ScheduleStatusUpdate::class,
            UpdateCostService::class,
            ScheduleCheckBalance::class,
        ],
        CampaignSaving::class => [
            SetCampaignUtm::class,
            RunOfferDetection::class,
        ],
        LeadAssigned::class => [
            UpdateReceivalTimestamp::class,
            UpdateResultsRecords::class,
            CheckForDuplicationOnFrx::class,
            SetDestinationId::class,
            SetLeadDataToAssignment::class,
            IncrementOfficePaymentAssigned::class,
            MarkAssignmentOnResellPivot::class,
            IncrementDelayedAssignments::class,
        ],
        LeadAssigning::class => [
            CheckAssignmentRegisteredAt::class,
        ],
        AssignmentSaved::class => [
            SetLeadBenefitByCondition::class,
            SendPostbackToAffiliate::class,
            FinishResellBatch::class,
        ],
        AssignmentUpdating::class => [
            UnsetConfirmedOnAssignment::class,
        ],
        AssignmentDeleted::class => [
            DecrementDelayedAssignments::class,
        ],
        ManagerCreated::class => [
            //DispatchSpreadSheetCreation::class,
        ],
        LeadPulled::class => [
            CreateDepositIfNotCreated::class,
            ConfirmAssignment::class,
            SetLeadBenefit::class,
        ],
        FirewallRuleCreated::class => [
            FlushUserFirewallCache::class
        ],
        FirewallRuleDeleting::class => [
            FlushUserFirewallCache::class
        ],
        AssignmentTransferred::class => [
            LogTransferringAssignment::class,
        ],
        AffiliateCreating::class => [
            GenerateAffiliateApiToken::class
        ],
        ClickCreated::class => [
            SendPostbackLeadToBinom::class,
        ],
        OfferAllowed::class => [
            AllowOfferToHeads::class,
            AllowOfferToSupport::class,
            AllowOfferToBuyers::class,
        ],
        OfficeCreated::class => [
            AttachStatusesToOffice::class,
        ],
        TenantCreating::class => [
            GenerateTenantApiToken::class,
        ],
        OfferCreated::class => [
            CopyDataToLeftoverOffer::class,
            CopyDataToDuplicateOffer::class,
            CopyDataToColdBaseOffer::class,
        ],
        OfferCreating::class => [
            SetOfferUUID::class,
        ],
        Failed::class => [
            ReportFailedLoginAttempt::class
        ],
        LeadDestinationUpdated::class => [
            SetDestinationAssignmentStatus::class,
        ],
    ];
}
