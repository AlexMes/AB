@extends('deluge::app')
@section('title', 'Создание аккаунта')
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
                Новый аккаунт
            </h3>
        </div>
        <form method="post" action="{{ route('deluge.accounts.store') }}" class="px-6">
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
                               value="{{ old('name') }}"
                               class="block w-full form-input sm:text-sm sm:leading-5"
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
                               value="{{ old('account_id') }}"
                               class="block w-full form-input sm:text-sm sm:leading-5"
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
                               value="{{ old('creo') }}"
                               class="block w-full form-input sm:text-sm sm:leading-5"
                               type="text"/>
                    </div>
                    @error('creo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="group_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    Группа
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="group_id"
                                name="group_id[]"
                                multiple
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            @foreach($groups as $group)
                                <option
                                    value="{{ $group->id }}" {{ in_array($group->id, old('group_id', [])) ? 'selected' : '' }}>{{ $group->name }}</option>
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
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option value="" {{ old('user_id') === null ? 'selected' : '' }}>Не выбран</option>
                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                Активный
                            </option>
                            <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Заблокирован
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
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option
                                value="review" {{ old('moderation_status', 'review') == 'review' ? 'selected' : '' }}>На
                                рассмотрении
                            </option>
                            <option value="approved" {{ old('moderation_status') == 'approved' ? 'selected' : '' }}>
                                Аппрув
                            </option>
                            <option
                                value="disapproved" {{ old('moderation_status') == 'disapproved' ? 'selected' : '' }}>
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
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5">
                            <option value="" disabled {{ old('supplier_id') === null ? 'selected' : '' }}>Не выбран</option>
                            @foreach($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('supplier_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
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
