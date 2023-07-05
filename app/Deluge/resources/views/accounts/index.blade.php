@extends('deluge::app')
@section('title', 'Аккаунты')
@section('content')
    <div x-data="AccountsIndex()" x-init="init()" class="mt-8">
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
                                @if ($errors->has('access'))
                                    {{ $errors->first('access') }}
                                @else
                                    Ошибка валидации данных. Проверьте информацию, и попробуйте ещё раз.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <form action="{{ route('deluge.accounts.index') }}" class="flex flex-col items-center mb-6 md:flex-row">
                <div class="w-1/6 mr-4 md:w-1/5 sm:col-span-3">
                    <label for="user" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        Байер
                    </label>
                    <div class="mt-1">
                        @component('deluge::components.multiselect', [
                                'id' => 'user',
                                'options' => $users,
                                'trackBy' => 'id',
                                'label' => 'name',
                            ]) @endcomponent
                    </div>
                </div>
                <div class="w-1/6 mr-4 sm:col-span-3">
                    <label for="moderation_status" class="block text-sm font-medium leading-5 text-gray-700">
                        Статус модерации
                    </label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <select id="moderation_status"
                                name="moderation_status"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option value="" @if(request('moderation_status') === null) selected @endif>Все</option>
                            <option value="review" @if(request('moderation_status') == 'review') selected @endif>На рассмотрении</option>
                            <option value="approved" @if(request('moderation_status') == 'approved') selected @endif>Аппрув</option>
                            <option value="disapproved" @if(request('moderation_status') == 'disapproved') selected @endif>Дизаппрув</option>
                        </select>
                    </div>
                </div>
                <div class="w-1/6 mr-4 md:w-1/5 sm:col-span-3">
                    <label for="group" class="relative block text-sm font-medium leading-5 text-gray-700 top-2">
                        Группа
                    </label>
                    <div class="mt-1">
                        @component('deluge::components.multiselect', [
                                'id' => 'group',
                                'options' => $groups,
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
                <div class="mx-4 mt-5 sm:col-span-3">
                    <span class="inline-flex rounded-md shadow-sm">
                      <a href="{{ route('deluge.accounts.create') }}"
                              class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                        <svg viewBox="0 0 128 128" width="16px" height="16px" fill="white" class="mr-2">
                                <path
                                    d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"/><path
                                d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"/>
                            </svg>
                            Добавить аккаунт
                      </a>
                    </span>
                </div>
                <div class="mx-4 mt-5 sm:col-span-3" x-show="accountsToPour.length>0">
                    <span class="inline-flex rounded-md shadow-sm">
                        <button type="submit"
                              @click.prevent="pourOpened = true"
                              class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                            <svg viewBox="0 0 128 128" width="16px" height="16px" fill="white" class="mr-2">
                                <path
                                    d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"/><path
                                  d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"/>
                            </svg>
                            <span>Создать залив</span>
                        </button>
                    </span>
                </div>
                <div class="mt-5 ml-auto sm:col-span-3">
                    <span class="inline-flex rounded-md shadow-sm">
                        <label for="files"
                               class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md cursor-pointer hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                            </svg>Импорт
                        </label>
                    </span>
                </div>
            </form>
            <form method="post" action="{{ route('deluge.imports.insights') }}" enctype="multipart/form-data" class="flex flex-col items-center mb-6 md:flex-row">
                @csrf
                <input id="files" type="file" name="files[]" multiple accept=".csv" class="hidden" onchange="this.form.submit()" />
            </form>
            @include('deluge::accounts.index-data')
        </div>

        <div x-show="pourOpened" class="fixed inset-x-0 bottom-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
            <div x-show="pourOpened" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div x-show="pourOpened" @click.away="pourOpened = false" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-auto sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                    <button @click="pourOpened = false" type="button" class="text-gray-400 transition duration-150 ease-in-out hover:text-gray-500 focus:outline-none focus:text-gray-500" aria-label="Close">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="post" action="{{ route('deluge.accounts-pours') }}">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-headline">
                                Создание залива
                            </h3>
                            <template x-for="id in accountsToPour" :key="id">
                                <input type="hidden" name="accounts[]" :value="id" />
                            </template>
                            <div class="mt-2">
                                <div class="w-full mr-4">
                                    <label for="date" class="block text-sm font-medium leading-5 text-gray-700">
                                        Выберите дату
                                    </label>
                                    <div class="mt-1 rounded-md shadow-sm">
                                        <input id="date"
                                               name="date"
                                               value="{{ old('date', now()->toDateString()) }}"
                                               class="block w-full form-input sm:text-sm sm:leading-5"
                                               type="date"/>
                                    </div>
                                    @error('date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red sm:text-sm sm:leading-5">
                                Создать
                              </button>
                            </span>
                        <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
                              <button @click="pourOpened = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5">
                                Отмена
                              </button>
                            </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
