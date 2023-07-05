<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          <span v-if="isEditing">Редактирование аккаунта</span>
          <span v-else>Создание аккаунта</span>
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div
          v-if="errors.hasMessage()"
          class="rounded-md bg-red-100 mt-6 p-4 max-w-7xl mx-auto"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-red-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p
                class="text-sm leading-5 font-medium text-red-800"
                v-text="errors.message"
              >
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
          <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="`[${account.id}] ${account.name}`"
            >
            </h3>
          </div>
          <form class="px-6">
            <div
              class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5"
            >
              <label
                for="account_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Аккаунт ID
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="account_id"
                    v-model="account.account_id"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('account_id')"
                  />
                </div>
                <p
                  v-if="errors.has('account_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('account_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="name"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Название
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="name"
                    v-model="account.name"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('name')"
                  />
                </div>
                <p
                  v-if="errors.has('name')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('name')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="user_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Пользователь
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="user_id"
                    v-model="account.user"
                    :show-labels="false"
                    :options="users"
                    placeholder="Выберите пользователя"
                    track-by="id"
                    label="name"
                    @input="errors.clear('user_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('user_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('user_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="group_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Группы
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="group_id"
                    v-model="account.groups"
                    :show-labels="false"
                    :multiple="true"
                    :options="groups"
                    placeholder="Выберите группы"
                    track-by="id"
                    label="name"
                    @input="errors.clear('group_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('group_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('group_id')"
                ></p>
              </div>
            </div>
            <div class="mt-6 sm:mt-5 border-t border-gray-200 py-5">
              <div class="flex justify-end">
                <span class="inline-flex rounded-md shadow-sm">
                  <a
                    class="inline-flex cursor-pointer items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                    @click="cancel"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Отмена
                  </a>
                </span>
                <span class="ml-3 inline-flex rounded-md shadow-sm">
                  <button
                    type="submit"
                    class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
                    :disabled="isBusy"
                    @click.prevent="save"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Сохранить
                  </button>
                </span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import ErrorBag from '../../../../../../resources/js/utilities/ErrorBag';

export default {
  name: 'accounts-form',
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => {
    return {
      isBusy: false,
      account: {
        account_id: null,
        name: null,
        user: null,
        groups: [],
      },
      users: [],
      groups: [],
      errors: new ErrorBag(),
    };
  },
  computed: {
    isEditing() {
      return this.id !== null;
    },
    cleanForm() {
      return {
        account_id: this.account.account_id,
        name: this.account.name,
        user_id: this.account.user !== null ? this.account.user.id : null,
        group_id: this.account.groups.map(group => group.id),
      };
    },
  },
  created() {
    if (this.isEditing) {
      this.load();
    }
    this.loadUsers();
    this.loadGroups();
  },
  methods: {
    load() {
      axios.get(`/api/accounts/${this.id}`)
        .then(({data}) => this.account = data)
        .catch(err => this.$toast.error({title: 'Unable to load the account.', message: err.response.data.message}));
    },
    loadUsers() {
      axios.get('/api/users')
        .then(({data}) => {
          this.users = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load users.', message: err.response.data.message}));
    },
    loadGroups() {
      axios.get('/api/groups', {params: {all: true}})
        .then(({data}) => {
          this.groups = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load groups.', message: err.response.data.message}));
    },
    save() {
      this.isBusy = true;
      this.isEditing ? this.update() : this.create();
    },
    cancel() {
      this.isEditing
        ? this.$router.push({name:'accounts.show', params:{id: this.id}})
        : this.$router.push({name:'accounts.index'});
    },
    create() {
      axios
        .post('/api/accounts/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'accounts.index',
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Unable to create account.', message: err.response.data.message});
        }).finally(()=> this.isBusy = false);
    },
    update() {
      axios
        .put(`/api/accounts/${this.id}`, this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'accounts.show',
            params: { id: r.data.id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Unable to update account.',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
  },
};
</script>

<style scoped>

</style>
