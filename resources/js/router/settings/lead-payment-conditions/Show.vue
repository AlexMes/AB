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
              v-text="`#${condition.id}`"
            ></h3>
          </div>
        </div>
        <div class="flex flex-shrink-0 mt-4 ml-4">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{name: 'lead-payment-conditions.update', params: {id: id}}"
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
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Офис:</span>
          <div
            class="ml-1"
            v-text="condition.office.name"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Офер:</span>
          <div
            class="ml-1"
            v-text="condition.offer.name"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Модель:</span>
          <div
            class="ml-1"
            v-text="condition.model"
          ></div>
        </div>
        <div
          class="flex items-center w-1/4 mb-3"
        >
          <span class="text-gray-700">Цена:</span>
          <div
            class="ml-1"
            v-text="condition.cost"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'lead-payment-condition-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      condition: {
        office: {},
        offer: {},
      },
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/lead-payment-conditions/${this.id}`)
        .then(r => (this.condition = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить условие.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
