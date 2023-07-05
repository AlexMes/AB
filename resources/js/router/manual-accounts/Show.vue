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
              v-text="account.name"
            ></h3>
            <div
              class="text-xs leading-6 font-medium text-gray-400"
              v-text="account.account_id"
            >
            </div>
          </div>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0 flex">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{name: 'manual-accounts.update', params: {id: id}}"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
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
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'manual-account-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      account: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/manual-accounts/${this.id}`)
        .then(r => (this.account = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить аккаунт.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
