<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <div
        class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap"
      >
        <div class="ml-4 mt-4">
          <div class="flex flex-col justify-center">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="offer.name"
            ></h3>
          </div>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0 flex z-10">
          <span class="relative z-0 inline-flex shadow-sm rounded-md">
            <router-link
              :to="{name: 'offers.update', params: {id: id}}"
              class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
            >
              <fa-icon
                :icon="['far', 'pencil-alt']"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Редактировать
              </span>
            </router-link>
            <span class="-ml-px relative block">
              <button
                type="button"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                aria-label="Expand"
                @click.prevent="isEditMenuOpen = !isEditMenuOpen"
              >
                <svg
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
              <!--
            Dropdown panel, show/hide based on dropdown state.

            Entering: "transition ease-out duration-100"
              From: "transform opacity-0 scale-95"
              To: "transform opacity-100 scale-100"
            Leaving: "transition ease-in duration-75"
              From: "transform opacity-100 scale-100"
              To: "transform opacity-0 scale-95"
          -->
              <div
                class="origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg"
                :class="{hidden: !isEditMenuOpen}"
              >
                <div class="rounded-md bg-white shadow-xs">
                  <div class="py-1">
                    <a
                      href="#"
                      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                      @click.prevent="startOrder"
                    >
                      <fa-icon
                        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                        :icon="['far', 'play']"
                        fixed-width
                      ></fa-icon>
                      Запустить
                    </a>
                    <a
                      href="#"
                      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                      @click.prevent="pauseOrder"
                    >
                      <fa-icon
                        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                        :icon="['far', 'pause']"
                        fixed-width
                      ></fa-icon>
                      Приостановить
                    </a>
                    <a
                      href="#"
                      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                      @click.prevent="stopOrder"
                    >
                      <fa-icon
                        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                        :icon="['far', 'stop']"
                        fixed-width
                      ></fa-icon>
                      Остановить
                    </a>
                  </div>
                </div>
              </div>
            </span>
          </span>
        </div>
      </div>
    </div>

    <div class="px-4 py-5 pb-2 bg-white border-b border-gray-200 shadow sm:px-6">
      <div class="flex flex-wrap text-sm font-medium leading-6 text-gray-400">
        <div class="flex items-center w-1/2 mb-3">
          <span class="text-gray-700">UUID:</span>
          <div
            class="ml-1"
            v-text="offer.uuid"
          ></div>
        </div>
        <div class="w-1/4 mb-4">
          <span class="text-gray-700">Разрешить дубли </span>
          <span
            v-if="offer.allow_duplicates"
            class="w-4 h-4 -mb-1 inline-block bg-green-500 rounded-full"
          ></span>
          <span
            v-else
            class="w-4 h-4 -mb-1 inline-block bg-red-500 rounded-full"
          ></span>
        </div>
      </div>
    </div>

    <div class="bg-white shadow px-4 border-b border-gray-200 sm:px-6">
      <nav class="-mb-px flex">
        <router-link
          :to="{
            name: 'offers.allowed-users',
            params: { id: id }
          }"
          active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
          class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
        >
          Разрешенные пользователи
        </router-link>
      </nav>
    </div>
    <div class="">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'offers-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      offer:{},
      isEditMenuOpen: false,
    };
  },
  created(){
    this.load();
  },
  methods: {
    load(){
      axios.get(`/api/offers/${this.id}`)
        .then(r => this.offer = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить офер.', message: e.response.data.message}));
    },
    startOrder() {
      axios.post(`/api/offers/${this.id}/start-lead-order-routes`)
        .then(r => this.$toast.success({title: 'Successful', message: 'Offer\'s routes have been started.'}))
        .catch(e => this.$toast.error({title: 'Couldn\'t start offer\'s routes.', message: e.response.data.message}));
    },
    pauseOrder() {
      axios.post(`/api/offers/${this.id}/pause-lead-order-routes`)
        .then(r => this.$toast.success({title: 'Successful', message: 'Offer\'s routes have been paused.'}))
        .catch(e => this.$toast.error({title: 'Couldn\'t pause offer\'s routes.', message: e.response.data.message}));
    },
    stopOrder() {
      axios.post(`/api/offers/${this.id}/stop-lead-order-routes`)
        .then(r => this.$toast.success({title: 'Successful', message: 'Offer\'s routes have been stopped.'}))
        .catch(e => this.$toast.error({title: 'Couldn\'t stop offer\'s routes.', message: e.response.data.message}));
    },
  },
};
</script>
