<template>
  <modal
    name="add-office-manager-modal"
    height="auto"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          class="flex w-full mb-2 font-semibold"
          for="managerName"
        >Имя менеджера</label>
        <input
          id="managerName"
          v-model="manager.name"
          type="text"
          placeholder="Bob"
          class="border-b text-gray-700 placeholder-gray-500 mt-2 px-1 py-2 mx-3"
          @input="errors.clear('name')"
        />
        <span
          v-if="errors.has('name')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('name')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          class="flex w-full mb-2 font-semibold"
          for="managerEmail"
        >Почта</label>
        <input
          id="managerEmail"
          v-model="manager.email"
          type="text"
          placeholder="bob@mail.ru"
          class="border-b text-gray-700 placeholder-gray-500 mt-2 px-1 py-2 mx-3"
          @input="errors.clear('email')"
        />
        <span
          v-if="errors.has('email')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('email')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          class="flex w-full mb-2 font-semibold"
          for="managerSpreadsheet"
        >Spreadsheet</label>
        <input
          id="managerSpreadsheet"
          v-model="manager.spreadsheet_id"
          type="text"
          placeholder="Ds9skqpOKw2438"
          class="border-b text-gray-700 placeholder-gray-500 mt-2 px-1 py-2 mx-3"
          @input="errors.clear('spreadsheet_id')"
        />
        <span
          v-if="errors.has('spreadsheet_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('spreadsheet_id')"
        ></span>
      </div>
      <div
        v-if="$root.user.role === 'admin'"
        class="flex flex-col w-full mb-4"
      >
        <label
          class="flex w-full mb-2 font-semibold"
          for="managerRole"
        >Роль</label>
        <select
          id="managerRole"
          v-model="manager.frx_role"
          class="text-gray-700 mt-2 px-1 py-2 mx-3 w-auto"
          @change="errors.clear('frx_role')"
        >
          <option :value="null">
            Не выбрано
          </option>
          <option
            v-for="(value, key) in roles"
            :key="key"
            :value="key"
            v-text="value"
          >
            Админ
          </option>
        </select>
        <span
          v-if="errors.has('frx_role')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('frx_role')"
        ></span>
      </div>
      <div
        v-if="$root.user.role === 'admin'"
        class="flex flex-col w-full mb-4"
      >
        <label
          class="flex w-full mb-2 font-semibold"
          for="managerPassword"
        >Пароль</label>
        <input
          id="managerPassword"
          v-model="manager.password"
          type="password"
          autocomplete="new-password"
          class="border-b text-gray-700 placeholder-gray-500 mt-2 px-1 py-2 mx-3"
          @input="errors.clear('password')"
        />
        <span
          v-if="errors.has('password')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('password')"
        ></span>
      </div>
      <div
        v-if="$root.user.role === 'admin'"
        class="flex flex-col w-full mb-4"
      >
        <label
          class="flex w-full mb-2 font-semibold"
          for="managerPasswordConfirmation"
        >Подверждение пароля</label>
        <input
          id="managerPasswordConfirmation"
          v-model="manager.password_confirmation"
          type="password"
          class="border-b text-gray-700 placeholder-gray-500 mt-2 px-1 py-2 mx-3"
        />
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="addManager"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('add-office-manager-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'add-office-manager-modal',
  props: {
    officeId: {
      type: [String, Number],
      required: true,
    },
  },
  data: () => ({
    isBusy: false,
    manager: {
      name: null,
      email: null,
      spreadsheet_id: null,
      frx_role: null,
      password: null,
      password_confirmation: null,
    },
    roles: {
      root: 'Root',
      admin: 'Администратор',
      closer: 'Клоузер',
      agent: 'Оператор',
    },
    errors: new ErrorBag(),
  }),
  computed: {
    cleanForm() {
      let data = {
        name: this.manager.name,
        email: this.manager.email,
        spreadsheet_id: this.manager.spreadsheet_id,
      };

      if (this.manager.frx_role !== null) {
        data.frx_role = this.manager.frx_role;
        data.password = this.manager.password;
        data.password_confirmation = this.manager.password_confirmation;
      }

      return data;
    },
  },
  methods: {
    addManager() {
      this.isBusy = true;
      axios
        .post(`/api/offices/${this.officeId}/managers`, this.cleanForm)
        .then(response => {
          this.$toast.success({title: 'Success', message: 'Manager has been added.'});
          this.$modal.hide('add-office-manager-modal');
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors.fromResponse(error);
          }
          this.$toast.error({
            title: 'Error',
            message: 'Cant add manager now',
          });
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

