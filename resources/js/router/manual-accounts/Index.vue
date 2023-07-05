<template>
  <div class="container mx-auto">
    <div class="flex flex-col">
      <div class="mb-8 flex w-full justify-between">
        <h1 class="text-gray-700">
          Аккаунты
        </h1>
        <div class="flex">
          <div class="flex flex-1">
            <search-field @search="search"></search-field>
          </div>
          <router-link
            :to="{'name':'manual-accounts.create'}"
            class="button btn-primary flex items-center ml-3"
          >
            <fa-icon
              :icon="['far','plus']"
              class="fill-current mr-2"
            ></fa-icon> Добавить
          </router-link>
        </div>
      </div>
      <div
        v-if="$root.user.role === 'admin' "
        class="mt-4 mb-8 flex flex-wrap"
      >
        <div
          v-if="$root.user.role === 'admin' "
          class="flex flex-col mr-4 my-2 xl:my-0"
        >
          <label
            for="userFilter"
            class="mb-2"
          >Пользователь</label>
          <select
            id="userFilter"
            v-model="filters.user_id"
          >
            <option :value="null">
              Все
            </option>
            <option
              v-for="user in users"
              :key="user.id"
              :value="user.id"
              v-text="user.name"
            ></option>
          </select>
        </div>
        <div class="flex flex-col mr-4 my-2 md:my-0">
          <button
            type="button"
            class="button btn-secondary mt-6"
            @click="load(1)"
          >
            <fa-icon
              :icon="['far','filter']"
              class="fill-current mr-2"
              fixed-width
            ></fa-icon>Фильтровать
          </button>
        </div>
      </div>
      <div
        v-if="hasAccounts"
      >
        <div class="flex flex-col">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Id
                      </th>
                      <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Account ID
                      </th><th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Name
                      </th>
                      <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                        Buyer
                      </th>
                      <th class="px-6 py-3 bg-gray-50"></th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <manual-account-list-item
                      v-for="account in accounts"
                      :key="account.id"
                      :account="account"
                    ></manual-account-list-item>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <pagination
          :response="response"
          @load="load"
        ></pagination>
      </div>
      <div
        v-else
        class="text-center p-4"
      >
        <h2>Аккаунтов не найдено</h2>
      </div>
    </div>
  </div>
</template>

<script>
import ManualAccountListItem from '../../components/manual-accounts/manual-account-list-item';
export default {
  name: 'manual-accounts-index',
  components: {ManualAccountListItem},
  data:() => ({
    accounts: {},
    response: {},
    needle: null,
    filters: {
      user_id: null,
    },
    users: [],
  }),
  computed:{
    hasAccounts(){
      return this.accounts !== undefined && this.accounts.length > 0;
    },
    cleanFilters() {
      return {
        search: this.needle === '' ? null : this.needle,
        user_id: this.filters.user_id,
      };
    },
  },
  created(){
    this.load();
    this.loadUsers();
  },
  methods:{
    load(page = 1) {
      axios.get('/api/manual-accounts', {params: {...this.cleanFilters, page}})
        .then(response => {
          this.accounts = response.data.data;
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить аккаунты.', message: err.response.data.message});
        });
    },
    search(needle) {
      this.needle = needle;
      this.load();
    },
    loadUsers() {
      axios.get('/api/users', {params: {all: true, userRole: 'buyer'}})
        .then(response => this.users = response.data)
        .catch(error => this.$toast.error({title: 'Could not load users.', message: error.response.data.message}));
    },
  },
};
</script>
