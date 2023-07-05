<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          Отчет
        </h1>
      </div>
    </header>

    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div
          class="max-w-full mt-3"
        >
          <div class="flex flex-wrap items-center mb-6 -mr-4">
            <div class="w-full sm:w-1/3 md:w-1/4 lg:w-1/5 mt-3 pr-4 sm:col-span-3">
              <label
                for="period"
                class="block text-sm font-medium leading-5 text-gray-700"
              >
                Период
              </label>
              <div class="mt-1 rounded-md shadow-sm">
                <date-picker
                  id="period"
                  v-model="filters.dates"
                  class="w-full px-1 py-2 border rounded text-gray-600"
                  :config="pickerConfig"
                  placeholder="Выберите даты"
                ></date-picker>
              </div>
            </div>
            <div class="w-full sm:w-1/3 md:w-1/4 lg:w-1/5 mt-3 pr-4 sm:col-span-3">
              <label
                for="users"
                class="block text-sm font-medium leading-5 text-gray-700"
              >
                Байер
              </label>
              <div class="mt-1">
                <multiselect
                  id="users"
                  v-model="filters.users"
                  :show-labels="false"
                  :multiple="true"
                  :options="users"
                  placeholder="Выберите пользователя"
                  track-by="id"
                  label="name"
                ></multiselect>
              </div>
            </div>
            <div class="w-full sm:w-1/3 md:w-1/4 lg:w-1/5 mt-3 pr-4 sm:col-span-3">
              <label
                for="offers"
                class="block text-sm font-medium leading-5 text-gray-700"
              >
                Офер
              </label>
              <div class="mt-1">
                <multiselect
                  id="offers"
                  v-model="filters.offers"
                  :show-labels="false"
                  :multiple="true"
                  :options="offers"
                  placeholder="Выберите офер"
                  track-by="id"
                  label="name"
                ></multiselect>
              </div>
            </div>
            <div class="w-full sm:w-1/3 md:w-1/4 lg:w-1/5 mt-3 pr-4 sm:col-span-3">
              <label
                for="groups"
                class="block text-sm font-medium leading-5 text-gray-700"
              >
                Группа
              </label>
              <div class="mt-1">
                <multiselect
                  id="groups"
                  v-model="filters.groups"
                  :show-labels="false"
                  :multiple="true"
                  :options="groups"
                  placeholder="Выберите группу"
                  track-by="id"
                  label="name"
                ></multiselect>
              </div>
            </div>
            <div class="w-full sm:w-1/3 md:w-1/4 lg:w-1/5 mt-3 pr-4 sm:col-span-3">
              <label
                for="accounts"
                class="block text-sm font-medium leading-5 text-gray-700"
              >
                Аккаунты
              </label>
              <div class="mt-1">
                <multiselect
                  id="accounts"
                  v-model="filters.accounts"
                  :show-labels="false"
                  :multiple="true"
                  :options="accounts"
                  placeholder="Выберите аккаунт"
                  track-by="id"
                  label="name"
                ></multiselect>
              </div>
            </div>
            <div class="mb-1 mt-5 self-end pr-4 ml-auto sm:col-span-3">
              <span class="inline-flex rounded-md shadow-sm">
                <button
                  type="submit"
                  :disabled="isBusy"
                  class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700"
                  @click.prevent="load"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  ><path
                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                    clip-rule="evenodd"
                    fill-rule="evenodd"
                  /></svg> Фильтровать
                </button>
              </span>
            </div>
          </div>
          <report-table :report="report"></report-table>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import ReportTable from '../../components/report/report-table';
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'performance-report',
  components: {
    ReportTable,
    DatePicker,
  },
  data: () => {
    return {
      isBusy: false,
      report: {
        headers: [],
        rows: [],
        summary: [],
        period: null,
      },
      pickerConfig: {
        defaultDates: `${moment().startOf('month').format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
        maxDate: moment().format('YYYY-MM-DD'),
        mode: 'range',
      },
      filters: {
        dates: `${moment().startOf('month').format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
        users: [],
        offers: [],
        groups: [],
        accounts: [],
      },
      users: [],
      offers: [],
      groups: [],
      accounts: [],
    };
  },
  computed: {
    cleanFilters() {
      return {
        since: this.period.since,
        until: this.period.until,
        users: this.filters.users.map(user => user.id),
        offers: this.filters.offers.map(user => user.id),
        groups: this.filters.groups.map(user => user.id),
        accounts: this.filters.accounts.map(user => user.id),
      };
    },
    period() {
      const dates = this.filters.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  created() {
    this.load();
    this.loadUsers();
    this.loadOffers();
    this.loadGroups();
    this.loadAccounts();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios.get('/api/reports/performance', {params: this.cleanFilters})
        .then(r => this.report = r.data)
        .catch(err => this.$toast.error({title: 'Unable load the report.', message: err.response.data.message}))
        .finally(() => this.isBusy = false);
    },
    loadUsers() {
      axios.get('/api/users')
        .then(r => this.users = r.data)
        .catch(err => this.$toast.error({title: 'Unable load users.', message: err.response.data.message}));
    },
    loadOffers() {
      axios.get('/api/offers', {params: {all: true}})
        .then(r => this.offers = r.data)
        .catch(err => this.$toast.error({title: 'Unable load offers.', message: err.response.data.message}));
    },
    loadGroups() {
      axios.get('/api/groups', {params: {all: true}})
        .then(r => this.groups = r.data)
        .catch(err => this.$toast.error({title: 'Unable load groups.', message: err.response.data.message}));
    },
    loadAccounts() {
      axios.get('/api/accounts', {params: {all: true}})
        .then(r => this.accounts = r.data)
        .catch(err => this.$toast.error({title: 'Unable load accounts.', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>

</style>
