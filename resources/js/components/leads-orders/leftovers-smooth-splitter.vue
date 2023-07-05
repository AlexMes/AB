<template>
  <modal
    name="leftovers-smooth-splitter"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-4">
        <label
          class="flex w-full mb-2 font-semibold"
        >Плавная выдача остатков</label>
        <mutiselect
          v-model="splitParams.offices"
          :show-labels="false"
          :multiple="true"
          :limit="3"
          :options="offices"
          :max-height="70"
          track-by="id"
          label="name"
          placeholder="Выберите офисы"
        ></mutiselect>
      </div>
      <div class="flex flex-col w-full mb-4">
        <mutiselect
          v-model="splitParams.offers"
          :show-labels="false"
          :multiple="true"
          :limit="3"
          :options="offers"
          :max-height="70"
          track-by="id"
          label="name"
          placeholder="Выберите оферы"
        ></mutiselect>
      </div>
      <div class="flex flex-col w-full mb-4">
        <span
          class="cursor-pointer text-xs"
          @click="showSearch = !showSearch"
        >Поиск/Фильтр</span>
        <div
          v-if="showSearch"
          class="rounded-md shadow-sm mt-2"
        >
          <textarea
            v-model="splitParams.search"
            rows="7"
            placeholder="jonny@gmail.com,zhora123@gmail.com"
            class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
            required
          ></textarea>
        </div>
      </div>
      <div class="flex flex-col w-full">
        <mutiselect
          v-model="splitParams.leads"
          :show-labels="false"
          :multiple="true"
          :limit="3"
          :options="leads"
          :max-height="70"
          track-by="id"
          label="phone"
          :custom-label="leadsLabel"
          placeholder="Выберите лидов"
        >
          <template
            slot="option"
            slot-scope="props"
          >
            <div>
              <span>{{ props.option.phone }}</span>
              &nbsp;-&nbsp;
              <span>{{ props.option.fullname }}</span>
            </div>
          </template>
        </mutiselect>
      </div>

      <div class="flex flex-col w-full">
        <label
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
        >
          Симулировать автологин
        </label>
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg">
            <toggle
              v-model="splitParams.simulate_autologin"
            ></toggle>
          </div>
        </div>
      </div>

      <div class="flex flex-col w-full">
        <label
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
        >
          Пробовать заменить лид, если не доставлен
        </label>
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg">
            <toggle
              v-model="splitParams.replace"
            ></toggle>
          </div>
        </div>
      </div>

      <div
        v-if="splitParams.replace"
        class="flex flex-col w-full mt-4"
      >
        <mutiselect
          v-model="splitParams.destination"
          :show-labels="false"
          :options="destinations"
          :max-height="70"
          track-by="id"
          label="name"
          placeholder="Пропуск фейлед лидов на дестинейшене"
        ></mutiselect>
      </div>

      <div class="flex flex-col w-full">
        <label
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
        >
          Сперва свежие
        </label>
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg">
            <toggle
              v-model="splitParams.new_first"
            ></toggle>
          </div>
        </div>
        <span
          v-if="errors.has('sort_order')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('sort_order')"
        ></span>
      </div>

      <div class="flex flex-col w-full">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">Начать выдачу в</label>
        <date-picker
          v-model="splitParams.deliver_since"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfigSince"
          placeholder="Выберите даты"
          @input="errors.clear('deliver_since')"
        ></date-picker>
        <span
          v-if="errors.has('deliver_since')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('deliver_since')"
        ></span>
      </div>

      <div class="flex flex-col w-full">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">Выдать до</label>
        <date-picker
          v-model="splitParams.deliver_until"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfigUntil"
          placeholder="Выберите даты"
          @input="errors.clear('deliver_until')"
        ></date-picker>
        <span
          v-if="errors.has('deliver_until')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('deliver_until')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-4">
        <label
          for="amount"
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
        >
          Количество
        </label>
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg rounded-md shadow-sm">
            <input
              id="amount"
              v-model="splitParams.amount"
              type="amount"
              placeholder="999"
              class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
              required
              @input="errors.clear('amount')"
            />
          </div>
          <span
            v-if="errors.has('amount')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('amount')"
          ></span>
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="runSplitter"
            >
              Распределить
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('leftovers-smooth-splitter')"
            >
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>
  </modal>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import ErrorBag from '../../utilities/ErrorBag';
import moment from 'moment';

export default {
  name: 'leftovers-smooth-splitter',
  components: {DatePicker},
  props: {
    datePeriod: {
      type: Object,
      default: null,
    },
  },
  data: () => ({
    isBusy: false,
    showSearch: false,
    offices: [],
    offers: [],
    leads: [],
    destinations: [],
    splitParams: {
      offices: null,
      offers: null,
      leads: null,
      amount: 1,
      simulate_autologin: false,
      deliver_until: null,
      deliver_since: null,
      replace: false,
      search: '',
      new_first: false,
      destination: null,
    },
    pickerConfigSince: {
      enableTime: true,
      minDate: moment().add(5, 'm').format('YYYY-MM-DD HH:mm'),
    },
    pickerConfigUntil: {
      enableTime: true,
      minDate: moment().add(30, 'm').format('YYYY-MM-DD HH:mm'),
      defaultDate: moment().add(30, 'm').format('YYYY-MM-DD HH:mm'),
    },
    errors: new ErrorBag(),
  }),
  watch: {
    datePeriod() {
      this.getLeads();
    },
    'splitParams.offers'() {
      this.getLeads();
    },
    'splitParams.search'(newVal, oldVal) {
      const selected = newVal.split(/,|;|\s|\n/);
      this.splitParams.leads = this.leads.filter(lead => selected.includes(lead.id.toString()) || selected.includes(lead.phone)|| selected.includes(lead.email));
    },
  },
  created() {
    this.getOffices();
    this.getOffers();
    this.getLeads();
    this.getDestinations();
  },
  methods: {
    runSplitter() {
      this.isBusy = true;
      axios
        .post('/api/leads-orders-leftovers-smooth', {
          date: this.datePeriod,
          offices:
            this.splitParams.offices === null
              ? null
              : this.splitParams.offices.map(office => office.id),
          offers:
            this.splitParams.offers === null
              ? null
              : this.splitParams.offers.map(offer => offer.id),
          leads:
            this.splitParams.leads === null
              ? null
              : this.splitParams.leads.map(lead => lead.id),
          amount: this.splitParams.amount,
          simulate_autologin: this.splitParams.simulate_autologin,
          deliver_until: this.splitParams.deliver_until,
          deliver_since: this.splitParams.deliver_since,
          replace: this.splitParams.replace,
          sort_order: this.splitParams.new_first ? 'desc' : 'asc',
          destination: !this.splitParams.replace
            ? undefined
            : (this.splitParams.destination === null ? null : this.splitParams.destination.id),
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Распределение запущено',
          });
          this.$modal.hide('leftovers-smooth-splitter');
        })
        .catch(err => {
          this.$toast.error({
            title: 'Ошибка',
            message: err.response.data.message,
          });
          this.errors.fromResponse(err);
        })
        .finally(() => (this.isBusy = false));
    },

    getOffices() {
      axios
        .get('/api/offices')
        .then(response => (this.offices = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить офисы',
          }),
        );
    },

    getOffers() {
      axios
        .get('/api/offers')
        .then(response => (this.offers = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить оферы',
          }),
        );
    },

    getLeads() {
      axios
        .get('/api/leads-orders-leftovers', {params: {
          date: this.datePeriod ? [
            this.datePeriod.since, this.datePeriod.until,
          ] : null,
          offers:
            this.splitParams.offers === null
              ? null
              : this.splitParams.offers.map(offers => offers.id),
        }})
        .then(response => (this.leads = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить лиды',
          }),
        );
    },

    getDestinations() {
      axios
        .get('/api/leads-destinations', {params: {active: true}})
        .then(({ data }) => (this.destinations = data.data))
        .catch(r =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить дестинейшены',
          }),
        );
    },

    leadsLabel({ phone, fullname }) {
      return `${phone} - ${fullname}`;
    },
  },
};
</script>
