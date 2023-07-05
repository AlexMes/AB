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
              v-text="destination.name"
            ></h3>
          </div>
        </div>

        <div class="flex mt-4 ml-4">
          <span class="relative z-0 inline-flex shadow-sm rounded-md">
            <router-link
              :to="{name: 'leads-destinations.update', params: {id: id}}"
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
                      v-if="['admin', 'support'].includes($root.user.role) && destination.is_active"
                      href="#"
                      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                      @click.prevent="collectStatuses"
                    >
                      <fa-icon
                        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                        :icon="['far', 'ballot-check']"
                        fixed-width
                      ></fa-icon>
                      Собрать статусы
                    </a>
                    <a
                      v-if="['admin', 'support'].includes($root.user.role) && destination.is_active"
                      href="#"
                      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                      @click.prevent="$modal.show('collect-lead-destination-results-modal', {destination: destination})"
                    >
                      <fa-icon
                        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                        :icon="['far', 'hands-usd']"
                        fixed-width
                      ></fa-icon>
                      Собрать депозиты
                    </a>
                    <a
                      v-if="['admin', 'support'].includes($root.user.role) && destination.is_active"
                      href="#"
                      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                      @click.prevent="$modal.show('test-lead-destination-modal', {destination: destination})"
                    >
                      <fa-icon
                        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                        :icon="['far', 'file-check']"
                        fixed-width
                      ></fa-icon>
                      Отправить тест
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
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Активно:</span>
          <div
            class="w-4 h-4 ml-1 border-0 rounded-full"
            :class="[destination.is_active ? 'bg-green-500' : 'bg-red-500']"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Драйвер:</span>
          <div
            class="ml-1"
            v-text="destination.driver"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Автологин:</span>
          <div
            class="ml-1"
            v-text="destination.autologin"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Филиал:</span>
          <div class="ml-1">
            <span
              v-if="destination.branch"
              v-text="destination.branch.name"
            ></span>
            <span v-else>-</span>
          </div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Офис:</span>
          <div class="ml-1">
            <span
              v-if="destination.office"
              v-text="destination.office.name"
            ></span>
            <span v-else>-</span>
          </div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Автологин ленда:</span>
          <div
            class="w-4 h-4 ml-1 border-0 rounded-full"
            :class="[destination.land_autologin ? 'bg-green-500' : 'bg-red-500']"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Уведомления о депах:</span>
          <div
            class="w-4 h-4 ml-1 border-0 rounded-full"
            :class="[destination.deposit_notification ? 'bg-green-500' : 'bg-red-500']"
          ></div>
        </div>
      </div>
    </div>
    <collect-lead-destination-results-modal></collect-lead-destination-results-modal>
    <test-lead-destination-modal></test-lead-destination-modal>
  </div>
</template>

<script>
import CollectLeadDestinationResultsModal from '../../../components/settings/collect-lead-destination-results-modal';
import TestLeadDestinationModal from '../../../components/settings/test-lead-destination-modal';

export default {
  name: 'leads-destinations-show',
  components: {CollectLeadDestinationResultsModal, TestLeadDestinationModal},
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      isEditMenuOpen: false,
      destination: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/leads-destinations/${this.id}`)
        .then(r => (this.destination = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить дестинейшн.',
            message: e.response.data.message,
          });
        });
    },
    collectStatuses() {
      axios
        .post(`/api/leads-destinations/${this.id}/collect-statuses`)
        .then(r => (this.$toast.success({title: 'Ok', message: 'Сбор статусов запущен.'})))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось запустить сбор статусов.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
