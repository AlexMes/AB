<template>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <nav class="bg-white shadow">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex items-center flex-shrink-0">
                        <a class="flex items-center mx-3 lg:mx-0" href="/">
                            <svg
                                class="w-8 h-8"
                                viewBox="0 0 130 76"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M26.2301 61.3885C27.6748 64.1133 27.6192 68.3951 26.1745 70.953C24.7298 73.511 21.0625 75.5685 18.0064 75.5685H4.11496C1.05885 75.5685 -0.219162 73.3998 1.28111 70.7306L13.9501 47.9314C15.4504 45.2622 17.8397 45.2622 19.2288 47.987L26.2301 61.3885Z"
                                    fill="#2C7A7B"
                                />
                                <path
                                    d="M67.6822 56.9399C67.6822 56.9399 66.4597 54.7155 65.015 52.0464L41.5663 8.28307C40.1216 5.6139 37.7323 5.55829 36.232 8.22747L29.5641 20.4612C28.0638 23.1303 28.1194 27.5234 29.6197 30.1925L52.2349 70.7862C53.7352 73.4554 57.4581 75.6241 60.5142 75.6241L71.3495 75.5685C74.4056 75.5685 77.0728 75.5685 77.3506 75.5685C77.5728 75.5685 76.5727 73.3441 75.128 70.675L70.4049 61.8333C68.9046 59.1086 67.7377 56.9399 67.6822 56.9399Z"
                                    fill="#2C7A7B"
                                />
                                <path
                                    d="M120.636 18.4593C118.914 21.0172 115.191 23.0191 112.246 23.0191C109.301 23.0191 105.745 20.7392 104.3 18.07L97.632 5.83632C96.1873 3.16715 97.4653 0.998444 100.521 0.998444L126.582 1.22087C129.638 1.22087 130.749 3.33397 129.082 5.89193L120.636 18.4593Z"
                                    fill="#ADD5D5"
                                />
                                <path
                                    d="M61.681 19.8495C61.681 19.8495 62.9034 22.0738 64.3481 24.743L87.7968 68.5063C89.2415 71.1754 91.6309 71.231 93.1311 68.5619L99.8546 56.3838C101.355 53.7146 101.299 49.3216 99.799 46.6524L77.1838 6.05876C75.6835 3.38959 71.9606 1.22089 68.9045 1.22089L58.0692 1.27649C55.0131 1.27649 52.3459 1.27649 52.0681 1.27649C51.7903 1.27649 52.846 3.5008 54.2907 6.16998L59.0138 15.0116C60.403 17.6252 61.6254 19.8495 61.681 19.8495Z"
                                    fill="#ADD5D5"
                                />
                            </svg>
                        </a>
                    </div>
                    <div class="hidden lg:ml-6 lg:flex lg:space-x-2">
                        <router-link
                            v-if="
                                [
                                    'admin',
                                    'head',
                                    'teamlead',
                                    'buyer',
                                    'support',
                                    'subsupport'
                                ].includes(user.role)
                            "
                            :to="{ name: 'dashboard' }"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:border-gray-300 hover:text-teal-700"
                            exact-active-class="text-teal-700 border-teal-500 hover:border-teal-500"
                        >
                            Рабочий стол
                        </router-link>

                        <menu-dropdown
                            v-if="
                                [
                                    'admin',
                                    'head',
                                    'teamlead',
                                    'buyer',
                                    'verifier'
                                ].includes(user.role)
                            "
                            heading="Facebook"
                            :active="$route.path.match('/facebook')"
                        >
                            <!--                            <router-link-->
                            <!--                                :to="{ name: 'facebook.apps.index' }"-->
                            <!--                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"-->
                            <!--                                active-class="bg-gray-100"-->
                            <!--                            >-->
                            <!--                                Приложения-->
                            <!--                            </router-link>-->
                            <router-link
                                :to="{ name: 'profiles.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Профили
                            </router-link>
                            <router-link
                                :to="{ name: 'accounts.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Аккаунты
                            </router-link>
                            <router-link
                                :to="{ name: 'campaigns.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Кампании
                            </router-link>
                            <router-link
                                :to="{ name: 'adsets.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Адсеты
                            </router-link>
                            <router-link
                                v-if="user.role !== 'verifier'"
                                :to="{ name: 'accesses.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Доступы
                            </router-link>
                            <router-link
                                :to="{ name: 'profile-pages.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Страницы
                            </router-link>
                            <router-link
                                v-if="user.role !== 'verifier'"
                                :to="{ name: 'profile-logs.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Журнал профилей
                            </router-link>
                        </menu-dropdown>

                        <menu-dropdown
                            v-if="
                                [
                                    'admin',
                                    'head',
                                    'teamlead',
                                    'buyer',
                                    'verifier'
                                ].includes(user.role)
                            "
                            heading="VK"
                            :active="$route.path.match('/vk')"
                        >
                            <router-link
                                :to="{ name: 'vk-profiles.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Профили
                            </router-link>
                        </menu-dropdown>

                        <menu-dropdown
                            v-if="
                                [
                                    'admin',
                                    'support',
                                    'subsupport',
                                    'head'
                                ].includes(user.role)
                            "
                            heading="Результаты"
                            :active="$route.path.match('/results')"
                        >
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'results.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Обзвон
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'support',
                                        'developer',
                                        'subsupport',
                                        'head'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'leads.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Лиды
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'support',
                                        'developer',
                                        'subsupport',
                                        'head'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'deposits.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Депозиты
                            </router-link>
                        </menu-dropdown>

                        <menu-dropdown
                            v-if="['admin', 'developer'].includes(user.role)"
                            heading="Инфраструктура"
                            :active="
                                $route.path.match('/domains') ||
                                    $route.path.match('/servers')
                            "
                        >
                            <router-link
                                :to="{ name: 'domains.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Домены
                            </router-link>
                            <router-link
                                :to="{ name: 'orders.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Заказы ссылок
                            </router-link>
                            <router-link
                                :to="{ name: 'servers.index' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Серверы
                            </router-link>
                        </menu-dropdown>

                        <menu-dropdown
                            v-if="user.role !== 'developer'"
                            heading="Отчеты"
                            :active="$route.path.match('/reports')"
                        >
                            <router-link
                                v-if="user.id === 1"
                                :to="{ name: 'reports.conversion-timeline' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Долеты
                            </router-link>
                            <router-link
                                v-if="user.id === 1"
                                :to="{ name: 'reports.revenue' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Revenue
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.statistics' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Статистика
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.daily' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Daily
                            </router-link>
                            <router-link
                                v-if="['support', 'admin'].includes(user.role)"
                                :to="{ name: 'reports.daily-opt' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Daily opt
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.performance' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                FB Performance
                            </router-link>
                            <router-link
                                v-if="['verifier'].includes(user.role)"
                                :to="{ name: 'reports.facebook-performance' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                FB Performance
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.placements' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Placements
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.reach-status' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Статус прохождения
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.telegram-performance' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Telegram performance
                            </router-link>
                            <router-link
                                v-if="['admin'].includes(user.role)"
                                :to="{ name: 'reports.accounts-banned' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Accounts Banned
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.gender' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Gender
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.regions' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Regions
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.conversion' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                3 day conversion
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.os' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Lead OS
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.average-spend' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Average spend
                            </router-link>
                            <router-link
                                v-if="['admin', 'designer'].includes(user.role)"
                                :to="{ name: 'reports.designer-performance' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Designer performance
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'buyer',
                                        'support'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.lead-stats' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Lead stats
                            </router-link>
                            <router-link
                                v-if="['admin', 'support'].includes(user.role)"
                                :to="{ name: 'reports.night-shift' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Night shift
                            </router-link>
                            <router-link
                                v-if="['admin', 'support'].includes(user.role)"
                                :to="{ name: 'reports.current-rates' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Current rates
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.account-stats' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Account statistics
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.ad-disapproval-reason' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Disapproval reason
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.week-days-conversion' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Week days conversion
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'support', 'subsupport'].includes(
                                        user.role
                                    )
                                "
                                :to="{ name: 'reports.leads-received' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Leads received
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'support',
                                        'subsupport',
                                        'head'
                                    ].includes(user.role)
                                "
                                :to="{
                                    name: 'reports.lead-manager-assignments'
                                }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Выдача лидов менеджерам
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'support', 'head'].includes(
                                        user.role
                                    )
                                "
                                :to="{ name: 'reports.affiliates' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Affiliates
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.profiles-banned' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Profiles banned
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'head', 'support'].includes(
                                        user.role
                                    )
                                "
                                :to="{ name: 'reports.office-performance' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Office Performance
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'head', 'support'].includes(
                                        user.role
                                    )
                                "
                                :to="{
                                    name: 'reports.office-performance-copy'
                                }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Office Performance Copy
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'head', 'support'].includes(
                                        user.role
                                    )
                                "
                                :to="{
                                    name: 'reports.office-affiliate-performance'
                                }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Office Performance Affs
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'support', 'subsupport'].includes(
                                        user.role
                                    )
                                "
                                :to="{
                                    name: 'reports.lead-office-assignments'
                                }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Выдача лидов по офисам
                            </router-link>
                            <router-link
                                v-if="['admin', 'support'].includes(user.role)"
                                :to="{ name: 'reports.conversion-stats' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Conversion stats
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.monthly-dtd-noa' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Monthly DTD NOA
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.monthly-offices-noa' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Monthly Offices NOA
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.monthly-office-cr' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Monthly Office CR
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.monthly-offer-cr' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Monthly Offer CR
                            </router-link>
                            <router-link
                                v-if="user.role === 'admin'"
                                :to="{ name: 'reports.assignment-labels' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Assignment Labels
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'support'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.revise' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Revise
                            </router-link>
                            <router-link
                                v-if="
                                    [
                                        'admin',
                                        'head',
                                        'teamlead',
                                        'support'
                                    ].includes(user.role)
                                "
                                :to="{ name: 'reports.revise-v2' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Revise V2
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin'].includes(user.role) ||
                                        user.branch_id === 19
                                "
                                :to="{ name: 'reports.dtd-cr' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                DtD CR
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'support', 'head'].includes(user.role) ||
                                        user.branch_id === 19
                                "
                                :to="{ name: 'reports.resell-doubles' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Resell doubles
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'head'].includes(user.role)
                                "
                                :to="{ name: 'reports.lead-duplicates' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Lead duplicates
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin'].includes(user.role) ||
                                        user.branch_id === 19
                                "
                                :to="{ name: 'reports.rejected-unique' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Rejected unique
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin'].includes(user.role) ||
                                        user.branch_id === 16 && ['support', 'teamlead'].includes(user.role)
                                "
                                :to="{ name: 'reports.offer-source' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Offer & UTM source
                            </router-link>
                            <router-link
                                v-if="
                                    ['admin', 'support', 'head'].includes(user.role)
                                "
                                :to="{ name: 'reports.leftovers-by-buyers' }"
                                class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-200 focus:outline-none focus:bg-gray-200"
                                active-class="bg-gray-100"
                            >
                                Leftovers by buyers
                            </router-link>
                        </menu-dropdown>

                        <router-link
                            v-if="
                                user.role === 'admin' ||
                                    user.role === 'developer'
                            "
                            :to="{ name: 'sms.campaigns.index' }"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:border-gray-300 hover:text-teal-700"
                            active-class="text-teal-700 border-teal-500 hover:border-teal-500"
                        >
                            СМС
                        </router-link>
                        <router-link
                            v-if="
                                [
                                    'admin',
                                    'support',
                                    'subsupport',
                                    'head'
                                ].includes(user.role)
                            "
                            :to="{ name: 'leads-orders.index' }"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:border-gray-300 hover:text-teal-700"
                            active-class="text-teal-700 border-teal-500 hover:border-teal-500"
                        >
                            Заказы
                        </router-link>
                        <router-link
                            v-if="
                                user.id === 1
                                    || user.branch_id === 20
                                    || user.branch_id === 16
                                    || (user.role === 'support' && user.branch_id === 19)
                            "
                            :to="{ name: 'resell-batches.index' }"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:border-gray-300 hover:text-teal-700"
                            active-class="text-teal-700 border-teal-500 hover:border-teal-500"
                        >
                            Batches
                        </router-link>
                        <router-link
                            v-if="
                                ['admin', 'support', 'subsupport'].includes(
                                    user.role
                                )
                            "
                            :to="{ name: 'logs.payment-fails' }"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:border-gray-300 hover:text-teal-700"
                            :class="{
                                'border-teal-500 text-teal-700 hover:border-teal-500': $route.path.match(
                                    '/logs'
                                )
                            }"
                        >
                            Логи
                        </router-link>
                        <router-link
                            v-if="
                                [
                                    'admin',
                                    'support',
                                    'developer',
                                    'subsupport',
                                    'head'
                                ].includes(user.role)
                            "
                            :to="settingsPath"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 border-b-2 border-transparent hover:border-gray-300 hover:text-teal-700"
                            active-class="text-teal-700 border-teal-500 hover:border-teal-500"
                        >
                            Настройки
                        </router-link>
                    </div>
                </div>
                <profile-dropdown :user="user"></profile-dropdown>

                <div class="flex items-center -mr-2 lg:hidden">
                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="inline-flex items-center justify-center p-2 text-gray-700 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                        @click="isOpen = !isOpen"
                    >
                        <span class="sr-only">Open main menu</span>
                        <!--
                                      Icon when menu is closed.
                                      Heroicon name: outline/menu
                                      Menu open: "hidden", Menu closed: "block"
                                    -->
                        <svg
                            class="w-6 h-6"
                            :class="isOpen ? 'hidden' : 'block'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        <svg
                            class="w-6 h-6"
                            :class="isOpen ? 'block' : 'hidden'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div v-show="isOpen" id="mobile-menu" class="lg:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <router-link
                    v-if="
                        [
                            'admin',
                            'head',
                            'teamlead',
                            'buyer',
                            'support',
                            'subsupport'
                        ].includes(user.role)
                    "
                    :to="{ name: 'dashboard' }"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                    exact-active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                >
                    Рабочий стол
                </router-link>
                <router-link
                    v-if="user.role === 'admin' || user.role === 'developer'"
                    :to="{ name: 'sms.campaigns.index' }"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                    active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                >
                    СМС
                </router-link>
                <router-link
                    v-if="
                        user.role !== 'farmer' &&
                            user.role !== 'buyer' &&
                            user.role !== 'financier' &&
                            user.role !== 'verifier'
                    "
                    :to="{ name: 'leads-orders.index' }"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                    active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                >
                    Заказы
                </router-link>
                <router-link
                    v-if="
                        user.id === 1
                            || user.branch_id === 20
                            || user.branch_id === 16
                            || (user.role === 'support' && user.branch_id === 19)
                    "
                    :to="{ name: 'resell-batches.index' }"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                    active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                >
                    Batches
                </router-link>
                <router-link
                    v-if="
                        user.role !== 'verifier' &&
                            user.role !== 'support' &&
                            user.role !== 'developer' &&
                            user.role !== 'subsupport' &&
                            user.role !== 'sales'
                    "
                    :to="{ name: 'logs.payment-fails' }"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                    :class="{
                        'bg-teal-50 border-teal-500 text-teal-700 hover:border-teal-500': $route.path.match(
                            '/logs'
                        )
                    }"
                >
                    Логи
                </router-link>
                <router-link
                    v-if="
                        user.role === 'admin' ||
                            user.role === 'support' ||
                            user.role === 'developer' ||
                            user.role === 'subsupport' ||
                            user.role === 'head'
                    "
                    :to="settingsPath"
                    class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                    active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                >
                    Настройки
                </router-link>
            </div>

            <div
                v-if="
                    ['admin', 'head', 'teamlead', 'buyer', 'verifier'].includes(
                        user.role
                    )
                "
                class="pt-4 pb-3 border-t border-gray-200"
            >
                <div class="pb-2 pl-2 text-gray-500">
                    Facebook
                </div>
                <div class="space-y-1">
                    <router-link
                        :to="{ name: 'profiles.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Профили
                    </router-link>
                    <router-link
                        :to="{ name: 'accounts.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Аккаунты
                    </router-link>
                    <router-link
                        :to="{ name: 'campaigns.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Кампании
                    </router-link>
                    <router-link
                        :to="{ name: 'adsets.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Адсеты
                    </router-link>
                    <router-link
                        :to="{ name: 'accesses.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Доступы
                    </router-link>
                    <router-link
                        :to="{ name: 'profile-pages.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Страницы
                    </router-link>
                    <router-link
                        :to="{ name: 'profile-logs.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Журнал профилей
                    </router-link>
                </div>
            </div>

            <div
                v-if="
                    ['admin', 'head', 'teamlead', 'buyer', 'verifier'].includes(
                        user.role
                    )
                "
                class="pt-4 pb-3 border-t border-gray-200"
            >
                <div class="pb-2 pl-2 text-gray-500">
                    VK
                </div>
                <div class="space-y-1">
                    <router-link
                        :to="{ name: 'vk-profiles.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Профили
                    </router-link>
                </div>
            </div>

            <div
                v-if="
                    [
                        'admin',
                        'support',
                        'developer',
                        'subsupport',
                        'head'
                    ].includes(user.role)
                "
                class="pt-4 pb-3 border-t border-gray-200"
            >
                <div class="pb-2 pl-2 text-gray-500">
                    Результаты
                </div>
                <div class="space-y-1">
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'results.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Обзвон
                    </router-link>
                    <router-link
                        v-if="
                            [
                                'admin',
                                'support',
                                'developer',
                                'subsupport',
                                'head'
                            ].includes(user.role)
                        "
                        :to="{ name: 'leads.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Лиды
                    </router-link>
                    <router-link
                        v-if="
                            [
                                'admin',
                                'support',
                                'developer',
                                'subsupport',
                                'head'
                            ].includes(user.role)
                        "
                        :to="{ name: 'deposits.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Депозиты
                    </router-link>
                </div>
            </div>

            <div
                v-if="['admin', 'developer'].includes(user.role)"
                class="pt-4 pb-3 border-t border-gray-200"
            >
                <div class="pb-2 pl-2 text-gray-500">
                    Инфраструктура
                </div>
                <div class="space-y-1">
                    <router-link
                        :to="{ name: 'domains.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Домены
                    </router-link>
                    <router-link
                        :to="{ name: 'orders.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Заказы ссылок
                    </router-link>
                    <router-link
                        :to="{ name: 'servers.index' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Серверы
                    </router-link>
                </div>
            </div>

            <div
                v-if="user.role !== 'developer'"
                class="pt-4 pb-3 border-t border-gray-200"
            >
                <div class="pb-2 pl-2 text-gray-500">
                    Отчеты
                </div>
                <div class="space-y-1">
                    <router-link
                        v-if="user.id === 1"
                        :to="{ name: 'reports.revenue' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Revenue
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'buyer'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.statistics' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Статистика
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.daily' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Daily
                    </router-link>
                    <router-link
                        v-if="['support', 'admin'].includes(user.role)"
                        :to="{ name: 'reports.daily-opt' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Daily opt
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'buyer'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.performance' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        FB Performance
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'buyer'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.placements' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Placements
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.reach-status' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Статус прохождения
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.telegram-performance' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Telegram performance
                    </router-link>
                    <router-link
                        v-if="['admin'].includes(user.role)"
                        :to="{ name: 'reports.accounts-banned' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Accounts Banned
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'buyer'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.gender' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Gender
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'buyer'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.regions' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Regions
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.conversion' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        3 day conversion
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'buyer'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.os' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Lead OS
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.average-spend' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Average spend
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin' || user.role === 'designer'"
                        :to="{ name: 'reports.designer-performance' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Designer performance
                    </router-link>
                    <router-link
                        v-if="
                            [
                                'admin',
                                'head',
                                'teamlead',
                                'buyer',
                                'support'
                            ].includes(user.role)
                        "
                        :to="{ name: 'reports.lead-stats' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Lead stats
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin' || user.role === 'support'"
                        :to="{ name: 'reports.night-shift' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Night shift
                    </router-link>
                    <router-link
                        v-if="['admin', 'support'].includes(user.role)"
                        :to="{ name: 'reports.current-rates' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Current rates
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.account-stats' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Account statistics
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.ad-disapproval-reason' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Disapproval reason
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.week-days-conversion' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Week days conversion
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'support', 'subsupport'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.leads-received' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Leads received
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'support', 'subsupport', 'head'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.lead-manager-assignments' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Выдача лидов менеджерам
                    </router-link>
                    <router-link
                        v-if="['admin', 'support', 'head'].includes(user.role)"
                        :to="{ name: 'reports.affiliates' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Affiliates
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.profiles-banned' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Profiles banned
                    </router-link>
                    <router-link
                        v-if="['admin', 'head', 'support'].includes(user.role)"
                        :to="{ name: 'reports.office-performance' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Office Performance
                    </router-link>
                    <router-link
                        v-if="['admin', 'head', 'support'].includes(user.role)"
                        :to="{ name: 'reports.office-performance-copy' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Office Performance Copy
                    </router-link>
                    <router-link
                        v-if="['admin', 'head', 'support'].includes(user.role)"
                        :to="{ name: 'reports.office-affiliate-performance' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Office Performance Affs
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'support', 'subsupport'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.lead-office-assignments' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Выдача лидов по офисам
                    </router-link>
                    <router-link
                        v-if="['admin', 'support'].includes(user.role)"
                        :to="{ name: 'reports.conversion-stats' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Conversion stats
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.monthly-dtd-noa' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Monthly DTD NOA
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.monthly-offices-noa' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Monthly Offices NOA
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.monthly-office-cr' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Monthly Office CR
                    </router-link>
                    <router-link
                        v-if="user.role === 'admin'"
                        :to="{ name: 'reports.monthly-offer-cr' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Monthly Offer CR
                    </router-link>
                    <!-- <router-link
                        v-if="['admin','head','teamlead','buyer','support'].includes(user.role)"
                        :to="{ name: 'reports.assignment-labels' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Assignment Labels
                    </router-link> -->
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'support'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.revise' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Revise
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head', 'teamlead', 'support'].includes(
                                user.role
                            )
                        "
                        :to="{ name: 'reports.revise-v2' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Revise V2
                    </router-link>
                    <router-link
                        v-if="
                            ['admin'].includes(user.role) ||
                                user.branch_id === 19
                        "
                        :to="{ name: 'reports.dtd-cr' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        DtD CR
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'support', 'head'].includes(user.role) ||
                                user.branch_id === 19
                        "
                        :to="{ name: 'reports.resell-doubles' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Resell doubles
                    </router-link>
                    <router-link
                        v-if="
                            ['admin', 'head'].includes(user.role)
                        "
                        :to="{ name: 'reports.lead-duplicates' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Lead duplicates
                    </router-link>
                    <router-link
                        v-if="
                            ['admin'].includes(user.role) ||
                                user.branch_id === 19
                        "
                        :to="{ name: 'reports.rejected-unique' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Rejected unique
                    </router-link>
                    <router-link
                        v-if="
                            ['admin'].includes(user.role) ||
                                user.branch_id === 16 && ['support', 'teamlead'].includes(user.role)
                        "
                        :to="{ name: 'reports.offer-source' }"
                        class="block py-2 pl-3 pr-4 text-base font-medium text-gray-700 border-l-4 border-transparent hover:bg-gray-100 hover:border-gray-300 hover:text-gray-700"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Offer & UTM source
                    </router-link>
                </div>
            </div>

            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <img
                            class="w-10 h-10 rounded-full"
                            :src="
                                `https://eu.ui-avatars.com/api/?name=${user.name}&background=2C7A7B&color=F7FAFC`
                            "
                            alt=""
                        />
                    </div>
                    <div class="ml-3">
                        <div
                            class="text-base font-medium text-gray-800"
                            v-text="user.name"
                        ></div>
                        <div
                            class="text-sm font-medium text-gray-500"
                            v-text="user.email"
                        ></div>
                    </div>
                    <button
                        class="flex-shrink-0 p-1 ml-auto text-gray-400 bg-white rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                    >
                        <span class="sr-only">View notifications</span>
                        <!-- Heroicon name: outline/bell -->
                        <svg
                            class="w-6 h-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 space-y-1">
                    <router-link
                        v-if="user.role !== 'admin'"
                        :to="{ name: 'users.show', params: { id: user.id } }"
                        class="block px-4 py-2 text-base font-medium text-gray-700 border-l-4 border-transparent hover:text-gray-700 hover:bg-gray-100 hover:border-gray-300"
                        active-class="text-teal-700 border-teal-500 bg-teal-50 hover:border-teal-500"
                    >
                        Профиль
                    </router-link>
                    <a
                        href="/logout"
                        class="block px-4 py-2 text-base font-medium text-gray-700 border-l-4 border-transparent hover:text-gray-700 hover:bg-gray-100 hover:border-gray-300"
                        >Выйти</a
                    >
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: "application-menu",
    props: {
        user: {
            type: Object,
            default: () => null
        }
    },
    data: () => ({
        isOpen: false
    }),
    computed: {
        settingsPath() {
            if (this.user.role === "head") {
                return { name: "affiliates.index" };
            }
            return { name: "offices.index" };
        }
    },
    watch: {
        $route() {
            this.isOpen = false;
        }
    }
};
</script>
