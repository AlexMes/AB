@extends('deluge::app')
@section('title', 'Редактирование записи')
@section('content')
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

    <div x-data="InsightsForm()" x-init="init()" class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ sprintf('[#%d] %s ',$insight->id, $insight->date->format('d.m.Y')) }}
            </h3>
        </div>
        <form method="post" action="{{ route('deluge.insights.update', $insight) }}" class="px-6">
            @method('PUT')
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
                               value="{{ old('date', $insight->date->toDateString()) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="date"/>
                    </div>
                    @error('date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="account_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Аккаунт
                </label>
                <div class="-my-2 sm:col-span-2">
                    <div class="max-w-xs">
                        @component('deluge::components.multiselect', [
                                'id' => 'account_id',
                                'options' => $accounts,
                                'trackBy' => 'account_id',
                                'label' => 'name',
                                'multiple' => false,
                                'selected' => old('account_id', $insight->account_id),
                            ]) @endcomponent
                    </div>
                    @error('account_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
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
                            @foreach($campaigns as $campaign)
                                <option
                                    value="{{ $campaign->id }}" {{ old('campaign_id', $insight->campaign_id) == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('campaign_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="impressions" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Показы
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="impressions"
                               name="impressions"
                               value="{{ old('impressions', $insight->impressions) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               placeholder="0"
                               type="number"/>
                    </div>
                    @error('impressions') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
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
                               value="{{ old('clicks', $insight->clicks) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               placeholder="0"
                               type="number"/>
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
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                  <span class="text-gray-500 sm:text-sm sm:leading-5">
                                    $
                                  </span>
                            </div>
                            <input type="number" name="spend" id="spend" step="0.01"
                                   value="{{ old('spend', $insight->spend) }}"
                                   class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                                   placeholder="0" aria-describedby="price-currency"/>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <span class="text-gray-500 sm:text-sm sm:leading-5" id="price-currency">
                                USD
                              </span>
                            </div>
                        </div>
                    </div>
                    @error('spend') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="leads_cnt" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Лиды
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="leads_cnt"
                               name="leads_cnt"
                               value="{{ old('leads_cnt', $insight->leads_cnt) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               placeholder="0"
                               type="number"/>
                    </div>
                    @error('leads_cnt') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 py-5">
                <div class="flex justify-end">
                  <span class="inline-flex rounded-md shadow-sm">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> Отмена
                    </a>
                  </span>
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                    <button type="submit"
                            class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
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
