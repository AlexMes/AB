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
              v-text="branch.name"
            ></h3>
          </div>
        </div>
        <div class="flex flex-shrink-0 mt-4 ml-4">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{name: 'branches.update', params: {id: id}}"
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
        </div>
      </div>
    </div>

    <div class="px-4 py-5 pb-2 bg-white border-b border-gray-200 shadow sm:px-6">
      <div class="flex flex-wrap text-sm font-medium leading-6 text-gray-400">
        <div class="flex items-center w-1/4 mb-3">
          <span class="text-gray-700">Доступ к стате:</span>
          <div
            class="w-4 h-4 ml-1 border-0 rounded-full"
            :class="[branch.stats_access ? 'bg-green-500' : 'bg-red-500']"
          ></div>
        </div>
        <div class="flex items-center w-1/4 mb-3">
          <span class="text-gray-700">Telegram ID:</span>
          <div
            class="ml-1"
            v-text="branch.telegram_id"
          ></div>
        </div>
      </div>
    </div>

    <div class="px-4 bg-white border-b border-gray-200 shadow sm:px-6">
      <div>
        <div>
          <nav class="flex -mb-px">
            <router-link
              :to="{
                name: 'branches.teams',
                params: { id: id }
              }"
              active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
              class="px-1 py-4 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Команды
            </router-link>
            <router-link
              :to="{
                name: 'branches.offices',
                params: { id: id }
              }"
              active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
              class="px-1 py-4 ml-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Офисы
            </router-link>
            <router-link
              :to="{
                name: 'branches.users',
                params: { id: id }
              }"
              active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
              class="px-1 py-4 ml-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Пользователи
            </router-link>
            <router-link
              :to="{
                name: 'branches.allowed-users',
                params: { id: id }
              }"
              active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
              class="px-1 py-4 ml-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Разрешенные пользователи
            </router-link>
            <router-link
              :to="{
                name: 'branches.black-leads',
                params: { id: id }
              }"
              active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
              class="px-1 py-4 ml-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Блеклист лидов
            </router-link>
          </nav>
        </div>
      </div>
    </div>
    <div class="">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'branches-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      branch: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/branches/${this.id}`)
        .then(r => (this.branch = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить филиал.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
