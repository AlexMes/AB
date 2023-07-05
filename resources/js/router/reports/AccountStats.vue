<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Account statistics
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
          <div class="flex flex-col align-middlemx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Баеры</label>
            <mutiselect
              v-model="filters.users"
              :show-labels="false"
              :multiple="true"
              :options="users"
              placeholder="Выберите баера"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Статус</label>
            <mutiselect
              v-model="filters.statuses"
              :show-labels="false"
              :multiple="true"
              :options="statuses"
              placeholder="Выберите статусы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">UTM Campaign</label>
            <mutiselect
              v-model="filters.utmCampaigns"
              :show-labels="false"
              :multiple="true"
              :options="utmCampaigns"
              placeholder="Выберите кампанию"
            ></mutiselect>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
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
    <div class="bg-white shadow rounded">
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
  name: 'account-stats',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
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
    filters: {
      users: [],
      utmCampaigns: [],
      statuses: [],
    },
    isBusy: false,
    users: [],
    utmCampaigns: [],
    statuses: [
      {id: 1, name: 'ACTIVE'},
      {id: 2, name: 'DISABLED'},
      {id: 3, name: 'UNSETTLED'},
      {id: 7, name: 'PENDING_RISK_REVIEW'},
      {id: 8, name: 'PENDING_SETTLEMENT'},
      {id: 9, name: 'IN_GRACE_PERIOD'},
      {id: 100, name: 'PENDING_CLOSURE'},
      {id: 101, name: 'CLOSED'},
      {id: 201, name: 'ANY_ACTIVE'},
      {id: 202, name: 'ANY_CLOSED'},
    ],
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
        users: this.filters.users === null ? null : this.filters.users.map(user => user.id),
        campaigns: this.filters.utmCampaigns,
        statuses: this.filters.statuses === null ? null : this.filters.statuses.map(status => status.id),
      };
    },
  },
  created() {
    this.getUsers();
    this.getUtm();
    this.load();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/account-stats', {params: this.cleanFilters})
        .then(response => this.report = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        ).finally(() => this.isBusy = false);
    },
    getUsers() {
      axios
        .get('/api/users', {
          params: {
            all: true,
            userRole: 'buyer',
          },
        })
        .then(response => (this.users = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить пользователей',
            message: error.response.data.message,
          }),
        );
    },
    getUtm() {
      axios.get('/api/utm-campaigns')
        .then(r => this.utmCampaigns = r.data)
        .catch(err => this.$toast.error({title: 'Ошибка', message: err.response.data.message}));
    },
  },
};
</script>
