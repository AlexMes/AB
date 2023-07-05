<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          Детали аккаунта
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8 max-w-7xl mx-auto">
          <div class="px-4 py-5 border-b border-gray-200 flex justify-between items-center sm:px-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="`[${account.id}] ${account.name}`"
            >
            </h3>

            <div>
              <router-link :to="{name: 'accounts.update', params: {id}}">
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
                  Аккаунт ID
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    class="inline-block"
                    v-text="account.account_id"
                  ></span>
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Название
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    class="inline-block"
                    v-text="account.name"
                  ></span>
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Пользователь
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    v-if="account.user_id"
                    class="inline-block"
                    v-text="account.user.name"
                  ></span>
                  <span v-else>-</span>
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Группы
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    class="inline-block"
                    v-text="groupsList"
                  ></span>
                </dd>
              </div>
              <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
              >
                <dt class="text-sm leading-5 font-medium text-gray-500">
                  Дата и время создания
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                  <span
                    class="inline-block"
                    v-text="createdAt"
                  ></span>
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'accounts-show',
  props: {
    id: {
      type: [String, Number],
      required: true,
    },
  },
  data: () => {
    return {
      account: {},
    };
  },
  computed: {
    createdAt() {
      return moment(this.account.created_at).format('DD.MM.YYYY HH:mm:ss');
    },
    groupsList() {
      return !!this.account.groups ? this.account.groups.map(group => group.name).join(', ') : '';
    },
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios.get(`/api/accounts/${this.id}`)
        .then(({data}) => this.account = data)
        .catch(err => this.$toast.error({title: 'Unable to load the account.', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>

</style>
