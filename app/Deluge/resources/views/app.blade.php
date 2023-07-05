@extends('deluge::layout')
@section('application')
    <div>
        <nav x-data="{ open: false }" @keydown.window.escape="open = false" class="bg-gray-800">
            <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8" src="https://tailwindui.com/img/logos/v1/workflow-mark-on-white.svg"
                                alt="Workflow logo">
                        </div>
                        <div class="hidden md:block">
                            <div class="flex items-baseline ml-10">
                                @can('viewAny', \App\ManualAccount::class)
                                    <a href="{{ route('deluge.accounts.index') }}"
                                        class="px-3 py-2 rounded-md text-sm font-medium {{ Request::is('accounts*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Аккаунты</a>
                                @endcan
                                {{-- @can('viewAny', \App\ManualPour::class)
                                    <a href="{{ route('deluge.pours.index') }}"
                                       class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('pours*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Заливы</a>
                                @endcan --}}
                                <div x-data="{ open: false }" @keydown.window.escape="open = false"
                                     @click.away="open = false" class="relative inline-block text-left">
                                    <div>
                                    <span class="rounded-md shadow-sm">
                                        <button type="button" @click="open = !open"
                                                class="inline-flex justify-center w-full rounded-md px-4 py-2 text-sm font-medium leading-5 transition ease-in-out duration-150 focus:outline-none focus:text-white focus:bg-gray-700 {{ Request::is('bundles*') || Request::is('traffic-sources*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}"
                                                id="reports-menu" aria-haspopup="true" x-bind:aria-expanded="open">
                                            Связки
                                            <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                      clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                    </div>

                                    <div x-show="open" style="display: none"
                                         x-description="Dropdown panel, show/hide based on dropdown state."
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 z-10 w-56 mt-2 origin-top-right rounded-md shadow-lg">
                                        <div class="bg-white rounded-md shadow-xs">
                                            <div class="py-1" role="menu" aria-orientation="vertical"
                                                 aria-labelledby="options-menu">
                                                @can('viewAny', \App\ManualBundle::class)
                                                    <a href="{{ route('deluge.bundles.index') }}"
                                                       class="block px-4 py-2 text-sm leading-5 {{ Request::is('bundles*') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                       role="menuitem">Связки</a>
                                                @endcan
                                                @can('viewAny', \App\ManualTrafficSource::class)
                                                    <a href="{{ route('deluge.traffic-sources.index') }}"
                                                       class="block px-4 py-2 text-sm leading-5 {{ Request::is('traffic-sources*') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                       role="menuitem">Источники трафика</a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @can('viewAny', \App\ManualGroup::class)
                                    <a href="{{ route('deluge.groups.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('groups*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Группы</a>
                                @endcan
                                @can('viewAny', \App\ManualSupplier::class)
                                    <a href="{{ route('deluge.suppliers.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('suppliers*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Поставщики</a>
                                @endcan

                                @can('viewAny', \App\ManualCampaign::class)
                                    <a href="{{ route('deluge.campaigns.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('campaigns*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Кампании</a>
                                @endcan
                                @can('viewAny', \App\ManualInsight::class)
                                    <a href="{{ route('deluge.insights.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('insights*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Статистика</a>
                                @endcan
                                @can('viewAny', \App\Deluge\Domain::class)
                                    <a href="{{ route('deluge.domains.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('domains*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Domains</a>
                                @endcan
                                @if (in_array(auth()->user()->role, [
                                    \App\User::ADMIN,
                                    \App\User::HEAD,
                                    \App\User::TEAMLEAD,
                                    \App\User::DESIGNER,
                                ]) ||
                                    (in_array(Request::ip(), \App\AdsBoard::DELUGE_OFFICE_IPS) &&
                                        !auth()->user()->isFinancier()))
                                    <div x-data="{ open: false }" @keydown.window.escape="open = false"
                                        @click.away="open = false" class="relative inline-block text-left">
                                        <div>
                                            <span class="rounded-md shadow-sm">
                                                <button type="button" @click="open = !open"
                                                    class="inline-flex justify-center w-full rounded-md px-4 py-2 text-sm font-medium leading-5 transition ease-in-out duration-150 focus:outline-none focus:text-white focus:bg-gray-700 {{ Request::is('reports/*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}"
                                                    id="reports-menu" aria-haspopup="true" x-bind:aria-expanded="open">
                                                    Отчеты
                                                    <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </div>

                                        <div x-show="open" style="display: none"
                                            x-description="Dropdown panel, show/hide based on dropdown state."
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 z-10 w-56 mt-2 origin-top-right rounded-md shadow-lg">
                                            <div class="bg-white rounded-md shadow-xs">
                                                <div class="py-1" role="menu" aria-orientation="vertical"
                                                    aria-labelledby="options-menu">
                                                    <a href="{{ route('deluge.reports.performance', ['since' => now()->startOfMonth()->toDateString(),'until' => now()->toDateString()]) }}"
                                                        class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/performance') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                        role="menuitem">Отчет</a>
                                                    <a href="{{ route('deluge.reports.quiz', ['since' => now()->startOfMonth()->toDateString(),'until' => now()->toDateString(),'group' => 'offer']) }}"
                                                        class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/quiz') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                        role="menuitem">Квизы</a>
                                                    @unless(auth()->user()->isDesigner())
                                                        <a href="{{ route('deluge.reports.account-stats') }}"
                                                            class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/account-stats') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                            role="menuitem">Стата по аккаунтам</a>
                                                        <a href="{{ route('deluge.reports.buyer-stats') }}"
                                                            class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/buyer-stats') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                            role="menuitem">Стата по байерам</a>
                                                    @endunless
                                                    @if (auth()->user()->isAdmin())
                                                        <a href="{{ route('deluge.reports.designer-conversion') }}"
                                                            class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/designer-conversion') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                            role="menuitem">Конверсия дизайнеров</a>
                                                    @endif
                                                    @if (auth()->user()->isAdmin() ||
                                                        auth()->user()->isBranchHead() ||
                                                        auth()->user()->isBuyer())
                                                        <a href="{{ route('deluge.reports.average-spend') }}"
                                                            class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/average-spend') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                            role="menuitem">Средний спенд</a>
                                                    @endif
                                                    @if (auth()->user()->isAdmin() || auth()->id() === 132)
                                                        <a href="{{ route('deluge.reports.buyer-costs') }}"
                                                            class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/buyer-costs') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                            role="menuitem">Косты байеров</a>
                                                    @endif
                                                    @if (auth()->user()->isAdmin() || auth()->id() === 37)
                                                        <a href="{{ route('deluge.reports.lead-stats') }}"
                                                            class="block px-4 py-2 text-sm leading-5 {{ Request::is('reports/lead-stats') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                            role="menuitem">Статистика лидов</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @can('viewAny', \App\ManualCreditCard::class)
                                    <a href="{{ route('deluge.credit-cards.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('credit-cards*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Карты</a>
                                @endcan

                                @if(in_array(auth()->user()->branch_id, [16, 25, 26]))
                                    <a href="{{ route('deluge.leads.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('leads*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Leads</a>
                                @endcan

                                @can('viewAny', \App\ManualApp::class)
                                    <a href="{{ route('deluge.apps.index') }}"
                                        class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ Request::is('apps*') ? 'text-white bg-gray-900 focus:outline-none focus:text-white focus:bg-gray-700' : 'text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700' }} ">Apps</a>
                                @endcan

                                @if(auth()->user()->can('viewAny', \App\UnityOrganization::class)
                                        || auth()->user()->can('viewAny', \App\UnityApp::class)
                                        || auth()->user()->can('viewAny', \App\UnityCampaign::class)
                                        || auth()->user()->can('viewAny', \App\UnityInsight::class))
                                    <div x-data="{ open: false }" @keydown.window.escape="open = false"
                                         @click.away="open = false" class="relative inline-block text-left ml-4">
                                        <div>
                                        <span class="rounded-md shadow-sm">
                                            <button type="button" @click="open = !open"
                                                    class="inline-flex justify-center w-full rounded-md px-4 py-2 text-sm font-medium leading-5 transition ease-in-out duration-150 focus:outline-none focus:text-white focus:bg-gray-700 {{ Request::is('unity/*') ? 'text-white bg-gray-900' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}"
                                                    id="reports-menu" aria-haspopup="true" x-bind:aria-expanded="open">
                                                Unity
                                                <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg"
                                                     viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                          clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                        </div>

                                        <div x-show="open" style="display: none"
                                             x-description="Dropdown panel, show/hide based on dropdown state."
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 z-10 w-56 mt-2 origin-top-right rounded-md shadow-lg">
                                            <div class="bg-white rounded-md shadow-xs">
                                                <div class="py-1" role="menu" aria-orientation="vertical"
                                                     aria-labelledby="options-menu">
                                                    @can('viewAny', \App\UnityOrganization::class)
                                                        <a href="{{ route('deluge.unity.organizations.index') }}"
                                                           class="block px-4 py-2 text-sm leading-5 {{ Request::is('unity/organizations*') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                           role="menuitem">Организации</a>
                                                    @endcan
                                                    @can('viewAny', \App\UnityApp::class)
                                                        <a href="{{ route('deluge.unity.apps.index') }}"
                                                           class="block px-4 py-2 text-sm leading-5 {{ Request::is('unity/apps*') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                           role="menuitem">Приложения</a>
                                                    @endcan
                                                    @can('viewAny', \App\UnityCampaign::class)
                                                        <a href="{{ route('deluge.unity.campaigns.index') }}"
                                                           class="block px-4 py-2 text-sm leading-5 {{ Request::is('unity/campaigns*') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                           role="menuitem">Кампании</a>
                                                    @endcan
                                                    @can('viewAny', \App\UnityInsight::class)
                                                        <a href="{{ route('deluge.unity.insights.index') }}"
                                                           class="block px-4 py-2 text-sm leading-5 {{ Request::is('unity/insights*') ? 'text-gray-900 bg-gray-200' : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }} focus:outline-none focus:bg-gray-300 focus:text-gray-900"
                                                           role="menuitem">Статистика</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center flex-1 px-2 lg:ml-6 lg:justify-end">
                        <form action="{{ route('deluge.accounts.index') }}" method="get"
                            class="w-full max-w-lg mb-0 lg:max-w-xs">
                            <label for="search" class="sr-only">Поиск</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="search" name="search" value="{{ request('search') }}"
                                    class="block w-full py-2 pl-10 pr-3 leading-5 text-gray-300 placeholder-gray-400 transition duration-150 ease-in-out bg-gray-700 border border-transparent rounded-md focus:outline-none focus:bg-white focus:text-gray-900 sm:text-sm"
                                    placeholder="Поиск" type="search" />
                            </div>
                        </form>
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
                                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
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
                                        <a href="{{ route('deluge.logout') }}"
                                            class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 group hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900">
                                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Выйти
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
                            <svg :class="{ 'hidden': !open, 'block': open }" class="hidden w-6 h-6" stroke="currentColor"
                                fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden">
                <div class="px-2 pt-2 pb-3 sm:px-3">
                    <a href="{{ route('deluge.accounts.index') }}"
                        class="block px-3 py-2 text-base font-medium text-white bg-gray-900 rounded-md focus:outline-none focus:text-white focus:bg-gray-700">Аккаунты</a>
                    {{-- <a href="#" class="block px-3 py-2 mt-1 text-base font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700">Team</a> --}}
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
                            <div class="mt-1 text-sm font-medium leading-none text-gray-400">{{ auth()->user()->email }}
                            </div>
                        </div>
                    </div>
                    <div class="px-2 mt-3" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                        <a href="{{ route('deluge.logout') }}"
                            class="block px-3 py-2 mt-1 text-base font-medium text-gray-400 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                            role="menuitem">Выйти</a>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow-sm">
            <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-lg font-semibold leading-6 text-gray-900">
                    @yield('title', 'Page title')
                </h1>
                <span>{{ Request::ip() }}</span>
            </div>
        </header>
        <main class="container mx-auto">
            <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
@endsection
