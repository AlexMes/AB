<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Редактирование конфигурации
      </h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новая конфигурация
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
            for="office_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="office_id"
                v-model="config.office_id"
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
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="new_status"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Новый статус
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="new_status"
                v-model="config.new_status"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('new_status')"
              >
                <option
                  v-for="(status, index) in statuses"
                  :key="index"
                  :value="status"
                  v-text="status"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('new_status')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('new_status')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="statuses"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Текущие статусы
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="statuses"
                v-model="config.statuses"
                :options="statuses"
                :multiple="true"
                :close-on-select="false"
                :show-labels="false"
                placeholder="Выберите статусы"
                class="block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('statuses')"
              >
              </mutiselect>
            </div>
            <span
              v-if="errors.has('statuses')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('statuses')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="statuses_type"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Тип
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="statuses_type"
                v-model="config.statuses_type"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('statuses_type')"
              >
                <option
                  v-for="(type, index) in statuses_types"
                  :key="index"
                  :value="type.id"
                  v-text="type.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('statuses_type')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('statuses_type')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="assigned_days_ago"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Выданы N дней назад
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="assigned_days_ago"
                v-model="config.assigned_days_ago"
                type="number"
                min="1"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
                @input="errors.clear('assigned_days_ago')"
              />
            </div>
            <span
              v-if="errors.has('assigned_days_ago')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('assigned_days_ago')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Активно
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="config.enabled"></toggle>
            </div>
            <span
              v-if="errors.has('enabled')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('enabled')"
            ></span>
          </div>
        </div>
        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="cancel"
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
import ErrorBag from '../../../utilities/ErrorBag';
export default {
  name: 'status-configs-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    config: {
      office_id: null,
      new_status: null,
      statuses: [],
      statuses_type: null,
      assigned_days_ago: null,
      enabled: false,
    },
    statuses: [],
    offices: [],
    statuses_types: [
      {id: 'in', name: 'В списке выбранных'},
      {id: 'out', name: 'Кроме выбранных'},
    ],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating() {
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
      } else {
        this.config.office_id = this.$route.params.officeId || null;
      }
      this.loadStatuses();
      this.loadOffices();
    },
    load() {
      axios.get(`/api/status-configs/${this.id}`)
        .then(r => this.config = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить конфигурацию.', message: err.response.data.message}));
    },
    loadStatuses() {
      axios.get('/api/statuses')
        .then(r => this.statuses = r.data.map(status => status.name))
        .catch(err => this.$toast.error({title: 'Не удалось загрузить список статусов.', message: err.resoinse.data.message}));
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(r => this.offices = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить список офисов.', message: err.resoinse.data.message}));
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/status-configs/',this.config)
        .then(r => this.$router.back())
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить конфигурацию.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/status-configs/${this.id}`,this.config)
        .then(r => this.$router.back())
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить конфигурацию.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    cancel() {
      this.$router.back();
    },
  },
};
</script>
