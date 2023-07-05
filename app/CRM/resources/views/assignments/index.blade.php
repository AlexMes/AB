@extends('crm::app')
@section('title', __('crm::common.dashboard'))
@section('content')
    <div x-data="AssignmentsIndex()" x-init="init()" class="mt-8">
        <div class="flex flex-col">
            @if(session()->has('message'))
                <div class="p-4 mb-4 bg-blue-100 rounded-md shadow-sm">
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
                <div class="p-4 mb-4 bg-red-100 rounded-md shadow-sm">
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
            <form action="{{ route('crm.assignments.index') }}" class="flex flex-col flex-no-wrap items-center mb-6 md:flex-wrap md:flex-row">
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="date" class="block text-sm font-medium leading-5 text-gray-700">
                        @lang('crm::filter.period')
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input id="date"
                               x-model="filters.period"
                               x-ref="period"
                               name="period"
                               value="{{ request('period') }}"
                               class="block w-full form-input sm:text-sm sm:leading-5" />
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="registration_period" class="block text-sm font-medium leading-5 text-gray-700">
                        Период регистрации
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input id="registration_period"
                               x-model="filters.registration_period"
                               x-ref="registration_period"
                               name="registration_period"
                               value="{{ request('registration_period') }}"
                               class="block w-full form-input sm:text-sm sm:leading-5" />
                    </div>
                </div>
                @if($offices->count() > 1)
                    <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                        <label for="office" class="block text-sm font-medium leading-5 text-gray-700">
                            @lang('crm::filter.office')
                        </label>
                        <div class="mt-1 rounded-md shadow-sm">
                            <select id="office"
                                    x-model="filters.office"
                                    x-ref="office"
                                    onchange="this.form.submit()"
                                    name="office"
                                    class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                <option value=''>@lang('crm::common.all')</option>
                                @foreach($offices as $office)
                                    <option
                                        value="{{$office->id}}" {{ request('office') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                    <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                        <label for="status" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                            @lang('crm::filter.manager')
                        </label>
                        <div class="mt-1">
                            @component('crm::components.multiselect', [
                                    'id' => 'manager',
                                    'options' => $managers,
                                    'trackBy' => 'id',
                                    'label' => 'name',
                                ]) @endcomponent
                        </div>
                    </div>
                @endif
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="status" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.status')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'status',
                                'options' => $statuses,
                                'trackBy' => 'name',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="offer" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.landing')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'offer',
                                'options' => $offers,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="label" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.label')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'label',
                                'options' => $labels,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="timezone" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.timezone')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'timezone',
                                'options' => $timezones,
                                'trackBy' => 'name',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="gender" class="block text-sm font-medium leading-5 text-gray-700">
                        @lang('crm::filter.gender')
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <select id="gender"
                                x-model="filters.gender"
                                x-ref="gender"
                                name="gender"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option value=''>@lang('crm::common.all')</option>
                            <option value="1" {{ request('gender') == 1 ? 'selected' : '' }}>Мужской</option>
                            <option value="2" {{ request('gender') == 2 ? 'selected' : '' }}>Женский</option>
                        </select>
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="offer" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.supplier')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'branch',
                                'options' => $branches,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="office_group" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.office_group')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'office_group',
                                'options' => $officeGroups,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="affiliate" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        @lang('crm::filter.affiliate')
                    </label>
                    <div class="mt-1">
                        @component('crm::components.multiselect', [
                                'id' => 'affiliate',
                                'options' => $affiliates,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                @if(auth('web')->check())
                    <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                        <label for="smooth_lo" class="block text-sm font-medium leading-5 text-gray-700">
                            @lang('crm::filter.smooth_lo')
                        </label>
                        <div class="mt-1 rounded-md shadow-sm">
                            <select id="smooth_lo"
                                    x-model="filters.smooth_lo"
                                    x-ref="smooth_lo"
                                    name="smooth_lo"
                                    class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                <option value="all" {{ request('smooth_lo') == 'all' ? 'selected' : '' }}>@lang('crm::common.all')</option>
                                <option value="without_delayed" {{ request('smooth_lo', 'without_delayed') == 'without_delayed' ? 'selected' : '' }}>Без запланированных но не выданных</option>
                            </select>
                        </div>
                    </div>
                @endif
                <div class="flex flex-col w-full pr-4 ml-auto md:w-auto md:flex-row sm:col-span-3">
                    <div class="mt-5 sm:col-span-3">
                        <span @click.away="openFilterButton = false" class="relative z-10 inline-flex rounded-md shadow-sm">
                            <button type="submit"
                                   class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 bg-teal-600 border border-white rounded-l-md text-gray-50 hover:text-white hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                        clip-rule="evenodd" fill-rule="evenodd"></path>
                                </svg>
                                <span>
                                    @lang('crm::filter.filter')
                                </span>
                            </button>
                            <span class="relative block -ml-px">
                                <button
                                    type="button"
                                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 transition duration-150 ease-in-out bg-teal-600 border border-white rounded-r-md text-gray-50 focus:z-10 hover:text-white hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700"
                                    aria-label="Expand"
                                    @click.prevent="openFilterButton = !openFilterButton"
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
                                    :class="{hidden: !openFilterButton}"
                                >
                                    <div class="bg-teal-600 rounded-md shadow-xs">
                                        <div class="py-1">
                                            @can('export', \App\CRM\LeadOrderAssignment::class)
                                                <button @click.prevent="exportLeads" :disabled="isBusy"
                                                   class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 bg-teal-600 text-gray-50 hover:bg-teal-500 hover:text-white focus:outline-none focus:bg-teal-500 focus:text-white">
                                                    <template x-if="isBusy">
                                                        <span>@lang('crm::common.loading')</span>
                                                    </template>
                                                    <template x-if="!isBusy">
                                                        <span class="inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-2 -ml-1 text-white"
                                                                 fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                            </svg>
                                                            <span>@lang('crm::common.download')</span>
                                                        </span>
                                                    </template>
                                                  </button>
                                            @endif

                                            @can('massTransfer', \App\CRM\LeadOrderAssignment::class)
                                                @if(request('office', $offices->count() === 1 ? $offices->first()->id : null))
                                                    <a href="#" @click.prevent="transferOpened = true; openFilterButton = false" x-show="transferAssignments.length>0"
                                                       class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 bg-teal-600 text-gray-50 hover:bg-teal-500 hover:text-white focus:outline-none focus:bg-teal-500 focus:text-white">
                                                        <svg class="w-4 h-4 mr-2 -ml-1 text-white" fill="none"
                                                             stroke-width="1.3" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path d="M21.883 12l-7.527 6.235.644.765 9-7.521-9-7.479-.645.764 7.529 6.236h-21.884v1h21.883z"/>
                                                        </svg>
                                                        <span>
                                                            @lang('crm::common.transfer')
                                                        </span>
                                                    </a>
                                                @endif
                                            @endcan

                                            @can('massMarkAsLeftover', \App\CRM\LeadOrderAssignment::class)
                                                <a href="#" @click.prevent="markingLeftoverOpened = true; openFilterButton = false" x-show="transferAssignments.length>0"
                                                   class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 bg-teal-600 text-gray-50 hover:bg-teal-500 hover:text-white focus:outline-none focus:bg-teal-500 focus:text-white">
                                                    <svg class="w-4 h-4 mr-2 -ml-1 text-white"
                                                         viewBox="0 0 512 512">
                                                        <path data-v-002ae016="" fill="currentColor" d="M508.485 168.485l-100.375 100c-4.686 4.686-12.284 4.686-16.97 0l-19.626-19.626c-4.753-4.753-4.675-12.484.173-17.14L422.916 184H12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h410.916l-51.228-47.719c-4.849-4.656-4.927-12.387-.173-17.14l19.626-19.626c4.686-4.686 12.284-4.686 16.97 0l100.375 100c4.685 4.686 4.685 12.284-.001 16.97zm-504.97 192l100.375 100c4.686 4.686 12.284 4.686 16.97 0l19.626-19.626c4.753-4.753 4.675-12.484-.173-17.14L89.084 376H500c6.627 0 12-5.373 12-12v-24c0-6.627-5.373-12-12-12H89.084l51.228-47.719c4.849-4.656 4.927-12.387.173-17.14l-19.626-19.626c-4.686-4.686-12.284-4.686-16.97 0l-100.375 100c-4.686 4.686-4.686 12.284.001 16.97z" class=""></path>
                                                    </svg>
                                                    <span>@lang('crm::lead.mark_as_leftover')</span>
                                                </a>
                                            @endcan

                                            @can('massDelete', \App\CRM\LeadOrderAssignment::class)
                                                <a href="#" @click.prevent="deletingOpened = true; openFilterButton = false" x-show="transferAssignments.length>0"
                                                   class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 bg-teal-600 text-gray-50 hover:bg-teal-500 hover:text-white focus:outline-none focus:bg-teal-500 focus:text-white">
                                                    <svg class="w-4 h-4 mr-2 -ml-1 text-white" fill="none"
                                                         stroke-linecap="round"
                                                         stroke-linejoin="round"
                                                         stroke-width="1" stroke="currentColor" viewBox="0 0 32 32">
                                                        <path
                                                            d="M23 27H11c-1.1 0-2-.9-2-2V8h16v17C25 26.1 24.1 27 23 27zM27 8L7 8M14 8V6c0-.6.4-1 1-1h4c.6 0 1 .4 1 1v2M17 23L17 12M21 23L21 12M13 23L13 12"/>
                                                    </svg>
                                                    <span>@lang('crm::common.delete')</span>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </span>
                    </div>
                </div>
            </form>
            <div class="relative flex pb-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                @if($assignments->count() > 0)
                    <div class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto sm:px-6 lg:px-8 max-w-8xl">
                        <div
                            class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                            <table class="min-w-full">
                                <thead>
                                <tr>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.id')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.offer')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.name')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.phone')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.time')
                                    </th>
                                    @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                            @lang('crm::lead.manager')
                                        </th>
                                    @endif
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.status')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.registered_at')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.assigned_at')
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        @lang('crm::lead.comment')
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white">
                                @foreach($assignments as $assignment)
                                    <tr class="cursor-pointer hover:bg-gray-100"
                                        onclick="window.open('{{ route('crm.assignments.show', $assignment->id) }}', '_blank')">
                                        <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            @canany(['massTransfer', 'massMarkAsLeftover', 'massDelete'], $assignment)
                                                <input type="checkbox"
                                                       x-model="transferAssignments"
                                                       name="assignments[]"
                                                       value="{{ $assignment->id }}"
                                                       @if(in_array($assignment->id, old('assignments', []))) checked @endif
                                                       @click.stop="" />
                                            @endcanany
                                            {{ $assignment->id }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $assignment->name }}
                                        </td>
                                        <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                            {{ \Illuminate\Support\Str::limit($assignment->fullname, 40) }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                            {{ $assignment->phone }}
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
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                            @if ($assignment->actual_time !== null)
                                                {{ $assignment->actual_time->format('H:i') }}
                                                @php $mskDiff = ($assignment->actual_time->utcOffset() - now('Europe/Moscow')->utcOffset()) / 60 @endphp
                                                <span>(мск{{ ($mskDiff >= 0 ? '+' : '') . $mskDiff }})</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                                            <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                                {{ $assignment->manager ?? '-'}}
                                            </td>
                                        @endif
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            @component('crm::components.status-badge', ['status' => $assignment->status]) @endcomponent
                                            @if($assignment->status === 'Перезвон')
                                                <div class="-mb-5 text-xs">
                                                    {{ Carbon\Carbon::parse($assignment->callback_at)->format('M d, H:i:s') ?? 'nan'}}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                           @if ($assignment->registered_at)
                                                {{ $assignment->registered_at->format('M d, H:i:s') ?? '-' }}
                                           @endif
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $assignment->created_at->format('M d, H:i:s') ?? '-' }}
                                        </td>
                                        <td x-data="{show: false}" class="max-w-4xl px-5 py-5 text-sm leading-5 text-gray-500 whitespace-normal border-b border-gray-200">
                                            <span x-show="!show">{{ \Illuminate\Support\Str::limit($assignment->comment, 60) }}</span>
                                            @if(strlen($assignment->comment) > 60)
                                                <span x-show="show">{{ $assignment->comment }}</span>
                                                <div>
                                                    <a href="#" @click.stop="show = !show">Подробнеее</a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                                <div class="hidden sm:block">
                                    <p class="text-sm leading-5 text-gray-700">
                                        @lang('crm::pagination.from')
                                        <span class="font-medium">{{ $assignments->firstItem() }}</span>
                                        @lang('crm::pagination.to')
                                        <span class="font-medium">{{ $assignments->lastItem() }}</span>
                                        @lang('crm::pagination.total')
                                        <span class="font-medium">{{ $assignments->total() }}</span>
                                        @choice('crm::pagination.leads', $assignments->total())
                                    </p>
                                </div>
                                <div class="flex justify-between flex-1 sm:justify-end">
                                    @if($assignments->previousPageUrl())
                                        <a href="{{ $assignments->appends(request(['offer','status','manager','search','office','period', 'timezone','branch']))->previousPageUrl() }}"
                                           class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                            @lang('crm::pagination.previous')
                                        </a>
                                    @endif
                                    @if($assignments->nextPageUrl())
                                        <a href="{{ $assignments->appends(request(['offer','status','manager','search','office','period', 'timezone','branch']))->nextPageUrl() }}"
                                           class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                            @lang('crm::pagination.next')
                                        </a>
                                    @endif
                                </div>
                            </nav>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow">
                        <p>@lang('crm::lead.no_leads_found')</p>
                    </div>
                @endif
            </div>
        </div>

        @can('massTransfer', \App\CRM\LeadOrderAssignment::class)
            @if(request('office', $offices->count() === 1 ? $offices->first()->id : null))
                <div x-show="transferOpened" class="fixed inset-x-0 bottom-0 z-10 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                    <div x-show="transferOpened" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <div x-show="transferOpened" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                            <button @click="transferOpened = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <form method="post" action="{{ route('crm.assignments.mass-transfer') }}">
                            @csrf
                            <div class="sm:flex sm:items-start">
                                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-headline">
                                        @lang('crm::lead.leads_transfer')
                                    </h3>
                                    <template x-for="id in transferAssignments" :key="id">
                                        <input type="hidden" name="assignments[]" :value="id" />
                                    </template>
                                    <div class="mt-2">
                                        <div class="w-full mr-4">
                                            <label for="transferManagerId" class="block text-sm font-medium leading-5 text-gray-700">
                                                @lang('crm::lead.select_manager')
                                            </label>
                                            <div class="mt-1 rounded-md shadow-sm">
                                                <select id="transferManagerId"
                                                        name="manager_id"
                                                        class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                                    @foreach($transferManagers as $manager)
                                                        <option
                                                            value="{{$manager->id}}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
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
                                  <button @click="transferOpened = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                    @lang('crm::common.cancel')
                                  </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endcan

        @can('massDelete', \App\CRM\LeadOrderAssignment::class)
            <div x-show="deletingOpened" class="fixed inset-x-0 bottom-0 z-10 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                <div x-show="deletingOpened" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div x-show="deletingOpened" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                        <button @click="deletingOpened = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form method="post" action="{{ route('crm.assignments.mass-delete') }}">
                        @csrf
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
                                <template x-for="id in transferAssignments" :key="id">
                                    <input type="hidden" name="assignments[]" :value="id" />
                                </template>
                                <div class="mt-2">
                                    <div class="w-full mr-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red sm:text-sm sm:leading-5">
                                @lang('crm::common.delete')
                              </button>
                            </span>
                            <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              <button @click="deletingOpened = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                @lang('crm::common.cancel')
                              </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        @can('massMarkAsLeftover', \App\CRM\LeadOrderAssignment::class)
            <div x-show="markingLeftoverOpened" class="fixed inset-x-0 bottom-0 z-10 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                <div x-show="markingLeftoverOpened" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div x-show="markingLeftoverOpened" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                        <button @click="markingLeftoverOpened = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form method="post" action="{{ route('crm.assignments.mass-mark-leftover') }}">
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
                                <template x-for="id in transferAssignments" :key="id">
                                    <input type="hidden" name="assignments[]" :value="id" />
                                </template>
                                <div class="mt-2">
                                    <div class="w-full mr-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red sm:text-sm sm:leading-5">
                                @lang('crm::lead.mark_as_leftover')
                              </button>
                            </span>
                            <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              <button @click="markingLeftoverOpened = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                @lang('crm::common.cancel')
                              </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
