@extends('deluge::app')
@section('title', 'Performance report')
@section('content')
    <div class="max-w-full mt-6" x-data="PerformanceReport()" x-init="init()">
        <div class="flex flex-col">
            @if(in_array(auth()->id(), [199, 204]))
                <form action="{{ route('deluge.revenue-visibility') }}" method="post" class="flex flex-col mb-6">
                    @csrf
                    <div class="flex items-center">
                        <div class="mr-4 sm:col-span-3">
                            <label class="relative block text-sm font-medium leading-5 text-gray-700 bottom-1">
                                Показывать revenue
                            </label>
                            <div class="mt-1">
                                <button class="inline-flex items-center mt-3 cursor-pointer">
                                    <span class="relative">
                                        <span class="@if(auth()->user()->displayRevenue()) bg-indigo-600 @else bg-gray-300 @endif block w-10 h-6 bg-gray-300 rounded-full shadow-inner"></span>
                                        <span class="@if(auth()->user()->displayRevenue()) transform translate-x-full @endif absolute inset-y-0 left-0 block w-4 h-4 mt-1 ml-1 transition-transform duration-300 ease-in-out bg-white rounded-full shadow focus-within:shadow-outline">
                                        </span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
            <form action="{{ route('deluge.reports.performance') }}" class="flex flex-col mb-6">
                <div class="flex items-center">
                    <div class="w-1/5 mr-4 sm:col-span-3">
                        <label for="since" class="block text-sm font-medium leading-5 text-gray-700">
                            Начало периода
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
                    <div class="w-1/5 mr-4 sm:col-span-3">
                        <label for="until" class="block text-sm font-medium leading-5 text-gray-700">
                            Конец периода
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
                    @unless(auth()->user()->isDesigner())
                        <div class="w-1/5 mr-4 sm:col-span-3">
                            <label for="level" class="block text-sm font-medium leading-5 text-gray-700">
                                Уровень
                            </label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <select id="level"
                                        name="level"
                                        class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                    <option value="account" @if(request('level') == 'account') selected @endif>Аккаунт</option>
                                    <option value="campaign" @if(request('level', 'campaign') == 'campaign') selected @endif>Кампания</option>
                                    <option value="creo" @if(request('level') == 'creo') selected @endif>Креатив</option>
                                </select>
                            </div>
                        </div>
                    @endunless
                    @if (!auth()->user()->isBuyer() || auth()->id() === 132)
                        <div class="w-1/5 mr-4 sm:col-span-3">
                            <label for="users" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                                Байер
                            </label>
                            <div class="mt-1">
                                @component('deluge::components.multiselect', [
                                        'id' => 'users',
                                        'options' => $users,
                                        'trackBy' => 'id',
                                        'label' => 'name',
                                    ]) @endcomponent
                            </div>
                        </div>
                    @endif
                    <div class="mx-4 mt-5 ml-auto sm:col-span-3">
                        <x-deluge::btn-dropdown>
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                    clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                            <span>Фильтровать</span>

                            <x-slot name="options">
                                <a href="{{ route('deluge.reports.exports.performance', request()->all()) }}"
                                   target="_blank"
                                   class="relative inline-flex items-center w-full px-4 py-2 text-sm font-medium leading-5 bg-indigo-600 text-gray-50 hover:bg-indigo-500 hover:text-white focus:outline-none focus:bg-indigo-500 focus:text-white">
                                    <span class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2 -ml-1 text-white"
                                             fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        <span>Скачать</span>
                                    </span>
                                </a>
                            </x-slot>
                        </x-deluge::btn-dropdown>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-1/5 mr-4 sm:col-span-3">
                        <label for="offers" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                            Офер
                        </label>
                        <div class="mt-1">
                            @component('deluge::components.multiselect', [
                                    'id' => 'offers',
                                    'options' => $offers,
                                    'trackBy' => 'id',
                                    'label' => 'name',
                                ]) @endcomponent
                        </div>
                    </div>
                    <div class="w-1/5 mr-4 sm:col-span-3">
                        <label for="groups" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                            Группа
                        </label>
                        <div class="mt-1">
                            @component('deluge::components.multiselect', [
                                    'id' => 'groups',
                                    'options' => $groups,
                                    'trackBy' => 'id',
                                    'label' => 'name',
                                ]) @endcomponent
                        </div>
                    </div>
                    @if (auth()->id() === 0)
                        <div class="w-1/5 mr-4 sm:col-span-3">
                            <label for="accounts" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                                Аккаунты
                            </label>
                            <div class="mt-1">
                                @component('deluge::components.multiselect', [
                                        'id' => 'accounts',
                                        'options' => $accounts,
                                        'trackBy' => 'account_id',
                                        'label' => 'name',
                                    ]) @endcomponent
                            </div>
                        </div>
                    @endif
                    <div class="w-1/5 mr-4 sm:col-span-3">
                        <label for="utm_campaigns" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                            UTM Campaign
                        </label>
                        <div class="mt-1">
                            @component('deluge::components.multiselect', [
                                    'id' => 'utm_campaigns',
                                    'options' => $utm_campaigns,
                                    'trackBy' => 'account_id',
                                    'label' => 'name',
                                ]) @endcomponent
                        </div>
                    </div>
                    <div class="mr-4 sm:col-span-3">
                        <label class="relative block text-sm font-medium leading-5 text-gray-700 bottom-1">
                            Разбивать по байеру
                        </label>
                        <div class="mt-1">
                            <label @click="byUser = !byUser" for="checked" class="inline-flex items-center mt-3 cursor-pointer">
                                <span class="relative">
                                    <span :class="{'bg-indigo-600': byUser, 'bg-gray-300': !byUser}" class="block w-10 h-6 bg-gray-300 rounded-full shadow-inner"></span>
                                    <span :class="{'transform translate-x-full': byUser}" class="absolute inset-y-0 left-0 block w-4 h-4 mt-1 ml-1 transition-transform duration-300 ease-in-out bg-white rounded-full shadow focus-within:shadow-outline">
                                        <input x-ref="by_user" @if(request('by_user')) checked @endif name="by_user" type="checkbox" value="1" :checked="byUser" class="absolute w-0 h-0 opacity-0" />
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    @if(auth()->user()->isAdmin() || in_array(auth()->id(), \App\AdsBoard::PRIV))
                        <div class="w-1/5 mr-4 sm:col-span-3">
                            <label for="branches" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                                Филиал
                            </label>
                            <div class="mt-1">
                                @component('deluge::components.multiselect', [
                                        'id' => 'branches',
                                        'options' => $branches,
                                        'trackBy' => 'id',
                                        'label' => 'name',
                                    ]) @endcomponent
                            </div>
                        </div>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->isBranchHead())
                        <div class="w-1/5 mr-4 sm:col-span-3">
                            <label for="teams" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                                Команда
                            </label>
                            <div class="mt-1">
                                @component('deluge::components.multiselect', [
                                        'id' => 'teams',
                                        'options' => $teams,
                                        'trackBy' => 'id',
                                        'label' => 'name',
                                    ]) @endcomponent
                            </div>
                        </div>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->id() === 132
                        || (\App\Offer::allowed()->pluck('vertical')->unique()->filter()->count() === count(\App\Offer::VERTICALS))
                        )
                        <div class="w-1/5 mr-4 sm:col-span-3">
                            <label for="vertical" class="block text-sm font-medium leading-5 text-gray-700">
                                Вертикаль
                            </label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <select id="vertical"
                                        name="vertical"
                                        class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                    <option value="" @if(!request('vertical')) selected @endif>Все</option>
                                    @foreach(\App\Offer::VERTICALS as $vertical)
                                        <option value="{{ $vertical }}" @if(request('vertical') === $vertical) selected @endif>{{ $vertical }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="w-1/5 mr-4 sm:col-span-3">
                        <label for="part" class="block text-sm font-medium leading-5 text-gray-700">
                            Разделить
                        </label>
                        <div class="mt-1 rounded-md shadow-sm">
                            <select id="part"
                                    name="part"
                                    class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                                <option value="" @if(!request('part')) selected @endif>Все</option>
                                @foreach(\App\Deluge\Reports\Performance\Report::PARTS as $part)
                                    <option value="{{ $part }}" @if(request('part') === $part) selected @endif>{{ $part }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="desc" value="{{ request('desc', false) }}">
            </form>
            <div class="h-screen -my-2 overflow-scroll sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div
                    class="inline-block min-w-full align-middle border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="relative table w-full table-auto">
                        <thead>
                        <tr>
                            @php $defaultKey = array_key_first($report['headers']) @endphp
                            @foreach($report['headers'] as $key => $header)
                                <th class="sticky top-0 px-3 py-2 text-xs font-medium leading-4 text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                    <div class="flex items-center">
                                        <span>{{ $header }}</span>
                                        <div class="ml-1">
                                            @component('deluge::components.sort', [
                                                'key'        => $key,
                                                'defaultKey' => $defaultKey,
                                            ]) @endcomponent
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($report['rows'] as $row)
                                <tr class="hover:bg-gray-100">
                                    @foreach($row as $key => $value)
                                        <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr class="">
                            @foreach($report['summary'] as $key => $value)
                                <td class="sticky bottom-0 px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap bg-gray-200 border-b border-gray-200">
                                    {{ $value }}
                                </td>
                            @endforeach
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
