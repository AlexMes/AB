@extends('crm::app')
@section('title', __('crm::common.manager_report'))
@section('content')
    <div class="max-w-full mt-6" x-data="ManagerStatistics()" x-init="init()">
        <div class="flex flex-col">
            <form action="{{ route('crm.manager-statistic') }}" class="flex flex-col flex-no-wrap items-center mb-6 md:flex-wrap md:flex-row">
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="since" class="block text-sm font-medium leading-5 text-gray-700">
                        @lang('crm::filter.period_start')
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input
                            type="date"
                            id="since"
                            name="since"
                            value="{{ request('since', now()->startOfMonth()->toDateString()) }}"
                            class="block w-full transition duration-150 ease-in-out shadow-sm form-input sm:text-sm sm:leading-5"
                        />
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="until" class="block text-sm font-medium leading-5 text-gray-700">
                        @lang('crm::filter.period_end')
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input
                            type="date"
                            id="until"
                            name="until"
                            value="{{ request('until', now()->toDateString()) }}"
                            class="block w-full transition duration-150 ease-in-out shadow-sm form-input sm:text-sm sm:leading-5"
                        />
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="since_registration" class="block text-sm font-medium leading-5 text-gray-700">
                        Начало периода регистрации
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input
                            type="date"
                            id="since_registration"
                            name="since_registration"
                            value="{{ request('since_registration') }}"
                            class="block w-full transition duration-150 ease-in-out shadow-sm form-input sm:text-sm sm:leading-5"
                        />
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="until_registration" class="block text-sm font-medium leading-5 text-gray-700">
                        Конец периода регистрации
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input
                            type="date"
                            id="until_registration"
                            name="until_registration"
                            value="{{ request('until_registration') }}"
                            class="block w-full transition duration-150 ease-in-out shadow-sm form-input sm:text-sm sm:leading-5"
                        />
                    </div>
                </div>
                <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                    <label for="status" class="block text-sm font-medium leading-5 text-gray-700">
                        @lang('crm::filter.offer_type')
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <select id="offerType"
                                onchange="this.form.submit()"
                                name="offerType"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option value=''>@lang('crm::common.all')</option>
                            <option value='current'>@lang('crm::filter.current')</option>
                            <option value='leftovers'>@lang('crm::filter.leftovers')</option>
                        </select>
                    </div>
                </div>
                @if($offices->count() > 1)
                    <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                        <label for="status" class="block text-sm font-medium leading-5 text-gray-700">
                            @lang('crm::filter.office')
                        </label>
                        <div class="mt-1 rounded-md shadow-sm">
                            <select id="office"
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
                @if(auth('web')->check() || auth('crm')->user()->hasElevatedPrivileges())
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
                @if($offers->count() > 1)
                    <div class="w-full pr-4 md:w-1/5 sm:col-span-3">
                        <label for="offer" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                            @lang('crm::filter.offer')
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
                @endif
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
                <div class="flex flex-col w-full pr-4 ml-auto md:w-auto md:flex-row sm:col-span-3">
                    <div class="mt-5 sm:col-span-3">
                        <span class="inline-flex rounded-md shadow-sm">
                          <button type="submit"
                                  class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-teal-600 border border-transparent rounded-md hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                    clip-rule="evenodd" fill-rule="evenodd"></path></svg> @lang('crm::filter.filter')
                          </button>
                        </span>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="desc" value="{{ request('desc', false) }}">
            </form>
            <div class="py-2 -my-2 overflow-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div
                    class="inline-block min-w-full align-middle border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="min-w-full overflow-auto">
                        <thead>
                        <tr>
                            <th class="px-3 py-3 text-xs font-medium leading-4 text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                @component('crm::components.sort', [
                                    'header'     => trans('crm::report.manager'),
                                    'key'        => 'name',
                                    'defaultKey' => 'name',
                                ]) @endcomponent
                            </th>
                            <th class="px-3 py-2 text-xs font-medium leading-4 text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                @component('crm::components.sort', [
                                    'header'     => trans('crm::report.ordered'),
                                    'key'        => 'ordered',
                                    'defaultKey' => 'name',
                                ]) @endcomponent
                            </th>
                            <th class="px-3 py-2 text-xs font-medium leading-4 text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                @component('crm::components.sort', [
                                    'header'     => trans('crm::report.leads'),
                                    'key'        => 'total',
                                    'defaultKey' => 'name',
                                ]) @endcomponent
                            </th>
                            <th class="px-3 py-3 text-xs font-medium leading-4 text-left text-gray-500 uppercase whitespace-no-wrap border-b border-gray-200 bg-gray-50">
                                @component('crm::components.sort', [
                                    'header'     => trans('crm::report.clean_leads'),
                                    'key'        => 'clean_leads',
                                    'defaultKey' => 'name',
                                ]) @endcomponent
                            </th>
                            @foreach($statuses as $status)
                                <th class="px-3 py-3 text-xs font-medium leading-4 text-left text-gray-500 uppercase whitespace-no-wrap border-b border-gray-200 bg-gray-50">
                                    @component('crm::components.sort', [
                                        'header'     => $status['name'],
                                        'key'        => $status['as_column'],
                                        'defaultKey' => 'name',
                                    ]) @endcomponent
                                </th>
                            @endforeach
                            <th class="px-3 py-3 text-xs font-medium leading-4 text-left text-gray-500 uppercase whitespace-no-wrap border-b border-gray-200 bg-gray-50">
                                @component('crm::components.sort', [
                                    'header'     => trans('crm::report.conversion'),
                                    'key'        => 'conversion',
                                    'defaultKey' => 'name',
                                ]) @endcomponent
                            </th>
                            <th class="px-3 py-3 text-xs font-medium leading-4 text-left text-gray-500 uppercase whitespace-no-wrap border-b border-gray-200 bg-gray-50">
                                @lang('crm::report.day_to_day_cr')
                            </th>
                            <th class="px-3 py-3 text-xs font-medium leading-4 text-left text-gray-500 uppercase whitespace-no-wrap border-b border-gray-200 bg-gray-50">
                                @lang('crm::report.day_to_day_na')
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        @foreach($statistics as $statistic)
                            <tr class="hover:bg-gray-100">
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ $statistic->name }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ $statistic->ordered }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ $statistic->total }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->clean_leads }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->clean_leads, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->new }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->new, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->reject }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->reject, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->on_closer }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->on_closer, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->no_answer }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->no_answer, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->force_call }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->force_call, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->demo }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->demo, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->deposits }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->deposits, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->callback }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->callback, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->double }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->double, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->wrong_nb }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->wrong_nb, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span class="font-semibold"> {{ $statistic->reserve }} </span>
                                    <span class='text-sm text-gray-600'>({{ percentage($statistic->reserve, $statistic->total) }}&nbsp;%)</span>
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200 pxsemibold3">
                                    <span> {{ $statistic->conversion }} </span> &nbsp;%
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ round($statistic->dtd_deposits, 2) }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ round($statistic->dtd_no_answer, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="bg-gray-200">
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                @lang('crm::report.total')
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('ordered') }}
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('total') }}
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('clean_leads') }}
                                <span>({{ percentage($statistics->sum('clean_leads'), $statistics->sum('total')) }}&nbsp;%)</span>
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('new') }}
                                <span>({{ percentage($statistics->sum('new'), $statistics->sum('total')) }}&nbsp;%)</span>
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('reject') }}
                                <span>({{ percentage($statistics->sum('reject'), $statistics->sum('total')) }}&nbsp;%)</span>
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('on_closer') }}
                                <span>({{ percentage($statistics->sum('on_closer'), $statistics->sum('total')) }}&nbsp;%)</span>
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('no_answer') }}
                                ({{ percentage($statistics->sum('no_answer'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('force_call') }}
                                ({{ percentage($statistics->sum('force_call'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('demo') }}
                                ({{ percentage($statistics->sum('demo'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('deposits') }}
                                ({{ percentage($statistics->sum('deposits'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('callback') }}
                                ({{ percentage($statistics->sum('callback'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('double') }}
                                ({{ percentage($statistics->sum('double'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('wrong_nb') }}
                                ({{ percentage($statistics->sum('wrong_nb'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ $statistics->sum('reserve') }}
                                ({{ percentage($statistics->sum('reserve'), $statistics->sum('total')) }}&nbsp;%)
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ percentage($statistics->sum('deposits'), ($statistics->sum('total') - $statistics->sum('double') - $statistics->sum('wrong_nb'))).' %' }}
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ round($statistics->sum('dtd_deposits'), 2) }}
                            </td>
                            <td class="px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap border-b border-gray-200">
                                {{ round($statistics->sum('dtd_no_answer'), 2) }}
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
@endsection
