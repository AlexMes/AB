<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div
        class="flex flex-col md:flex-row w-full justify-between items-start md:items-center"
      >
        <h1 class="text-gray-700 flex">
          Текущие показатели
        </h1>
      </div>
      <div class="flex flex-col my-6">
        <div class="flex flex-wrap md:flex-no-wrap items-start md:items-center">
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-1/6">
            <label class="mb-2">Дата</label>
            <date-picker
              id="datetime"
              v-model="date"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите дату"
            ></date-picker>
          </div>
          <div class="flex flex-col align-middle pr-4 my-2 md:my-0 w-1/6">
            <button
              type="button"
              class="button btn-primary mt-4 ml-3"
              :disabled="isBusy"
              @click="load"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far','spinner']"
                  class="mr-2 fill-current"
                  spin
                  fixed-width
                ></fa-icon> Загрузка
              </span>
              <span v-else>Загрузить</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="flex flex-col">
      <div class="flex my-4">
        <div class="flex flex-col w-1/2 mr-4 bg-white shadow rounded">
          <div class="flex items-center p-4 border-b w-full">
            <p class="font-semibold">
              Daily for month
            </p>
          </div>
          <report-table
            auto-height
            :report="reports.dailyForMonth"
          ></report-table>
        </div>
        <div class="flex flex-col w-1/2 ml-4 bg-white shadow rounded">
          <div class="flex items-center p-4 border-b w-full">
            <div class="flex items-center flex-wrap justify-between w-full">
              <p class="font-semibold">
                Daily
              </p>
            </div>
          </div>
          <report-table
            auto-height
            :report="reports.daily"
          ></report-table>
        </div>
      </div>
      <div class="flex flex-row my-4">
        <div class="flex flex-col w-1/2 mr-4 bg-white shadow rounded">
          <div class="p-4 border-b w-full">
            <p class="font-semibold">
              Конверсия 3 дня
            </p>
          </div>
          <report-table
            auto-height
            :report="reports.threeDayConversion"
          ></report-table>
        </div>
        <div class="flex flex-col w-1/2 ml-4 bg-white shadow rounded">
          <div class="p-4 border-b w-full">
            <p class="font-semibold">
              План/факт
            </p>
          </div>
          <report-table
            auto-height
            :report="reports.planFact"
          ></report-table>
        </div>
      </div>
      <div class="flex flex-col w-full my-4 bg-white shadow rounded">
        <div class="p-4 border-b w-full">
          <p class="font-semibold">
            Performance ({{ reports.performanceDay.period.until }})
          </p>
        </div>
        <report-table
          auto-height
          :report="reports.performanceDay"
        ></report-table>
      </div>
      <div class="flex flex-col w-full my-4 bg-white shadow rounded">
        <div class="p-4 border-b w-full">
          <p class="font-semibold">
            Performance ({{ reports.performance3Days.period.since }} - {{ reports.performance3Days.period.until }})
          </p>
        </div>
        <report-table
          auto-height
          :report="reports.performance3Days"
        ></report-table>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'current-rates',
  components: {DatePicker},
  data: () => ({
    reports: {
      threeDayConversion: {},
      planFact: {},
      dailyForMonth: {},
      daily: {},
      performanceDay: {
        period: {
          since: moment().subtract(1, 'd').format('YYYY-MM-DD'),
          until: moment().subtract(1, 'd').format('YYYY-MM-DD'),
        },
      },
      performance3Days: {
        period: {
          since: moment().subtract(3, 'd').format('YYYY-MM-DD'),
          until: moment().subtract(1, 'd').format('YYYY-MM-DD'),
        },
      },
    },
    isBusy: false,
    pickerConfig: {},
    date: moment().subtract(1, 'd').format('YYYY-MM-DD'),
  }),
  created() {
    this.load();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/current-rates', {params: {date: this.date}})
        .then(({ data }) => (this.reports = data))
        .catch(err =>
          this.$toast.error({
            title: 'error',
            message: err.response.statusText,
          }),
        )
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
