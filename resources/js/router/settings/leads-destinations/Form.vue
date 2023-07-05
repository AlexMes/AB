<template>
  <div class="container mx-auto">
    <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg font-medium leading-6 text-gray-900"
        v-text="destination.name"
      ></h3>
      <h3
        v-else
        class="text-lg font-medium leading-6 text-gray-900"
      >
        Новый дестинейшн
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="p-3 my-4 text-white bg-red-700 rounded"
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
                v-model="destination.name"
                type="text"
                placeholder=""
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('name')"
              />
            </div>
            <span
              v-if="errors.has('name')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('name')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="driver"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Драйвер
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="destination.driver"
                :show-labels="false"
                :multiple="false"
                :options="drivers"
                placeholder="Выберите драйвер"
                track-by="id"
                label="name"
              ></mutiselect>
              <!--              <input
                id="driver"
                v-model="destination.driver"
                type="text"
                placeholder=""
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('driver')"
              />-->
            </div>
            <span
              v-if="errors.has('driver')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('driver')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="autologin"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Автологин
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="autologin"
                v-model="destination.autologin"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                @change="errors.clear('autologin')"
              >
                <option :value="null">
                  Не выбрано
                </option>
                <option value="on">
                  On
                </option>
                <option value="off">
                  Off
                </option>
                <option value="optional">
                  Optional
                </option>
              </select>
            </div>
            <span
              v-if="errors.has('autologin')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('autologin')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >Филиал</label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="destination.branch"
                :show-labels="false"
                :multiple="false"
                :options="branches"
                placeholder="Выберите филиал"
                track-by="id"
                label="name"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('branch_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('branch_id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >Офис</label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="destination.office"
                :show-labels="false"
                :multiple="false"
                :options="offices"
                placeholder="Выберите офис"
                track-by="id"
                label="name"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('office_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('office_id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="configuration"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Конфиг
            <span @click="loadLeadDestinationConfigInformation()">
              <fa-icon
                :icon="addQuestion"
                class="text-teal-700 cursor-pointer fill-current"
                fixed-width
              ></fa-icon>
            </span>
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div
              v-if="this.example_configuration"
              class="my-2 sm:mt-0 sm:col-span-2"
            >
              <div>Required:</div>
              <div
                v-for="(item, index) in example_configuration.required"
                :key="`required_${index}`"
                class="block mt-1 text-red-600"
              >
                {{ index }}
              </div>
              <div class="mt-4">
                Not required:
              </div>
              <div
                v-for="(item, index) in example_configuration.not_required"
                :key="`not_required_${index}`"
                class="flex align-items-center items-center"
              >
                <div class="block mr-1 mt-1  text-green-600">
                  {{ index }}:
                </div>
                <div class="ml-2">
                  {{ item ? item : 'null' }}
                </div>
              </div>
            </div>
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="configuration"
                v-model="destination.configuration"
                placeholder=""
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                rows="5"
                @input="errors.clear('configuration')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('configuration')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('configuration')"
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
              <toggle v-model="destination.is_active"></toggle>
            </div>
            <span
              v-if="errors.has('is_active')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('is_active')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Отсылать автологин на ленд
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="destination.land_autologin"></toggle>
            </div>
            <span
              v-if="errors.has('land_autologin')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('land_autologin')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Уведомления о депах
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="destination.deposit_notification"></toggle>
            </div>
            <span
              v-if="errors.has('deposit_notification')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('deposit_notification')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <div class="flex justify-between sm:mt-px sm:pt-2">
            <label
              class="inline-block text-sm font-medium leading-5 text-gray-700"
            >
              Мапа статусов
            </label>
            <span @click="addStatusMapItem">
              <fa-icon
                :icon="addIcon"
                class="text-teal-700 cursor-pointer fill-current"
                fixed-width
              ></fa-icon>
            </span>
          </div>
          <div class="mt-1 space-y-2 sm:mt-0 sm:col-span-2">
            <div
              v-for="(item, index) in destination.status_map"
              :key="`status_map_${index}`"
              class="flex justify-between max-w-lg rounded-md"
            >
              <div class="w-5/12">
                <input
                  v-model="item.external"
                  type="text"
                  placeholder=""
                  class="w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                  required
                  maxlength="50"
                  @input="
                    errors.clear(
                      `status_map.${index}.external`
                    )
                  "
                />
                <span
                  v-if="
                    errors.has(
                      `status_map.${index}.external`
                    )
                  "
                  class="block mt-1 text-sm text-red-600"
                  v-text="
                    errors.get(
                      `status_map.${index}.external`
                    )
                  "
                ></span>
              </div>
              <div class="w-5/12">
                <mutiselect
                  v-model="item.internal"
                  :show-labels="false"
                  :allow-empty="true"
                  :options="statuses"
                  placeholder="Выберите статус"
                  @input="
                    errors.clear(
                      `status_map.${index}.internal`
                    )
                  "
                ></mutiselect>
                <span
                  v-if="
                    errors.has(
                      `status_map.${index}.internal`
                    )
                  "
                  class="block mt-1 text-sm text-red-600"
                  v-text="
                    errors.get(
                      `status_map.${index}.internal`
                    )
                  "
                ></span>
              </div>
              <div class="w-1/12 mt-3">
                <a
                  @click="
                    destination.status_map.splice(index, 1)
                  "
                >
                  <fa-icon
                    :icon="removeIcon"
                    class="text-red-700 cursor-pointer fill-current"
                    fixed-width
                  ></fa-icon>
                </a>
              </div>
            </div>
            <span
              v-if="errors.has('status_map')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('status_map')"
            ></span>
          </div>
        </div>
        <div class="flex justify-end w-full pt-4 mt-6 border-t">
          <button
            type="reset"
            class="mx-2 button btn-secondary"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="mx-2 button btn-primary"
            :disabled="isBusy"
            @click.prevent="save"
          >
            <span v-if="isBusy">
              <fa-icon
                :icon="['far', 'spinner']"
                class="fill-current"
                spin
                fixed-width
              ></fa-icon>
              Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';
import {
  faMinusCircle,
  faPlusCircle, faQuestion,
} from '@fortawesome/pro-regular-svg-icons';
export default {
  name: 'leads-destinations-form',
  props: {
    id: {
      type: [Number, String],
      required: false,
      default: null,
    },
  },
  data: () => ({
    isBusy: false,
    destination: {
      name: null,
      driver: null,
      autologin: 'off',
      configuration: null,
      branch: null,
      office: null,
      status_map: [],
      is_active: true,
      land_autologin: true,
      deposit_notification: false,
    },
    drivers: [],
    branches: [],
    offices: [],
    statuses: [],
    example_configuration: null,
    errors: new ErrorBag(),
  }),
  computed: {
    isUpdating() {
      return this.id !== null && this.id !== undefined;
    },
    cleanForm() {
      return {
        name: this.destination.name,
        driver: !!this.destination.driver
          ? this.destination.driver.id
          : null,
        autologin: this.destination.autologin,
        configuration: this.destination.configuration,
        branch_id: !!this.destination.branch
          ? this.destination.branch.id
          : null,
        office_id: !!this.destination.office
          ? this.destination.office.id
          : null,
        status_map: this.destination.status_map,
        is_active: this.destination.is_active,
        land_autologin: this.destination.land_autologin,
        deposit_notification: this.destination.deposit_notification,
      };
    },
    addIcon() {
      return faPlusCircle;
    },
    removeIcon() {
      return faMinusCircle;
    },
    addQuestion () {
      return faQuestion;
    },
  },
  watch: {
    'destination.driver'(value) {
      if (!this.isUpdating) {
        this.destination.name = value ? value.name : null;
      }
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
        this.getDrivers();

        if (!!this.$route.params.destination) {
          this.destination = this.$route.params.destination;
          this.destination.configuration = !!this.destination
            .configuration_keys
            ? JSON.stringify(this.destination.configuration_keys)
            : null;
        }
      }
      this.loadBranches();
      this.loadOffices();
      this.loadStatuses();
    },
    load() {
      axios
        .get(`/api/leads-destinations/${this.id}`)
        .then(r => {
          this.destination = r.data;
          this.destination.configuration = !!this.destination
            .configuration
            ? JSON.stringify(
              this.destination.configuration,
              null,
              2,
            )
            : null;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить дестинейшн.',
            message: err.response.data.message,
          });
        })
        .finally(() => this.getDrivers());
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios
        .post('/api/leads-destinations/', this.cleanForm)
        .then(r => {
          this.$router.push({ name: 'leads-destinations.index' });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось сохранить дестинейшн.',
            message: err.response.data.message,
          });
        })
        .finally(() => (this.isBusy = false));
    },
    update() {
      this.isBusy = true;
      axios
        .put(`/api/leads-destinations/${this.id}`, this.cleanForm)
        .then(r =>
          this.$router.push({
            name: 'leads-destinations.show',
            params: { id: r.data.id },
          }),
        )
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось обновить дестинейшн.',
            message: err.response.data.message,
          });
        })
        .finally(() => (this.isBusy = false));
    },
    cancel() {
      if (this.isUpdating) {
        this.$router.push({
          name: 'leads-destinations.show',
          params: { id: this.id },
        });
      } else {
        this.$router.push({ name: 'leads-destinations.index' });
      }
    },
    getDrivers() {
      axios
        .get('/api/lead-destination-drivers')
        .then(response => {
          this.drivers = response.data;
          const index = this.drivers.findIndex(
            driver => driver.id === this.destination.driver,
          );
          if (index !== -1) {
            this.destination.driver = this.drivers[index];
          }
        })
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить драйвера',
            message: error.response.data.message,
          }),
        );
    },
    loadBranches() {
      axios
        .get('/api/branches')
        .then(({ data }) => (this.branches = data))
        .catch(err =>
          this.$toast.error({
            title: 'Не удалось загрузить филиалы.',
            message: err.response.data.message,
          }),
        );
    },
    loadOffices() {
      axios
        .get('/api/offices')
        .then(({ data }) => (this.offices = data))
        .catch(err =>
          this.$toast.error({
            title: 'Не удалось загрузить офисы.',
            message: err.response.data.message,
          }),
        );
    },
    loadStatuses() {
      axios
        .get('/api/statuses')
        .then(r => (this.statuses = r.data.map(status => status.name)))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить список статусов.',
            message: e.response.data.message,
          }),
        );
    },
    loadLeadDestinationConfigInformation() {
      this.example_configuration = null;
      axios
        .post('/api/lead-destinations/configuration', {driver: this.destination.driver})
        .then(r => this.example_configuration = r.data.data)
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить пример конфигурации статусов.',
            message: e.response.data.message,
          }),
        );
    },
    addStatusMapItem() {
      if (!this.destination.status_map) {
        this.destination.status_map = [];
      }
      this.destination.status_map.push({
        external: '',
        internal: '',
      });
    },
  },
};
</script>
