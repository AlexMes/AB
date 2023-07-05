<template>
  <div class="container mx-auto">
    <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
      <div
        class="flex flex-wrap items-center justify-between -mt-4 -ml-4 sm:flex-no-wrap"
      >
        <div class="mt-4 ml-4">
          <div class="flex flex-col justify-center">
            <h3
              class="text-lg font-medium leading-6 text-gray-900"
              v-text="proxy.name"
            ></h3>
          </div>
        </div>
        <div class="flex flex-shrink-0 mt-4 ml-4">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{name: 'proxies.update', params: {id: id}}"
              class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            >
              <fa-icon
                :icon="['far', 'pencil-alt']"
                class="w-5 h-5 mr-2 -ml-1 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Редактировать
              </span>
            </router-link>
          </span>

          <span class="ml-3 inline-flex rounded-md shadow-sm">
            <button
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
              @click="check"
            >
              <fa-icon
                :icon="['far', 'check']"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Проверить
              </span>
            </button>
          </span>
        </div>
      </div>
    </div>

    <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
      <div class="flex flex-wrap text-sm font-medium leading-6 text-gray-400">
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Активно:</span>
          <div
            class="w-4 h-4 ml-1 border-0 rounded-full"
            :class="[proxy.is_active ? 'bg-green-500' : 'bg-red-500']"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Протокол:</span>
          <div
            class="ml-1"
            v-text="proxy.protocol"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Хост:</span>
          <div
            class="ml-1"
            v-text="proxy.host"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Порт:</span>
          <div
            class="ml-1"
            v-text="proxy.port"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Логин:</span>
          <div
            class="ml-1"
            v-text="proxy.login"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Пароль:</span>
          <div
            class="ml-1"
            v-text="proxy.password"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Гео:</span>
          <div
            class="ml-1"
            v-text="proxy.geo"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Филиал:</span>
          <div class="ml-1">
            <span
              v-if="proxy.branch"
              v-text="proxy.branch.name"
            ></span>
            <span v-else>-</span>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="checkResult"
      class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6 sm:py-1"
    >
      <div class="sm:p-0">
        <dl>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Status
            </dt>
            <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
              <div
                class="w-4 h-4 ml-1 mt-0.5 border-0 rounded-full"
                :class="[checkResult.active ? 'bg-green-500' : 'bg-red-500']"
              ></div>
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Server IP
            </dt>
            <dd
              class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="checkResult.server_ip"
            >
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Proxy IP
            </dt>
            <dd
              class="mt-1 text-sm break-all leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="checkResult.proxy_ip"
            >
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Details
            </dt>
            <dd
              class="mt-1 text-sm break-all leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="checkResult.details"
            >
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'proxies-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      proxy: {},
      checkResult: null,
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/proxies/${this.id}`)
        .then(r => (this.proxy = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить прокси.',
            message: e.response.data.message,
          });
        });
    },
    check() {
      axios
        .post(`/api/proxies/${this.id}/check`)
        .then(r => this.checkResult = r.data)
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось выполнить проверку.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
