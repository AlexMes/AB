<template>
  <nav
    class="bg-gray-800"
  >
    <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <img
              class="w-8 h-8"
              src="https://tailwindui.com/img/logos/v1/workflow-mark-on-white.svg"
              alt="Workflow logo"
            />
          </div>
          <div class="hidden md:block">
            <div class="flex items-baseline ml-10">
              <router-link
                :to="{ name: 'dashboard' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Рабочий стол
              </router-link>
              <router-link
                :to="{ name: 'applications.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Приложения
              </router-link>
              <router-link
                :to="{ name: 'accounts.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Аккаунты
              </router-link>
              <router-link
                :to="{ name: 'campaigns.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Кампании
              </router-link>
              <router-link
                :to="{ name: 'insights.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Статистика
              </router-link>
              <router-link
                :to="{ name: 'tech-costs.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Тех. расходы
              </router-link>
              <router-link
                v-if="user.role !== 'gambler'"
                :to="{ name: 'offers.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Оферы
              </router-link>
              <router-link
                v-if="user.role !== 'gambler'"
                :to="{ name: 'groups.index' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Группы
              </router-link>
              <router-link
                :to="{ name: 'reports.performance' }"
                class="px-3 py-2 rounded-md text-sm font-medium   text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
                active-class="text-white bg-gray-900"
              >
                Отчет
              </router-link>
            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="flex items-center ml-4 md:ml-6">
            <!-- Profile dropdown -->
            <div
              class="relative ml-3"
            >
              <div>
                <button
                  id="user-menu"
                  class="flex items-center max-w-xs text-sm text-white rounded-full focus:outline-none focus:shadow-solid"
                  aria-label="User menu"
                  aria-haspopup="true"
                  @click="open = !open"
                >
                  <span class="inline-block w-8 h-8 overflow-hidden bg-gray-100 rounded-full">
                    <svg
                      class="w-full h-full text-gray-300"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
                      />
                    </svg>
                  </span>
                </button>
              </div>
              <transition
                enter-active-class="transition ease-out duration-150"
                enter-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <div
                  v-if="open"
                  class="absolute right-0 w-auto mt-2 origin-top-right rounded-md shadow-lg"
                >
                  <div class="py-1 bg-white rounded-md shadow-xs">
                    <div class="px-4 py-3">
                      <p
                        class="text-xs font-semibold leading-5 text-gray-900"
                        v-text="user.name"
                      >
                      </p>
                      <p
                        class="text-xs font-normal leading-5 text-gray-600"
                        v-text="user.email"
                      >
                      </p>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <a
                      href="/logout"
                      class="flex items-center px-4 py-2 text-sm leading-5 text-gray-700 group hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                    >
                      <svg
                        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500 group-focus:text-gray-500"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                        />
                      </svg>
                      Выйти
                    </a>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>
        <div class="flex -mr-2 md:hidden">
          <!-- Mobile menu button -->
          <button
            class="inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white"
            :aria-label="open ? 'Close main menu' : 'Main menu'"
            :aria-expanded="open"
            @click="open = !open"
          >
            <svg
              :class="{ 'hidden': open, 'block': !open }"
              class="block w-6 h-6"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
            <svg
              :class="{ 'hidden': !open, 'block': open }"
              class="w-6 h-6"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
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

    <div
      :class="{ 'block': open, 'hidden': !open }"
      class="md:hidden"
    >
      <div class="px-2 pt-2 pb-3 sm:px-3">
        <router-link
          :to="{ name: 'dashboard' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Рабочий стол
        </router-link>
        <router-link
          :to="{ name: 'applications.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Приложения
        </router-link>
        <router-link
          :to="{ name: 'accounts.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Аккаунты
        </router-link>
        <router-link
          :to="{ name: 'campaigns.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Кампании
        </router-link>
        <router-link
          :to="{ name: 'insights.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Статистика
        </router-link>
        <router-link
          :to="{ name: 'tech-costs.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Тех. расходы
        </router-link>
        <router-link
          v-if="user.role !== 'gambler'"
          :to="{ name: 'offers.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Оферы
        </router-link>
        <router-link
          v-if="user.role !== 'gambler'"
          :to="{ name: 'groups.index' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Группы
        </router-link>
        <router-link
          :to="{ name: 'reports.performance' }"
          class="block px-3 py-2 rounded-md text-base font-medium   text-gray-300 focus:outline-none focus:text-white focus:bg-gray-700"
          active-class="text-white bg-gray-900"
        >
          Отчет
        </router-link>
      </div>
      <div class="pt-4 pb-3 border-t border-gray-700">
        <div class="flex items-center px-5">
          <div class="flex-shrink-0">
            <img
              class="w-10 h-10 rounded-full"
              src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80"
              alt=""
            />
          </div>
          <div class="ml-3">
            <div
              class="text-base font-medium leading-none text-white"
              v-text="user.name"
            >
            </div>
            <div
              class="mt-1 text-sm font-medium leading-none text-gray-400"
              v-text="user.email"
            >
            </div>
          </div>
        </div>
        <div
          class="px-2 mt-3"
          role="menu"
          aria-orientation="vertical"
          aria-labelledby="user-menu"
        >
          <a
            href="/logout"
            class="block px-3 py-2 mt-1 text-base font-medium text-gray-400 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700"
            role="menuitem"
          >Выйти</a>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'application-menu',
  props: {
    user: {
      type: Object,
      default: () => null,
    },
  },
  data: () => ({
    open: false,
  }),
};
</script>
