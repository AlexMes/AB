<?php

namespace App\CRM\Http\Controllers;

use App\Affiliate;
use App\Branch;
use App\CRM\Http\Requests\UpdateAssignment;
use App\CRM\Label;
use App\CRM\LeadOrderAssignment;
use App\CRM\Queries\ManagerAssignments;
use App\CRM\Status;
use App\CRM\Timezone;
use App\Deposit;
use App\Http\Controllers\Controller;
use App\LeadDestination;
use App\LeadDestinationDriver;
use App\Manager;
use App\Office;
use App\OfficeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Assignments extends Controller
{
    /**
     * @var \App\CRM\Queries\ManagerAssignments
     */
    protected ManagerAssignments $assignments;

    /**
     * Assignments constructor.
     *
     * @param \App\CRM\Queries\ManagerAssignments $assignments
     */
    public function __construct(ManagerAssignments $assignments)
    {
        $this->authorizeResource(LeadOrderAssignment::class, 'assignment');
        $this->assignments = $assignments;
    }

    /**
     * Index assignments
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        $offices = Office::visible()
            ->where(fn ($query) => $query->whereNull('destination_id')
            ->orWhereIn('destination_id', LeadDestination::whereIn('driver', [LeadDestinationDriver::INVESTEX, LeadDestinationDriver::HOTLEADS])->pluck('id')))
            ->get();

        return view('crm::assignments.index', [
            'offices'     => $offices,
            'statuses'    => Status::all(),
            'managers'    => Manager::visible()
                ->when(
                    $request->get('office'),
                    fn ($query) => $query->whereIn('office_id', Arr::wrap($request->get('office')))
                )
                ->get(['id', 'name']),
            'offers'      => $request->user()->offers(),
            'assignments' => $this->assignments
                ->search($request->get('search'))
                ->forOffice($request->get('office'))
                ->forManager($request->get('manager'))
                ->forOffer($request->get('offer'))
                ->havingStatus($request->get('status'))
                ->forPeriod($request->get('period'))
                ->forRegistrationPeriod($request->get('registration_period'))
                ->forLabels($request->get('label'))
                ->forTimezone($request->get('timezone'))
                ->forGender($request->get('gender'))
                ->forBranch($request->get('branch'))
                ->forOfficeGroup($request->get('office_group'))
                ->forAffiliate($request->get('affiliate'))
                ->forSmoothLo($request->get('smooth_lo', 'without_delayed'))
                ->paginate(),
            'labels'           => Label::all(),
            'timezones'        => Timezone::all(),
            'transferManagers' => Manager::whereOfficeId(
                $request->get('office', $offices->count() === 1 ? $offices->first()->id : null)
            )->get(['id', 'name']),
            'branches'     => Branch::allowed()->get(['id', 'name'])->prepend(new Branch(['id' => 0, 'name' => 'Unknown'])),
            'officeGroups' => OfficeGroup::visible()->get(['id', 'name']),
            'affiliates'   => Affiliate::visible()->get(['id', 'name']),
        ]);
    }

    /**
     * Show single assignment page
     *
     * @param \App\CRM\LeadOrderAssignment $assignment
     *
     * @return \Illuminate\View\View
     */
    public function show(LeadOrderAssignment $assignment, Request $request)
    {
        $assignment->addEvent(LeadOrderAssignment::READ, [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('crm::assignments.show')->with([
            'assignment' => $assignment->load(['scheduledCallbacks','lead','lead.user','lead.user.branch']),
            'managers'   => $assignment->route->order->office->managers,
        ]);
    }

    /**
     * Display editing form for assignment
     *
     * @param \App\CRM\LeadOrderAssignment $assignment
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(LeadOrderAssignment $assignment)
    {
        return view('crm::assignments.form')->with([
            'assignment' => $assignment,
            'labels'     => Label::all()
        ]);
    }

    /**
     * Update assignment details
     *
     * @param \App\CRM\LeadOrderAssignment            $assignment
     * @param \App\CRM\Http\Requests\UpdateAssignment $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LeadOrderAssignment $assignment, UpdateAssignment $request)
    {
        DB::transaction(function () use ($assignment, $request) {
            if (auth('web')->check() && auth('web')->user()->isSupport()) {
                $assignment->update(Arr::only(
                    $request->validated(),
                    ['status', 'reject_reason', 'deposit_sum']
                ));
            } else {
                $assignment->update(Arr::except($request->validated(), ['callback_at']));
                $assignment->labels()->sync($request->labels);
            }

            if ($request->input('status') === 'Перезвон' && $request->input('callback_at') !== null) {
                $assignment->actualCallback()
                    ->fill(['call_at' => $request->input('callback_at')])
                    ->save();
            }

            if ($request->get('status') === 'Депозит' && !$assignment->hasDeposit()) {
                Deposit::createFromAssignment($assignment->refresh());
            }
        });

        return redirect()->route('crm.assignments.show', $assignment);
    }

    /**
     * @param LeadOrderAssignment $assignment
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(LeadOrderAssignment $assignment)
    {
        $assignment->remove();

        return redirect()->route('crm.assignments.index')->with(['message' => trans('crm::lead.delete_success')]);
    }
}
