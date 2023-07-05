@extends('deluge::app')
@section('title', 'Создание записи')
@section('content')
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

    <div class="mx-auto mt-8 overflow-hidden bg-white shadow sm:rounded-md max-w-7xl">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                Новая запись
            </h3>
        </div>
        <form method="post" action="{{ route('deluge.unity.insights.store') }}" class="px-6">
            @csrf
            <div
                class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5">
                <label for="date" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Дата
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="date"
                               name="date"
                               value="{{ old('date') }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="date"/>
                    </div>
                    @error('date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="app_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Приложение
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="app_id"
                                name="app_id"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option
                                value="" {{ old('app_id') === null ? 'selected' : '' }}>Не выбран
                            </option>
                            @foreach($unityApps as $unityApp)
                                <option
                                    value="{{ $unityApp->id }}" {{ old('app_id') == $unityApp->id ? 'selected' : '' }}>{{ $unityApp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('app_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="campaign_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Кампания
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="campaign_id"
                                name="campaign_id"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option
                                value="" {{ old('campaign_id') === null ? 'selected' : '' }}>Не выбран
                            </option>
                            @foreach($campaigns as $campaign)
                                <option
                                    value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('campaign_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="views" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Просмотры
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="views"
                               name="views"
                               value="{{ old('views') }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('views') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="clicks" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Клики
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="clicks"
                               name="clicks"
                               value="{{ old('clicks') }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('clicks') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="spend" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Кост
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="spend"
                               name="spend"
                               value="{{ old('spend') }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('spend') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="installs" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Инсталы
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="installs"
                               name="installs"
                               value="{{ old('installs') }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('installs') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="py-5 mt-8 border-t border-gray-200">
                <div class="flex justify-end">
                  <span class="inline-flex rounded-md shadow-sm">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> Отмена
                    </a>
                  </span>
                    <span class="inline-flex ml-3 rounded-md shadow-sm">
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium leading-5 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> Сохранить
                    </button>
                  </span>
                </div>
            </div>
        </form>
    </div>
@endsection
