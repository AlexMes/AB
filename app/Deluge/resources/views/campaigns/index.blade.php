@extends('deluge::app')
@section('title', 'Кампании')
@section('content')
    <div x-data="CampaignsIndex()" x-init="init()" class="mt-8">
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
            <form action="{{ route('deluge.campaigns.index') }}" class="flex flex-col md:flex-row items-center mb-6">
                <div class="w-1/6 md:w-1/5 mr-4 sm:col-span-3">
                    <label for="bundle" class="block text-sm font-medium leading-5 text-gray-700">
                        Связка
                    </label>
                    <div class="mt-1">
                        @component('deluge::components.multiselect', [
                                'id' => 'bundle',
                                'options' => $bundles,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-1/6 md:w-1/5 mr-4 sm:col-span-3">
                    <label for="search" class="mx-2 block text-sm font-medium leading-5 text-gray-700">
                        Поиск
                    </label>
                    <div class="mt-3">
                        <input id="search"
                           name="search"
                           value="{{ request('search') }}"
                           class="mb-2 mx-2 leading-6 p-2 px-2 appearance-none outline-none w-full text-gray-800 border border-transparent border-gray-200 rounded"
                           placeholder="@lang('crm::common.search')" type="search"/>
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
                <div class="mx-4 mt-5 sm:col-span-3">
                    <span class="inline-flex rounded-md shadow-sm">
                      <a href="{{ route('deluge.campaigns.create') }}"
                         class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                        <svg viewBox="0 0 128 128" width="16px" height="16px" fill="white" class="mr-2">
                                <path
                                    d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"/><path
                                d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"/>
                            </svg>
                            Добавить кампанию
                      </a>
                    </span>
                </div>
            </form>
            @include('deluge::campaigns.index-data')
        </div>
    </div>
@endsection
