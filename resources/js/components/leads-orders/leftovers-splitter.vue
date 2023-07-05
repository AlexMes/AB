<template>
  <modal
    name="leftovers-splitter"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-4">
        <label
          class="flex w-full mb-2 font-semibold"
        >Распределения остатков</label>
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

      <div
        v-if="$root.user.branch_id === 19"
        class="flex flex-col w-full"
      >
        <label
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
        >
          Пробовать перевыдавать на другие офисы
        </label>
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg">
            <toggle
              v-model="splitParams.retry"
            ></toggle>
          </div>
        </div>
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
              @click="$modal.hide('leftovers-splitter')"
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
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'leftovers-splitter',
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
    splitParams: {
      offices: null,
      offers: null,
      leads: null,
      amount: 1,
      simulate_autologin: false,
      retry: false,
    },
    search: '',
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
  },
  methods: {
    runSplitter() {
      this.isBusy = true;
      axios
        .post('/api/leads-orders-leftovers', {
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
          retry: this.splitParams.retry,
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Распределение запущено',
          });
          this.$modal.hide('leftovers-splitter');
        })
        .catch(err => {
          this.$toast.error({
            title: 'Ошибка',
            message: 'Распределение уже запущено',
          });
          this.errors.fromResponse(err);
        },
        )
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

    leadsLabel({ phone, fullname }) {
      return `${phone} - ${fullname}`;
    },
  },
};
</script>
