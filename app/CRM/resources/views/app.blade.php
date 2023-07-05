@extends('crm::layout')
@section('application')
    <div>
        <nav x-data="{ open: false }" @keydown.window.escape="open = false" class="bg-gray-800">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8" src="https://tailwindui.com/img/logos/v1/workflow-mark-on-white.svg"
                                 alt="Workflow logo">
                        </div>
                        <div class="hidden md:block">
                            <div class="flex items-baseline ml-10">
                                <a href="{{ route('crm.assignments.index') }}"
                                   class="px-3 py-2 rounded-md text-sm font-medium {{ Request::is('assignments*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">@lang('crm::common.dashboard')</a>
                                <a href="{{ route('crm.statistic',['since' => now()->startOfMonth()->toDateString(),'until'=>now()->endOfMonth()->toDateString()]) }}"
                                   class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('statistic*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">@lang('crm::common.statistics')</a>
                                @if(auth('crm')->check() || auth('web')->user()->hasRole(['admin','support','head']))
                                    <a href="{{ route('crm.manager-statistic',['since' => now()->startOfMonth()->toDateString(),'until'=>now()->endOfMonth()->toDateString()]) }}"
                                       class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('manager-statistic*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">@lang('crm::common.manager_report')</a>
                                @endif
                                {{-- @if(auth('web')->check() && auth('web')->user()->hasRole(['admin','support'])
                                    || auth('crm')->check() && auth('crm')->user()->hasElevatedPrivileges()
                                    )
                                    <a href="{{ route('crm.office-statistic',['since' => now()->startOfMonth()->toDateString(),'until'=>now()->endOfMonth()->toDateString()]) }}"
                                       class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('office-statistic*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">@lang('crm::common.office_report')</a>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center flex-1 px-2 lg:ml-6 lg:justify-end">
                        <form action="{{ route('crm.assignments.index') }}" method="get"
                              class="w-full max-w-lg mb-0 lg:max-w-xs">
                            <label for="search" class="sr-only">@lang('crm::common.search')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input id="search"
                                       name="search"
                                       value="{{ request('search') }}"
                                       class="block w-full py-2 pl-10 pr-3 leading-5 text-gray-300 placeholder-gray-400 transition duration-150 ease-in-out bg-gray-700 border border-transparent rounded-md focus:outline-none focus:bg-white focus:text-gray-900 sm:text-sm"
                                       placeholder="@lang('crm::common.search')" type="search"/>
                            </div>
                        </form>
                        @if (auth('crm')->check())
                            <form action="{{ route('crm.managers.locale.update') }}" method="post"
                                  class="w-auto ml-4">
                                @csrf
                                <div class="mt-0 sm:col-span-2">
                                    <div class="max-w-xs rounded-md shadow-sm">
                                        <select
                                            name="locale"
                                            onchange="this.form.submit()"
                                            class="block w-full py-2 leading-5 text-gray-300 transition duration-150 ease-in-out bg-gray-700 border border-transparent form-select sm:text-sm">
                                            <option value="ru" {{ auth('crm')->user()->locale === 'ru' ? 'selected' : '' }}>Рус</option>
                                            <option value="en" {{ auth('crm')->user()->locale === 'en' ? 'selected' : '' }}>Eng</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="hidden md:block">
                        <div class="flex items-center ml-4 md:ml-6">
                            <!-- Profile dropdown -->
                            <div @click.away="open = false" @keydown.window.escape="open = false" class="relative ml-3"
                                 x-data="{ open: false }">
                                <div>
                                    <button @click="open = !open"
                                            class="flex items-center max-w-xs text-sm text-white rounded-full focus:outline-none focus:shadow-solid"
                                            id="user-menu" aria-label="User menu" aria-haspopup="true">
                                        <span class="inline-block w-8 h-8 overflow-hidden bg-gray-100 rounded-full">
                                          <svg class="w-full h-full text-gray-300" fill="currentColor"
                                               viewBox="0 0 24 24">
                                            <path
                                                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                                          </svg>
                                        </span>
                                    </button>
                                </div>
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 w-auto mt-2 origin-top-right rounded-md shadow-lg"
                                     style="display: none;">
                                    <div class="py-1 bg-white rounded-md shadow-xs">
                                        <div class="px-4 py-3">
                                            <p class="text-xs font-semibold leading-5 text-gray-900">
                                                {{ auth()->user()->name }}
                                            </p>
                                            <p class="text-xs font-normal leading-5 text-gray-600">
                                                {{ auth()->user()->email }}
                                            </p>
                                        </div>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="{{ route('crm.logout') }}"
                                           class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 group hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                            <svg
                                                class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                            </svg>
                                            @lang('crm::common.logout')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex -mr-2 md:hidden">
                        <!-- Mobile menu button -->
                        <button @click="open = !open"
                                class="inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white"
                                x-bind:aria-label="open ? 'Close main menu' : 'Main menu'" x-bind:aria-expanded="open"
                                aria-label="Main menu" aria-expanded="false">
                            <svg :class="{ 'hidden': open, 'block': !open }" class="block w-6 h-6" stroke="currentColor"
                                 fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg :class="{ 'hidden': !open, 'block': open }" class="hidden w-6 h-6"
                                 stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden">
                <div class="px-2 pt-2 pb-3 sm:px-3">
                    <a href="{{ route('crm.assignments.index') }}"
                       class="block px-3 py-2 text-base font-medium text-white bg-gray-900 rounded-md focus:outline-none focus:text-white focus:bg-gray-700">@lang('crm::common.dashboard')</a>
                    {{--          <a href="#" class="block px-3 py-2 mt-1 text-base font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Team</a>--}}
                </div>
                <div class="pt-4 pb-3 border-t border-gray-700">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <img class="w-10 h-10 rounded-full"
                                 src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80"
                                 alt="">
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium leading-none text-white">{{ auth()->user()->name }}</div>
                            <div
                                class="mt-1 text-sm font-medium leading-none text-gray-400">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="px-2 mt-3" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                        <a href="{{ route('crm.logout') }}"
                           class="block px-3 py-2 mt-1 text-base font-medium text-gray-400 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                           role="menuitem">@lang('crm::common.logout')</a>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow-sm">
            <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-lg font-semibold leading-6 text-gray-900">
                    @yield('title','Page title')
                </h1>
                <span
                    class="text-gray-700">MSK: {{ now('Europe/Moscow')->locale('ru_RU')->format('F, d, H:i:s') }}</span>
            </div>
        </header>
        <main>
            <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
@endsection
