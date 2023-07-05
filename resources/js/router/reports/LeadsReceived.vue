<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Leads received
        </h1>
        <span>
          <span
            v-if="report.period"
            class="text-gray-600 font-medium mt-2 md:mt-0"
            v-text="`( ${report.period.since} - ${report.period.until} )`"
          ></span>
        </span>
      </div>
      <div class="flex flex-col my-6">
        <div class="flex flex-wrap md:flex-no-wrap items-start md:items-center">
          <div class="flex flex-col align-middlemx-2 my-2 md:my-0 w-1/6">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-1/6">
            <label class="mb-2">Офисы</label>
            <mutiselect
              v-model="filterSets.offices"
              :show-labels="false"
              :multiple="true"
              :options="offices"
              placeholder="Выберите офисы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-1/6">
            <label class="mb-2">Группы офисов</label>
            <mutiselect
              v-model="filterSets.officeGroups"
              :show-labels="false"
              :multiple="true"
              :options="officeGroups"
              placeholder="Выберите группы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col align-middle items-end mx-2 my-2 md:my-0 w-4/6">
            <button
              type="button"
              class="button btn-primary mt-4"
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
    <div class="container mx-auto bg-white shadow rounded">
      <report-table
        auto-height
        :report="report"
      ></report-table>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import {uniqBy} from 'lodash-es';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'leads-received',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment().startOf('month').subtract(1, 'days').format('YYYY-MM-DD')} to ${moment().format(
      'YYYY-MM-DD',
    )}`,
    pickerConfig: {
      defaultDates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
        'YYYY-MM-DD',
      )}`,
      minDate: '2019-12-02',
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    report: {
      headers: [],
      rows: [],
      summary: [],
      period: null,
    },
    filterSets: {
      offices: [],
      officeGroups: [],
    },
    isBusy:false,
    offices: [],
    officeGroups: [],
  }),
  computed: {
    period() {
      const dates = this.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        since: this.period.since,
        until: this.period.until,
        offices: this.filterSets.offices === null ? null : this.filterSets.offices.map(office => office.id),
        officeGroups: this.filterSets.officeGroups === null ? null : this.filterSets.officeGroups.map(group => group.id),
      };
    },
  },
  created() {
    this.load();
    this.getOffices();
    this.getOfficeGroups();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/leads-received', { params: this.cleanFilters })
        .then(response => this.report = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        ).finally(() => this.isBusy = false);
    },

    getOffices() {
      axios.get('/api/offices')
        .then(response => this.offices = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить офисы',
            message: error.response.data.message,
          }),
        );
    },

    getOfficeGroups() {
      axios.get('/api/office-groups')
        .then(r => this.officeGroups = r.data)
        .catch(error => this.$toast.error({title: 'Не удалось загрузить группы офисов', message: error.response.data.message}));
    },
  },
};
</script>
