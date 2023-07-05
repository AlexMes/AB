@extends('deluge::app')
@section('title', 'Детали приложения')
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
                        Ошибка валидации данных. Проверьте информацию, и попробуйте ещё раз.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="mx-auto mt-8 overflow-hidden bg-white shadow sm:rounded-lg max-w-7xl">
        <div class="flex items-center justify-between px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                {{ sprintf('[#%s] %s ',$unityApp->id, $unityApp->name) }}
            </h3>

            <div>
                @can('update', $unityApp)
                    <a href="{{ route('deluge.unity.apps.edit', $unityApp) }}"
                       class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                        <svg class="w-5 h-5 mr-2 -ml-1 text-gray-400" fill="none" stroke-linecap="round"
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
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        ID приложения
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-block">{{ $unityApp->id }}</span>
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        Название
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $unityApp->name }}
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        Организация
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $unityApp->organization->name }}
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        Store
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $unityApp->store }}
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        Store ID
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $unityApp->store_id }}
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        Домен
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $unityApp->adomain }}
                    </dd>
                </div>
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm font-medium leading-5 text-gray-500">
                        Дата и время создания
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $unityApp->created_at->format('M d, H:i:s') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
