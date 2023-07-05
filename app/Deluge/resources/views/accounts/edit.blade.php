@extends('deluge::app')
@section('title', 'Редактирование аккаунта')
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

    <div class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ sprintf('[#%d] %s ',$account->id, $account->name) }}
            </h3>
        </div>
        <form method="post" action="{{ route('deluge.accounts.update', $account) }}" class="px-6">
            @method('PUT')
            @csrf
            <div
                class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5">
                <label for="name" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Название
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="name"
                               name="name"
                               value="{{ old('name', $account->name) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="account_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Аккаунт ID
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="account_id"
                               name="account_id"
                               value="{{ old('account_id', $account->account_id) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('account_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="account_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Креатив
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input id="creo"
                               name="creo"
                               value="{{ old('creo', $account->creo) }}"
                               class="form-input block w-full sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('creo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="group_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Группы
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="group_id"
                                name="group_id[]"
                                multiple
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            @foreach($groups as $group)
                                <option
                                    value="{{ $group->id }}" {{ in_array($group->id, old('group_id', $account->groups->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('group_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    @error('group_id.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="user_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Байер
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="user_id"
                                name="user_id"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option
                                value="" {{ old('user_id', $account->user_id) === null ? 'selected' : '' }}>Не выбран
                            </option>
                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}" {{ old('user_id', $account->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('user_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="status" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Статус
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="status"
                                name="status"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option value="active" {{ old('status', $account->status) == 'active' ? 'selected' : '' }}>
                                Активный
                            </option>
                            <option
                                value="blocked" {{ old('status', $account->status) == 'blocked' ? 'selected' : '' }}>
                                Заблокирован
                            </option>
                        </select>
                    </div>
                    @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="moderation_status"
                       class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Статус модерации
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="moderation_status"
                                name="moderation_status"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option
                                value="review" {{ old('moderation_status', $account->moderation_status) == 'review' ? 'selected' : '' }}>
                                На рассмотрении
                            </option>
                            <option
                                value="approved" {{ old('moderation_status', $account->moderation_status) == 'approved' ? 'selected' : '' }}>
                                Аппрув
                            </option>
                            <option
                                value="disapproved" {{ old('moderation_status', $account->moderation_status) == 'disapproved' ? 'selected' : '' }}>
                                Дизаппрув
                            </option>
                        </select>
                    </div>
                    @error('moderation_status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="supplier_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Поставщик
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="supplier_id"
                                name="supplier_id"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option value="" disabled {{ old('supplier_id', $account->supplier_id) === null ? 'selected' : '' }}>Не выбран</option>
                            @foreach($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}" {{ old('supplier_id', $account->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('supplier_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
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
