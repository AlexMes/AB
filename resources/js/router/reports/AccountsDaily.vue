<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Account Daily report
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
        <div class="flex flex-no-wrap items-center">
          <div class="flex flex-col align-middle mx-2 w-1/5">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>
          <div
            v-if="$root.isAdmin"
            class="flex flex-col align-middle mx-2 w-1/5"
          >
            <label class="mb-2">Баеры</label>
            <mutiselect
              v-model="users"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.users"
              placeholder="Выберите баера"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle mx-2 w-1/5">
            <label class="mb-2">Аккаунты</label>
            <mutiselect
              v-model="accounts"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.accounts"
              placeholder="Выберите аккаунты"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle mx-2 w-1/5">
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
    <report-table
      auto-height
      :report="report"
    ></report-table>
  </div>
</template>

<script>
import moment from 'moment';
import {uniqBy} from 'lodash-es';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'accounts-daily',
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
      mode: 'range',
    },
    report: {
      headers: [],
      rows: [],
      summary: [],
      period: null,
    },
    filterSets: {
      users: [],
      accounts: [],
    },
    isBusy:false,
    users: [],
    accounts: [],
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
        users: this.users === null ? null : this.users.map(offer => offer.id),
        accounts:
          this.accounts === null
            ? null
            : this.accounts.map(office => office.id),
      };
    },
  },
  watch: {
    users() {
      this.getAccounts();
    },
  },
  created() {
    this.getUsers();
    this.getAccounts();
    this.load();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/accounts-daily', { params: this.cleanFilters })
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
        .get('/api/users', { params: { all: true } })
        .then(response => (this.filterSets.users = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить пользователей',
            message: error.response.data.message,
          }),
        );
    },
    getAccounts() {
      axios
        .get('/api/accounts', {
          params: { all: true, users: this.cleanFilters.users },
        })
        .then(response => this.filterSets.accounts = uniqBy(response.data, account => account.id))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить аккаунты',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
