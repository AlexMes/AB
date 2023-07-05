<template>
  <div class="flex flex-col flex-shrink-0 bg-white rounded shadow mb-8">
    <div class="flex justify-between items-center p-3 flex-col md:flex-row">
      <h3
        class="text-gray-700 flex text-left w-full mb-4 md:mb-0"
      >
        Статистика по офферам
      </h3>
      <div
        class="flex flex-wrap items-center justify-between md:justify-end w-full"
      >
        <date-picker
          id="datetime"
          v-model="range"
          class="w-56 px-3 py-2 pr-4 mx-2 border border-gray-400 rounded text-gray-700"
          placeholder="Дата"
          :config="pickerConfig"
        ></date-picker>
        <select
          v-if="$root.isAdmin && offers.length > 1"
          v-model="offer"
          name="us-user"
          class="w-40 ml-2 mt-4 md:mt-0 self-end flex text-gray-700"
        >
          <option :value="null">
            Все
          </option>
          <option
            v-for="offer in offers"
            :key="offer.id"
            :value="offer.id"
            v-text="offer.name"
          ></option>
        </select>
      </div>
    </div>
    <div class="overflow-x-auto overflow-y-hidden flex w-full">
      <report-table
        v-if="hasStats"
        auto-height
        :report="report"
      ></report-table>
      <div
        v-else
        class="w-full flex border-t justify-center bg-white p-4 font-medium text-xl text-gray-700"
      >
        <span v-if="isBusy">
          <fa-icon
            :icon="['far', 'spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon>Загрузка
        </span>
        <span
          v-else
          class="flex text-center"
        >
          Нет статистики
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';
export default {
  name: 'offer-stats',
  components: {
    DatePicker,
  },
  data: () => ({
    report: {
      headers: [],
      rows: [],
      summary: [],
    },
    isBusy: false,
    offers: [],
    offer: null,
    range: `${moment()
      .startOf('month')
      .format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
    pickerConfig: {
      defaultDates: `${moment()
        .startOf('month')
        .format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      mode: 'range',
    },
  }),
  computed: {
    hasStats() {
      return this.report.rows.length > 0;
    },
    dates() {
      return this.range.split(' ');
    },
  },
  watch: {
    offer() {
      this.load();
    },
    range() {
      this.load();
    },
  },
  created() {
    if (this.$root.isAdmin) {
      axios
        .get('/api/offers', { params: { all: true } })
        .then(response => (this.offers = response.data))
        .catch(error => console.error);
    }
    this.load();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/offer-stats', {
          params: {
            offer: this.offer,
            since: this.dates[0],
            until: this.dates[2] || this.dates[0],
          },
        })
        .then(response => (this.report = response.data))
        .catch(error => console.error)
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
