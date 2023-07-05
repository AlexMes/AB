@extends('deluge::app')
@section('title', 'Заливы')
@section('content')
    <div x-data="PoursIndex()" x-init="init()" class="mt-8">
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
            @if(auth()->user()->isAdmin())
                <form action="{{ route('deluge.pours.index') }}" class="flex flex-col md:flex-row items-center mb-6">
                    <div class="w-1/6 md:w-1/5 mr-4 sm:col-span-3">
                        <label for="user" class="block text-sm font-medium leading-5 text-gray-700">
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
                </form>
            @endif
            <div class="relative flex pb-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                @if($pours->count() > 0)
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
                                        Дата
                                    </th>
                                    <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        Байер
                                    </th>
                                    @can('destroy')
                                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                        </th>
                                    @endcan
                                </tr>
                                </thead>
                                <tbody class="bg-white">
                                @foreach($pours as $pour)
                                    <tr class="cursor-pointer hover:bg-gray-100"
                                        onclick="window.location = '{{ route('deluge.pours.show', $pour->id) }}'">
                                        <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $pour->id }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                            {{ $pour->date->format('d.m.Y') }}
                                        </td>
                                        <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                            {{ optional($pour->user)->name }}
                                        </td>
                                        @can('destroy')
                                            <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200" @click.stop="">
                                                <form method="POST" action="{{ route('deluge.pours.destroy', $pour) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="if(confirm('Удалить залив?')) this.form.submit()">
                                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="trash-alt" class="svg-inline--fa fa-trash-alt fa-w-14 w-4 h-4 text-red-500" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M268 416h24a12 12 0 0 0 12-12V188a12 12 0 0 0-12-12h-24a12 12 0 0 0-12 12v216a12 12 0 0 0 12 12zM432 80h-82.41l-34-56.7A48 48 0 0 0 274.41 0H173.59a48 48 0 0 0-41.16 23.3L98.41 80H16A16 16 0 0 0 0 96v16a16 16 0 0 0 16 16h16v336a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128h16a16 16 0 0 0 16-16V96a16 16 0 0 0-16-16zM171.84 50.91A6 6 0 0 1 177 48h94a6 6 0 0 1 5.15 2.91L293.61 80H154.39zM368 464H80V128h288zm-212-48h24a12 12 0 0 0 12-12V188a12 12 0 0 0-12-12h-24a12 12 0 0 0-12 12v216a12 12 0 0 0 12 12z"></path></svg>
                                                    </button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                                <div class="hidden sm:block">
                                    <p class="text-sm leading-5 text-gray-700">
                                        От
                                        <span class="font-medium">{{ $pours->firstItem() }}</span>
                                        до
                                        <span class="font-medium">{{ $pours->lastItem() }}</span>
                                        из
                                        <span class="font-medium">{{ $pours->total() }}</span>
                                        заливов
                                    </p>
                                </div>
                                <div class="flex justify-between flex-1 sm:justify-end">
                                    @if($pours->previousPageUrl())
                                        <a href="{{ $pours->appends(request(['user']))->previousPageUrl() }}"
                                           class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                            Предыдущая
                                        </a>
                                    @endif
                                    @if($pours->nextPageUrl())
                                        <a href="{{ $pours->appends(request(['user']))->nextPageUrl() }}"
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
                        <p>Заливов не найдено</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
