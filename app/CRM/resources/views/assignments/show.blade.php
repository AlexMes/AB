@extends('crm::app')
@section('title', __('crm::lead.lead_details'))
@section('content')
    @if(session()->has('message'))
        <div class="p-4 mx-auto mt-6 bg-blue-100 rounded-md shadow-sm max-w-7xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 ml-3 md:flex md:justify-between">
                    <p class="text-sm leading-5 text-blue-700">
                        {{ session()->pull('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 mx-auto mt-6 bg-red-100 rounded-md max-w-7xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium leading-5 text-red-800">
                        @lang('crm::validation.common_error')
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="mx-auto mt-8 overflow-hidden bg-white shadow sm:rounded-lg max-w-7xl"
        x-data="{
            openTransfer: @if($errors->has('manager_id')) true @else false @endif,
            openDeleting: false,
            openLeftover: @if($errors->has('offer_id')) true @else false @endif,
            openEditButton: false,
        }">
        <div class="flex items-center justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                {{ sprintf('[#%d] %s ',$assignment->lead_id, $assignment->lead->fullname) }}
                @component('crm::components.status-badge', ['status' => $assignment->status]) @endcomponent
            </h3>

            <div>
                <span @click.away="openEditButton = false" class="relative z-0 inline-flex rounded-md shadow-sm">
                    @can('update', $assignment)
                        <a href="{{ route('crm.assignments.edit', $assignment) }}"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white border border-gray-300 rounded-l-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                            <svg class="w-5 h-5 mr-2 -ml-1 text-gray-400" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round"
                                 stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>
                                @lang('crm::common.edit')
                            </span>
                        </a>
                    @endcan
                    <span class="relative block -ml-px">
                        <button
                            type="button"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-r-md hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500"
                            aria-label="Expand"
                            @click.prevent="openEditButton = !openEditButton"
                        >
                            <svg
                            class="w-5 h-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            >
                            <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                            />
                            </svg>
                        </button>
                      <!--
                          Dropdown panel, show/hide based on dropdown state.

                          Entering: "transition ease-out duration-100"
                            From: "transform opacity-0 scale-95"
                            To: "transform opacity-100 scale-100"
                          Leaving: "transition ease-in duration-75"
                            From: "transform opacity-100 scale-100"
                            To: "transform opacity-0 scale-95"
                        -->
                        <div
                            class="absolute right-0 w-56 mt-2 -mr-1 origin-top-right rounded-md shadow-lg"
                            :class="{hidden: !openEditButton}"
                        >
                            <div class="bg-white rounded-md shadow-xs">
                                <div class="py-1">
                                    @can('markAsLeftover', $assignment)
                                        @if(!$assignment->route->offer->isLeftover())
                                            <a href="#" @click.prevent="openLeftover = true"
                                               class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                                <svg class="w-5 h-5 mr-2 -ml-1 text-gray-400"
                                                     viewBox="0 0 512 512">
                                                    <path data-v-002ae016="" fill="currentColor" d="M508.485 168.485l-100.375 100c-4.686 4.686-12.284 4.686-16.97 0l-19.626-19.626c-4.753-4.753-4.675-12.484.173-17.14L422.916 184H12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h410.916l-51.228-47.719c-4.849-4.656-4.927-12.387-.173-17.14l19.626-19.626c4.686-4.686 12.284-4.686 16.97 0l100.375 100c4.685 4.686 4.685 12.284-.001 16.97zm-504.97 192l100.375 100c4.686 4.686 12.284 4.686 16.97 0l19.626-19.626c4.753-4.753 4.675-12.484-.173-17.14L89.084 376H500c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12H89.084l51.228-47.719c4.849-4.656 4.927-12.387.173-17.14l-19.626-19.626c-4.686-4.686-12.284-4.686-16.97 0l-100.375 100c-4.686 4.686-4.686 12.284.001 16.97z" class=""></path>
                                                </svg>
                                                <span>@lang('crm::lead.mark_as_leftover')</span>
                                            </a>
                                        @endif
                                    @endcan

                                    @can('delete', $assignment)
                                        <a href="#" @click.prevent="openDeleting = true"
                                           class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                            <svg class="w-5 h-5 mr-2 -ml-1 text-gray-500" fill="none"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round"
                                                 stroke-width="1" stroke="currentColor" viewBox="0 0 32 32">
                                                <path
                                                    d="M23 27H11c-1.1 0-2-.9-2-2V8h16v17C25 26.1 24.1 27 23 27zM27 8L7 8M14 8V6c0-.6.4-1 1-1h4c.6 0 1 .4 1 1v2M17 23L17 12M21 23L21 12M13 23L13 12"/>
                                            </svg>
                                            <span>@lang('crm::common.delete')</span>
                                        </a>
                                    @endcan

                                    @can('transfer', $assignment)
                                        <a href="#" @click.prevent="openTransfer = true"
                                           class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                            <svg class="w-5 h-5 mr-2 -ml-1 text-gray-400" fill="none"
                                                 stroke-width="1.3" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M21.883 12l-7.527 6.235.644.765 9-7.521-9-7.479-.645.764 7.529 6.236h-21.884v1h21.883z"/>
                                            </svg>
                                            <span>
                                                @lang('crm::common.transfer')
                                            </span>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </span>
                </span>
            </div>
        </div>
        <div class="px-4 py-5 sm:p-0">
            <dl>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        @lang('crm::lead.form_phone')
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-block">{{ $assignment->lead->formatted_phone }}</span>
                        @if (auth('crm')->check() && auth('crm')->user()->hasTenant())
                            <a href="{{ route('crm.assignment.call', $assignment) }}"
                               class="inline-block cursor-pointer">
                                <svg class="w-4 h-4 ml-2 -mb-1 text-gray-500 hover:text-teal-600" fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M17.924 2.617a.997.997 0 00-.215-.322l-.004-.004A.997.997 0 0017 2h-4a1 1 0 100 2h1.586l-3.293 3.293a1 1 0 001.414 1.414L16 5.414V7a1 1 0 102 0V3a.997.997 0 00-.076-.383z"></path>
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </a>
                        @endif
                    </dd>
                </div>
                @if($assignment->hasAltPhone())
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_alt_phone')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="inline-block">{{ $assignment->formatted_alt_phone }}</span>
                            @if (auth('crm')->check() && auth('crm')->user()->hasTenant())
                                <a href="{{ route('crm.assignment.call-alt', $assignment) }}"
                                   class="inline-block cursor-pointer">
                                    <svg class="w-4 h-4 ml-2 -mb-1 text-gray-500 hover:text-teal-600"
                                         fill="currentColor"
                                         viewBox="0 0 20 20">
                                        <path
                                            d="M17.924 2.617a.997.997 0 00-.215-.322l-.004-.004A.997.997 0 0017 2h-4a1 1 0 100 2h1.586l-3.293 3.293a1 1 0 001.414 1.414L16 5.414V7a1 1 0 102 0V3a.997.997 0 00-.076-.383z"></path>
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                </a>
                            @endif
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->email)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_email')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->lead->email }}
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->utm_content && !Str::contains($assignment->lead->offer->name, 'JRD'))
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            Creative
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->lead->utm_content }}
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->user && !Str::contains($assignment->lead->offer->name, 'JRD'))
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            Buyer
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->lead->user->name }} ({{ optional($assignment->lead->user->branch)->name }})
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->user && Str::contains($assignment->lead->offer->name, 'JRD'))
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            Поставщик
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            Chicago
                        </dd>
                    </div>
                @endif
                @if($assignment->callback_at && $assignment->status === 'Перезвон')
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_callback_at')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->callback_at->format('M d, H:i:s') }}
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->ipAddress)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_registration_region')
                        </dt>
                        <dd class="flex justify-between mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            <span>{{ $assignment->lead->ipAddress->country_name }}, {{ $assignment->lead->ipAddress->city. ' ('.$assignment->lead->ipAddress->region.')' }}</span>
                            @if ($assignment->lead->current_time !== null)
                                <span class="text-xs text-gray-600">
                                    Time(alt): {{ $assignment->lead->current_time->format('H:i:s')  }}
                                </span>
                            @else
                                <span class="text-xs text-gray-600">
                                @lang('crm::lead.time'): {{ now()->setTimezone($assignment->lead->ipAddress->timezone)->format('H:i:s') }}
                                </span>
                            @endif
                        </dd>
                    </div>
                @endif
                @if($assignment->reject_reason)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_reject_reason')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{$assignment->reject_reason }}
                        </dd>
                    </div>
                @endif
                @if($assignment->deposit_sum)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_deposit_sum')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{sprintf("%s \$",$assignment->deposit_sum) }}
                        </dd>
                    </div>
                @endif
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        @lang('crm::lead.form_gender')
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $assignment->gender }}
                    </dd>
                </div>
                @if($assignment->age)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_age')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{$assignment->age ?? 'N/A' }}
                        </dd>
                    </div>
                @endif
                @if($assignment->profession)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_profession')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ Str::ucfirst($assignment->profession)  }}
                        </dd>
                    </div>
                @endif
                @if($assignment->timezone)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.timezone')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->timezone  }}
                        </dd>
                    </div>
                @endif
                @if($assignment->comment)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.comment')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2 text-wrap">
                            {{ $assignment->comment }}
                        </dd>
                    </div>
                @endif
                @if(auth('web')->check())
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_office')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ optional(optional($assignment->getManager())->office)->name  }}
                        </dd>
                    </div>
                @endif
                @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.manager')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->getManager()->name ?? '-' }}
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->domain && !Str::contains($assignment->lead->offer->name, 'JRD'))
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_landing')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            <a target="_blank" class="hover:text-teal-600"
                               href="https://{{$assignment->lead->domain}}">{{ $assignment->lead->domain }}</a>
                        </dd>
                    </div>
                @endif
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        @lang('crm::lead.form_registered_at')
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $assignment->registered_at->format('M d, H:i:s') }}
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        @lang('crm::lead.form_assigned_at')
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $assignment->created_at->format('M d, H:i:s') }}  <span class="ml-4 text-sm text-gray-600">({{ $assignment->created_at->diffForHumans($assignment->registered_at) . __('crm::lead.assigned_diff_registered') }})</span>
                    </dd>
                </div>
                @if($assignment->labels->isNotEmpty())
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_labels')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->labels->implode('name',', ') }}
                        </dd>
                    </div>
                @endif
                @if($assignment->lead->hasPoll())
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            Ответы на опрос:
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            @foreach($assignment->lead->pollResults() as $result)
                              <div class="my-3">
                                <p class="font-bold">{{ $result->getQuestion() }}</p>
                                <p class="italic">{{ $result->getAnswer() }}</p>
                              </div>
                            @endforeach
                        </dd>
                    </div>
                @endif
                @if((auth('web')->check() || auth('crm')->user()->isOfficeHead()) && $assignment->called_at !== null)
                    <div
                        class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                        <dt class="text-sm font-medium leading-5 text-gray-500">
                            @lang('crm::lead.form_called_at')
                        </dt>
                        <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $assignment->called_at->format('M d, H:i:s') }} <span class="ml-4 text-sm text-gray-600">({{ $assignment->called_at->diffForHumans($assignment->created_at) . __('crm::lead.called_diff_assigned') }})</span>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        @can('transfer', $assignment)
            <div x-show="openTransfer" class="fixed inset-x-0 bottom-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                <div x-show="openTransfer" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div x-show="openTransfer" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                        <button @click="openTransfer = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form method="post" action="{{ route('crm.assignment.transfer', $assignment) }}">
                        @csrf
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-headline">
                                    @lang('crm::lead.lead_transfer')
                                </h3>
                                <div class="mt-2">
                                    <div class="w-full mr-4">
                                        <label for="targetManager" class="block text-sm font-medium leading-5 text-gray-700">
                                            @lang('crm::lead.select_manager')
                                        </label>
                                        <div class="mt-1 rounded-md shadow-sm">
                                            <select id="targetManager"
                                                    name="manager_id"
                                                    class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                                @foreach($managers as $manager)
                                                    @if($manager->id != $assignment->route->manager_id)
                                                        <option
                                                            value="{{$manager->id}}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('manager_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red sm:text-sm sm:leading-5">
                                @lang('crm::common.transfer')
                              </button>
                            </span>
                            <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              <button @click="openTransfer = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                @lang('crm::common.cancel')
                              </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        @can('delete', $assignment)
            <div x-show="openDeleting" class="fixed inset-x-0 bottom-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                <div x-show="openDeleting" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div x-show="openDeleting" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                        <button @click="openDeleting = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form method="post" action="{{ route('crm.assignments.destroy', $assignment) }}">
                        @csrf
                        @method('DELETE')
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-headline">
                                    @lang('crm::lead.lead_delete_title')
                                </h3>
                                <div class="mt-2">
                                    <div class="w-full mr-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red sm:text-sm sm:leading-5">
                                @lang('crm::common.confirm')
                              </button>
                            </span>
                            <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              <button @click="openDeleting = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                @lang('crm::common.cancel')
                              </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        @can('markAsLeftover', $assignment)
            @if(!$assignment->route->offer->isLeftover())
                <div x-show="openLeftover" class="fixed inset-x-0 bottom-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                    <div x-show="openLeftover" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <div x-show="openLeftover" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                            <button @click="openLeftover = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <form method="post" action="{{ route('crm.assignments.mark-leftover', $assignment) }}">
                            @csrf
                            <div class="sm:flex sm:items-start">
                                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-headline">
                                        @lang('crm::lead.leftover_title')
                                    </h3>
                                    <div class="mt-2">
                                        <div class="w-full mr-4">
                                            @error('offer_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                  <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red sm:text-sm sm:leading-5">
                                    @lang('crm::common.confirm')
                                  </button>
                                </span>
                                <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                  <button @click="openLeftover = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                    @lang('crm::common.cancel')
                                  </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endcan
    </div>

    @can('viewAny', \App\CRM\Callback::class)
            <div class="mx-auto mt-8 overflow-x-auto max-w-7xl">
                @if($assignment->scheduledCallbacks->count() > 0)
                    <div
                        class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                        <div class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded-t shadow">
                            <p class="text-lg font-medium">@lang('crm::callback.title')</p>
                        </div>
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="w-1/12 px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                    @lang('crm::callback.id')
                                </th>
                                <th class="w-1/4 px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                    @lang('crm::callback.call_at')
                                </th>
                                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                    @lang('crm::callback.called_at')
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white">
                            @foreach($assignment->scheduledCallbacks as $callback)
                                <tr class="hover:bg-gray-100">
                                    <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                        {{ $callback->id }}
                                    </td>
                                    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                        {{ $callback->call_at->format('M d, H:i:s') }}
                                    </td>
                                    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                        @if($callback->called_at)
                                            <span class="font-medium text-gray-700">
                                                {{ $callback->called_at->diffForHumans($callback->call_at) . __('crm::callback.called_diff') }}
                                            </span>
                                            <div>
                                                {{ $callback->called_at->format('M d, H:i:s') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow">
                        <p>@lang('crm::callback.no_callbacks_found')</p>
                    </div>
                @endif
            </div>
    @endif
@endsection
