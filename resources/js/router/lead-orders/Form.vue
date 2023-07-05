<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="`${order.date} - ${order.office.name}`"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый заказ
      </h3>
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
            for="date"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="date"
                v-model="order.date"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                :config="pickerConfig"
                placeholder="Выберите дату заказа"
                @input="errors.clear('date')"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('date')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('date')"
            ></span>
          </div>
        </div>

        <div
          v-if="['admin'].includes($root.user.role)"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Филиал
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="order.branch"
                :show-labels="false"
                :options="branches"
                placeholder="Выберите филиал"
                track-by="id"
                label="name"
                @input="errors.clear('branch_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('branch_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('branch_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="order.office"
                :show-labels="false"
                :options="offices"
                placeholder="Выберите офис"
                track-by="id"
                label="name"
                @input="errors.clear('office_id');setDefaultStartStop();getDestinations()"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('office_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('office_id')"
            ></span>
          </div>
        </div>

        <!--        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="destination_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Доставка
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                v-model="order.destination"
                :show-labels="false"
                :allow-empty="false"
                :options="destinations"
                placeholder="Выберите доставку"
                track-by="id"
                label="name"
                @input="errors.clear('destination_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('destination_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('destination_id')"
            ></span>
          </div>
        </div>-->

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="start_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Старт заказа в
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="start_at"
                v-model="order.start_at"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                type="time"
              />
            </div>
            <span
              v-if="errors.has('start_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('start_at')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="stop_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Стоп заказа в
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="stop_at"
                v-model="order.stop_at"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                type="time"
              />
            </div>
            <span
              v-if="errors.has('stop_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('stop_at')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Автоудаление дублей
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="order.autodelete_duplicates"></toggle>
            </div>
            <span
              v-if="errors.has('autodelete_duplicates')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('autodelete_duplicates')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Запреть живой трафик
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="order.deny_live"></toggle>
            </div>
            <span
              v-if="errors.has('deny_live')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('deny_live')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="live_interval"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Живой трафик не чаще
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="live_interval"
                v-model="order.live_interval"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('live_interval')"
              >
                <option
                  v-for="interval in liveIntervals"
                  :key="interval.value"
                  :value="interval.value"
                  v-text="interval.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('live_interval')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('live_interval')"
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
            @click.prevent="save"
          >
            Сохранить
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import ErrorBag from '../../utilities/ErrorBag';
import moment from 'moment';

export default {
  name: 'lead-orders-form',
  components: {
    DatePicker,
  },
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => ({
    order: {
      date: null,
      destination_id: null,
      destination: null,
      office: {},
      branch: {},
      start_at: null,
      stop_at: null,
      autodelete_duplicates: false,
      deny_live: false,
      live_interval: 0,
    },
    isBusy: false,
    offices: [],
    branches: [],
    destinations: [],
    liveIntervals: [
      {name: 'Неограничено', value: 0},
      {name: '1 мин', value: 60},
      {name: '5 мин', value: 300},
      {name: '10 мин', value: 600},
      {name: '20 мин', value: 1200},
      {name: '30 мин', value: 1800},
    ],
    errors: new ErrorBag(),
    pickerConfig: {
      enableTime: false,
      dateFormat: 'Y-m-d',
    },
  }),
  computed: {
    isUpdating() {
      return this.id !== null;
    },
    cleanForm() {
      return {
        date: this.order.date,
        office_id: !!this.order.office ? this.order.office.id : null,
        branch_id: ['admin'].includes(this.$root.user.role)
          ? (!!this.order.branch ? this.order.branch.id : null)
          : undefined,
        destination_id: !!this.order.destination ? this.order.destination.id : null,
        start_at: this.order.start_at,
        stop_at: this.order.stop_at,
        autodelete_duplicates: this.order.autodelete_duplicates,
        deny_live: this.order.deny_live,
        live_interval: this.order.live_interval,
      };
    },
  },
  created() {
    if (this.isUpdating) {
      axios
        .get(`/api/leads-orders/${this.id}`)
        .then(({ data }) => {
          this.order = data;
          this.getDestinations();
        });
    }
    axios.get('/api/offices').then(({ data }) => {
      this.offices = data;
      if (!!this.$route.params.officeId && !this.isUpdating) {
        this.order.office = this.offices.find(o => o.id === this.$route.params.officeId) || {};
        this.setDefaultStartStop();
      }
    });
    this.getBranches();
  },
  methods: {
    save() {
      this.isBusy = true;
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'leads-orders.show', params:{id: this.id}})
        : this.$router.push({name:'leads-orders.index'});
    },
    update() {
      axios
        .put(`/api/leads-orders/${this.id}`, this.cleanForm)
        .then(() => {
          this.$router.push({
            name: 'leads-orders.show',
            params: { id: this.id },
          });
        })
        .catch(error => this.errors.fromResponse(error))
        .finally(() => (this.isBusy = false));
    },
    create() {
      axios
        .post('/api/leads-orders', this.cleanForm)
        .then(({ data }) =>
          this.$router.push({
            name: 'leads-orders.show',
            params: { id: data.id },
          }),
        )
        .catch(error => this.errors.fromResponse(error))
        .finally(() => (this.isBusy = false));
    },
    getDestinations() {
      axios
        .get('/api/leads-destinations', {params: {office_id: this.order.office.id, active: true}})
        .then(({ data }) => (this.destinations = data.data))
        .catch(r =>
          this.$toast.error({
            title: 'Cannot get destinations',
            message: r.response.data.message,
          }),
        );
    },
    getBranches() {
      axios.get('/api/branches').then(({ data }) => {
        this.branches = data;
      });
    },
    setDefaultStartStop() {
      if (!this.isUpdating) {
        if (!!this.order.office) {
          this.order.start_at = this.order.office.default_start_at;
          this.order.stop_at = this.order.office.default_stop_at;
        }
      }
    },
  },
};
</script>
