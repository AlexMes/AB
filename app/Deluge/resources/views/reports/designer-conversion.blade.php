@extends('deluge::app')
@section('title', 'Designer conversion')
@section('content')
    <div class="max-w-full mt-6" x-data="DesignerConversionReport()" x-init="init()">
        <div class="flex flex-col">
            <form action="{{ route('deluge.reports.designer-conversion') }}" class="flex flex-col mb-6">
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
                    @if(auth()->user()->isAdmin())
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
                    <div class="mx-4 mt-5 ml-auto sm:col-span-3">
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

                <div class="flex items-center">
                    @if(auth()->user()->isAdmin() || auth()->id() === 132
                        || \App\Offer::allowed()->pluck('vertical')->unique()->filter()->count() === count(\App\Offer::VERTICALS)
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
