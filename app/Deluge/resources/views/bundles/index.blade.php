@extends('deluge::app')
@section('title', 'Связки')
@section('content')
    <div x-data="BundlesIndex()" x-init="init()" class="mt-8">
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
                                Ошибка валидации данных. Проверьте информацию, и попробуйте ещё раз.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <form action="{{ route('deluge.bundles.index') }}" class="flex flex-col items-center mb-6 md:flex-row">
                <div class="w-1/6 mr-4 md:w-1/5 sm:col-span-3">
                    <label for="offer" class="block text-sm font-medium leading-5 text-gray-700">
                        Офер
                    </label>
                    <div class="mt-1">
                        @component('deluge::components.multiselect', [
                                'id' => 'offer',
                                'options' => $offers,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="mx-4 mt-5 sm:col-span-3">
                    <span class="inline-flex rounded-md shadow-sm">
                      <button type="submit"
                              class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                clip-rule="evenodd" fill-rule="evenodd"></path></svg> Фильтровать
                      </button>
                    </span>
                </div>
                @can('create', \App\ManualBundle::class)
                    <div class="mx-4 mt-5 sm:col-span-3">
                        <span class="inline-flex rounded-md shadow-sm">
                        <a href="{{ route('deluge.bundles.create') }}"
                            class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                            <svg viewBox="0 0 128 128" width="16px" height="16px" fill="white" class="mr-2">
                                    <path
                                        d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"/><path
                                    d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"/>
                                </svg>
                                Добавить связку
                        </a>
                        </span>
                    </div>
                @endcan
            </form>
            <div class="relative flex pb-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                @if($bundles->count() > 0)
                    <div class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto sm:px-6 lg:px-8 max-w-8xl">
                        <div
                            class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                            <table class="min-w-full">
                                <thead>
                                <tr>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        ID
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        Название
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        Офер
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        Источник
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        Создан
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white">
                                @foreach($bundles as $bundle)
                                    <tr class="cursor-pointer hover:bg-gray-100"
                                        onclick="window.location = '{{ route('deluge.bundles.show', $bundle->id) }}'">
                                        <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $bundle->id }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $bundle->name }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                            {{ optional($bundle->offer)->name }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                            {{ optional($bundle->trafficSource)->name ?? '-' }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $bundle->created_at->format('M d, H:i:s') }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                                <div class="hidden sm:block">
                                    <p class="text-sm leading-5 text-gray-700">
                                        От
                                        <span class="font-medium">{{ $bundles->firstItem() }}</span>
                                        до
                                        <span class="font-medium">{{ $bundles->lastItem() }}</span>
                                        из
                                        <span class="font-medium">{{ $bundles->total() }}</span>
                                        звязок
                                    </p>
                                </div>
                                <div class="flex justify-between flex-1 sm:justify-end">
                                    @if($bundles->previousPageUrl())
                                        <a href="{{ $bundles->appends(request(['offer']))->previousPageUrl() }}"
                                           class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                            Предыдущая
                                        </a>
                                    @endif
                                    @if($bundles->nextPageUrl())
                                        <a href="{{ $bundles->appends(request(['offer']))->nextPageUrl() }}"
                                           class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                            Следующая
                                        </a>
                                    @endif
                                </div>
                            </nav>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow">
                        <p>Связок не найдено</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
