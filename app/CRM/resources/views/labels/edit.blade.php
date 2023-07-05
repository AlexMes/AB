@extends('crm::app')
@section('title', __('crm::common.labels'))
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
                        @lang('crm::validation.common_error')
                    </p>
                </div>
            </div>
        </div>
    @endif
    <div class="mt-8">
        <div class="flex flex-col">
            <div class="container mx-auto w-full">
                <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        @lang('crm::label.editing')
                    </h3>
                    <form method="POST"
                          action="{{ route('crm.labels.update', $label) }}">
                        @method('PUT')
                        @csrf
                        <div
                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-b sm:border-gray-200 sm:py-5">
                            <label
                                for="name"
                                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                            >
                                @lang('crm::label.form_name')
                            </label>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                <div class="max-w-lg rounded-md shadow-sm">
                                    <input id="name"
                                           name="name" class="form-input flex block w-full sm:text-sm sm:leading-5"
                                           value="{{ $label->name }}"
                                           placeholder="{{ $label->name }}"/>
                                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 py-5">
                            <div class="flex justify-end">
                                    <span class="inline-flex rounded-md shadow-sm">
                                        <a href="{{ route('crm.labels.index') }}"
                                           class="inline-flex items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" fill-rule="evenodd">
                                                </path>
                                            </svg>
                                            @lang('crm::common.cancel')
                                        </a>
                                    </span>
                                <span class="ml-3 inline-flex rounded-md shadow-sm">
                                        <button type="submit"
                                                class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition duration-150 ease-in-out">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" fill-rule="evenodd">
                                                </path>
                                            </svg>
                                            @lang('crm::common.save')
                                        </button>
                                    </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
