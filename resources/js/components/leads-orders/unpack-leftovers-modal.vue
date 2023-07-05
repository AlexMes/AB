<template>
  <modal
    name="unpack-leftovers-modal"
    height="350"
    :adaptive="true"
    @before-open="beforeOpen"
    @before-close="reset"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Оффер</label>
        <mutiselect
          v-model="filters.offers"
          :show-labels="false"
          :multiple="true"
          :close-on-select="false"
          :limit="3"
          :max-height="170"
          :options="offers"
          track-by="id"
          label="name"
          placeholder="Выберите оферы"
        ></mutiselect>
        <span
          v-if="errors.has('offer_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('offer_id')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Дата</label>
        <date-picker
          v-model="filters.date"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите даты"
        ></date-picker>
        <span
          v-if="errors.has('date')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('date')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="unpackLeftovers"
        >
          Вернуть из LO
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('unpack-leftovers-modal');"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'unpack-leftovers-modal',
  components: {
    DatePicker,
  },
  data: () => ({
    filters: {
      date: '',
      offers: [],
    },
    offers: [],
    pickerConfig: {
      minDate: '2020-04-07',
      mode: 'range',
    },
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasOffers() {
      return this.offers.length > 0;
    },
    cleanFilters() {
      return {
        date: this.datePeriod,
        offer_id: this.filters.offers.map(offer => offer.id),
      };
    },
    datePeriod() {
      if (this.filters.date === null || this.filters.date === ''){
        return null;
      }

      const dates = this.filters.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  methods: {
    beforeOpen(event) {
      if (!this.hasOffers) {
        this.loadOffers();
      }
    },
    loadOffers() {
      axios.get('/api/offers')
        .then(({data}) => this.offers = data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить офферы.', message: err.response.data.message}));
    },
    unpackLeftovers() {
      this.isBusy = true;
      axios.post('/api/unpack-leftovers', this.cleanFilters)
        .then(() => {
          this.$modal.hide('unpack-leftovers-modal');
          this.$toast.success({title: 'OK', message: 'Остатки возвращены из LO.'});
          this.errors = new ErrorBag();
          this.$eventHub.$emit('leftovers-unpacked');
        })
        .catch(err => {
          this.errors.fromResponse(err);
          this.$toast.error({title: 'Не удалось вернуть из остатков.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    reset() {
      this.filters = {
        date: '',
        offers: [],
      };
    },
  },
};
</script>

