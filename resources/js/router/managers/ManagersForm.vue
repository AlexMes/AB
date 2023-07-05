<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="manager.name"
      ></h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit.prevent="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Имя
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="name"
                v-model="manager.name"
                type="text"
                placeholder="John Doe"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
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
            for="email"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Email
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="email"
                v-model="manager.email"
                type="email"
                placeholder="manager@domain.tld"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('email')"
              />
            </div>
            <span
              v-if="errors.has('email')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('email')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="spreadsheet_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Spreadsheet
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="spreadsheet_id"
                v-model="manager.spreadsheet_id"
                type="text"
                placeholder="Ds9skqpOKw2438"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
                @input="errors.clear('spreadsheet_id')"
              />
            </div>
            <span
              v-if="errors.has('spreadsheet_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('spreadsheet_id')"
            ></span>
          </div>
        </div>
        <div
          v-if="!manager.frx_tenant_id"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="office_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="office_id"
                v-model="manager.office_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('office_id')"
              >
                <option
                  v-for="office in offices"
                  :key="office.id"
                  :value="office.id"
                  v-text="office.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('office_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('office_id')"
            ></span>
          </div>
        </div>
        <div
          v-if="!manager.frx_tenant_id"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="frx_role"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Роль
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="frx_role"
                v-model="manager.frx_role"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('frx_role')"
              >
                <option
                  v-for="role in roles"
                  :key="role.id"
                  :value="role.id"
                  v-text="role.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('frx_role')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('frx_role')"
            ></span>
          </div>
        </div>
        <div
          v-if="!isUpdating"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="password"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Пароль
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="password"
                v-model="manager.password"
                type="password"
                placeholder="******"
                class="form-input block
                            w-full transition duration-150 ease-in-out
                            sm:text-sm sm:leading-5"
              />
            </div>
          </div>
        </div>
        <div
          v-if="!isUpdating"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="password_confirmation"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Подтвердите пароль
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="password_confirmation"
                v-model="manager.password_confirmation"
                type="password"
                placeholder="******"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              />
            </div>
          </div>
        </div>
        <div class="w-full flex justify-end mt-5">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click.prevent="cancel"
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
    <form
      v-if="isUpdating && !manager.frx_tenant_id && isAdmin"
      class="bg-white flex flex-col shadow p-6 mt-6"
      @submit.prevent="resetPassword"
    >
      <h2 class="text-lg leading-6 font-medium mb-5 text-gray-900">
        Сброс пароля
      </h2>
      <div class="w-full flex-col mr-4 my-2">
        <label class="flex w-full text-gray-700 font-medium">
          Новый пароль</label>
        <input
          v-model="passwords.password"
          type="password"
          placeholder="**********"
          class="w-full border-b text-gray-700 placeholder-gray-400 my-2 px-1 py-2 "
          required
          @input="resetErrors.clear('password')"
        />
        <span
          v-if="resetErrors.has('password')"
          class="text-red-600 text-sm mt-1"
          v-text="resetErrors.get('password')"
        ></span>
      </div>
      <div class="w-full flex-col my-2">
        <label
          class="flex w-full text-gray-700 font-medium"
        >Подтвердите пароль</label>
        <input
          v-model="passwords.password_confirmation"
          placeholder="**********"
          type="password"
          class="w-full border-b text-gray-700 placeholder-gray-400 my-2 px-1 py-2"
          required
          @input="resetErrors.clear('password_confirmation')"
        />
        <span
          v-if="resetErrors.has('password_confirmation')"
          class="text-red-600 text-sm mt-1"
          v-text="resetErrors.get('password_confirmation')"
        ></span>
      </div>
      <div class="w-full flex justify-end mt-5">
        <button
          type="reset"
          class="button btn-secondary mx-2"
        >
          Отмена
        </button>
        <button
          type="submit"
          class="button btn-primary mx-2"
          @click.prevent="resetPassword"
        >
          Сбросить пароль
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'managers-form',
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => ({
    isBusy: false,
    manager: {
      email: null,
      name: null,
      spreadsheet_id: null,
      office_id: null,
      frx_tenant_id: null,
      frx_role: 'closer',
      password: null,
      password_confirmation: null,
    },
    passwords: {
      password: null,
      password_confirmation: null,
    },
    errors: new ErrorBag(),
    resetErrors: new ErrorBag(),
    roles: [
      { id: 'root', name: 'Root' },
      { id: 'admin', name: 'Администратор' },
      { id: 'closer', name: 'Клоузер' },
      { id: 'agent', name: 'Оператор' },
    ],
    offices: [],
  }),
  computed: {
    isUpdating() {
      return this.$props.id !== null;
    },
    isAdmin() {
      return this.$root.user.role === 'admin';
    },
    cleanForm() {
      return {
        email: this.manager.email,
        name: this.manager.name,
        spreadsheet_id: this.manager.spreadsheet_id,
        office_id: this.manager.office_id,
        is_frx: this.manager.frx_tenant_id !== null,
        frx_role: this.manager.frx_role,
        password: this.manager.password,
        password_confirmation: this.manager.password_confirmation,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      if (this.isUpdating) {
        this.load();
        this.loadOffices();
      }
    },
    load() {
      axios
        .get(`/api/managers/${this.id}`)
        .then(r => (this.manager = r.data))
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить менеджера',
            message: err.response.data.message,
          });
        });
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios
        .post('/api/managers/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'offices.managers',
            params: { id: r.data.office_id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось сохранить менеджера',
            message: err.response.data.message,
          });
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios
        .put(`/api/managers/${this.manager.id}`, this.cleanForm)
        .then(r =>
          this.$router.push({
            name: 'offices.managers',
            params: { id: r.data.office_id },
          }),
        )
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось обновить менеджера',
            message: err.response.data.message,
          });
        })
        .finally(() => this.isBusy = false);
    },
    resetPassword() {
      axios
        .put(
          `/api/managers/${this.manager.id}/password`,
          this.passwords,
        )
        .then(() =>
          this.$router.push({
            name: 'offices.managers',
            params: { id: this.manager.office_id },
          }),
        )
        .catch(errors => {
          if (errors.response.status === 422) {
            return this.resetErrors.fromResponse(errors);
          }
          this.$toast.error({
            title: 'Не удалось сбросить пароль',
            message: errors.response.data.message,
          });
        });
    },
    cancel() {
      this.$router.push({
        name: 'offices.managers',
        params: { id: this.manager.office_id },
      });
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(r => this.offices = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить офисы.', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped></style>
