@extends('deluge::app')
@section('title', 'Детали группы')
@section('content')
    @if(session()->has('message'))
        <div class="rounded-md bg-blue-100 shadow-sm p-4 max-w-7xl mx-auto mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1 md:flex md:justify-between">
                    <p class="text-sm leading-5 text-blue-700">
                        {{ session()->pull('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-md bg-red-100 mt-6 p-4 max-w-7xl mx-auto">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm leading-5 font-medium text-red-800">
                        Ошибка валидации данных. Проверьте информацию, и попробуйте ещё раз.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8 max-w-7xl mx-auto">
        <div class="px-4 py-5 border-b border-gray-200 flex justify-between items-center sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ sprintf('[#%d] %s ',$group->id, $group->name) }}
            </h3>

            <div>
                @can('update', $group)
                    <a href="{{ route('deluge.groups.edit', $group) }}"
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke-linecap="round"
                             stroke-linejoin="round"
                             stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>
                            Редактировать
                        </span>
                    </a>
                @endcan
            </div>
        </div>
        <div class="px-4 py-5 sm:p-0">
            <dl>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Название
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-block">{{ $group->name }}</span>
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Филиал
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-block">{{ optional($group->branch)->name }}</span>
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Google
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-block">{{ $group->google }}</span>
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Дата и время создания
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $group->created_at->format('M d, H:i:s') }}
                    </dd>
                </div>
                {{--<div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Прохождение модерации за текущий месяц
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $group->current_month_approved_percent }} %
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Средний спенд за текущий месяц
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $group->accounts_count > 0 ? round($group->current_month_spend / $group->accounts_count, 2) : 0 }} $
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Бан за текущий месяц
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $group->current_month_blocked_percent }} %
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Среднее время жизни аккаунта за текущий месяц
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $group->accounts_count > 0 ? round($group->current_month_lifetime / $group->accounts_count, 2) : 0 }} мин.
                    </dd>
                </div>--}}
            </dl>
        </div>
    </div>

    @unless(auth()->user()->isDeveloper())
        <form action="{{ route('deluge.groups.show', $group) }}" class="flex flex-col mt-6">
            <div class="flex items-center">
                <div class="w-1/4 mr-4 sm:col-span-3">
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
                <div class="w-1/4 mr-4 sm:col-span-3">
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
                <div class="ml-4 mt-5 ml-auto sm:col-span-3">
                    <span class="inline-flex rounded-md shadow-sm">
                        <button type="submit"
                              class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                clip-rule="evenodd" fill-rule="evenodd"></path></svg> Фильтровать
                        </button>
                    </span>
                </div>
            </div>
        </form>
        <div class="mt-6 overflow-scroll sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="inline-block min-w-full align-middle border-b border-gray-200 shadow sm:rounded-lg">
                <table class="relative table w-full table-auto">
                    <thead>
                    <tr>
                        @foreach($report['headers'] as $key => $header)
                            <th class="sticky top-0 px-3 py-2 text-xs font-medium leading-4 text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center">
                                    <span>{{ $header }}</span>
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
                    <tfoot><tr></tr></tfoot>
                </table>
            </div>
        </div>

        <div class="mt-9">
            @include('deluge::accounts.index-data')
        </div>
    @endif
@endsection
