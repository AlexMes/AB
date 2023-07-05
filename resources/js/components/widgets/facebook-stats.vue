<template>
  <div class="flex flex-col flex-shrink-0 bg-white rounded shadow mb-8">
    <div class="flex justify-between items-center p-3 flex-col md:flex-row">
      <h3
        class="text-gray-700 flex text-left w-full mb-4 md:mb-0"
      >
        Статистика FB
      </h3>
      <div
        class="flex flex-wrap items-center justify-between md:justify-end w-full"
      >
        <date-picker
          id="datetime"
          v-model="range"
          class="w-56 px-3 py-2 pr-4 mx-2 border border-gray-400 rounded text-gray-700"
          placeholder="Период"
          :config="pickerConfig"
        ></date-picker>
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
import moment from 'moment';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'facebook-stats',
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
    range: `${moment().format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
    pickerConfig: {
      mode: 'range',
      maxDate: moment().format('YYYY-MM-DD'),
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
    range: {
      handler: 'load',
      immediate: true,
    },
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/facebook-stats', {
          params: {
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
