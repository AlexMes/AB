<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          Детали приложения
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8 max-w-7xl mx-auto">
          <div class="px-4 py-5 border-b border-gray-200 flex justify-between items-center sm:px-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="`[${application.id}] ${application.name}`"
            >
            </h3>

            <div>
              <router-link :to="{name: 'applications.update', params: {id}}">
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                  <svg
                    class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                  </svg>
                  <span>
                    Редактировать
                  </span>
                </span>
              </router-link>
            </div>
          </div>
          <div class="px-4 py-5 sm:p-0">
            <dl>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Название
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    class="inline-block"
                    v-text="application.name"
                  ></span>
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Статус WebView
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    class="inline-flex px-2 py-2 rounded-full"
                    :class="application.enabled ? 'bg-green-500' : 'bg-red-500'"
                  ></span>
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Play Market ID
                </dt>
                <dd
                  class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                  v-text="application.market_id"
                >
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  URL
                </dt>
                <dd
                  class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                  v-text="application.url"
                >
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  GEO
                </dt>
                <dd
                  class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                  v-text="application.geo"
                >
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <div class="mt-4">
          <div class="block">
            <nav class="flex">
              <router-link
                :to="{
                  name: 'applications.links',
                  params: { id: id }
                }"
                active-class="text-indigo-700 bg-indigo-100 focus:text-indigo-800 focus:bg-indigo-200"
                class="px-3 py-2 font-medium text-sm leading-5 rounded-md focus:outline-none   text-gray-500 hover:text-gray-700 focus:text-indigo-600 focus:bg-indigo-50"
              >
                Ссылки
              </router-link>
            </nav>
            <router-view></router-view>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'applications-show',
  props: {
    id: {
      type: [String, Number],
      required: true,
    },
  },
  data: () => {
    return {
      application: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios.get(`/api/applications/${this.id}`)
        .then(({data}) => this.application = data)
        .catch(err => this.$toast.error({title: 'Unable to load the application.', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>

</style>
