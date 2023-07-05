<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="account.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый аккаунт
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
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
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="name"
                v-model="account.name"
                type="text"
                placeholder="Account name"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
                @input="errors.clear('name')"
              />
            </div>
            <span
              v-if="errors.has('name')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('name')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="account_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Account ID
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="account_id"
                v-model="account.account_id"
                type="text"
                placeholder="Account ID"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
                @input="errors.clear('account_id')"
              />
            </div>
            <span
              v-if="errors.has('account_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('account_id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="user_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Buyer
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="user_id"
                v-model="account.user_id"
                @input="errors.clear('user_id')"
              >
                <option :value="null">
                  Не выбрано
                </option>
                <option
                  v-for="user in users"
                  :key="user.id"
                  :value="user.id"
                  v-text="user.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('user_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('user_id')"
            ></span>
          </div>
        </div>
        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="$router.push({name:'manual-accounts.index'})"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="save"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'manual-account-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    account: {
      name: null,
      account_id: null,
      user_id: null,
    },
    users: [],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      if (this.isUpdating) {
        this.load();
      }
      this.loadUsers();
    },
    load() {
      axios.get(`/api/manual-accounts/${this.id}`)
        .then(r => this.account = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить аккаунт.', message: err.response.data.message});
        });
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/manual-accounts/',this.account)
        .then(r => {
          this.$router.push({name:'manual-accounts.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить аккаунт.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/manual-accounts/${this.account.id}`,this.account)
        .then(r => this.$router.push({name:'manual-accounts.index'}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить аккаунт.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    loadUsers() {
      axios.get('/api/users', {params: {all: true, userRole: 'buyer'}})
        .then(response => {
          this.users = response.data;

          if (!this.isUpdating) {
            const user = this.users.find(user => user.id === this.$root.user.id);
            if (!!user) {
              this.account.user_id = user.id;
            }
          }
        })
        .catch(error => this.$toast.error({title: 'Could not load users.', message: error.response.data.message}));
    },
  },
};
</script>
