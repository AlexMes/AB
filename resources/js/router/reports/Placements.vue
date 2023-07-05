<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Placements report
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
            <label class="mb-2">UTM Campaign</label>
            <select
              v-model="utmCampaign"
            >
              <option :value="null">
                Все
              </option>
              <option
                v-for="(utmCampaign,index) in filterSets.utmCampaigns"
                :key="index"
                :value="utmCampaign"
                v-text="utmCampaign"
              ></option>
            </select>
          </div>
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Трафик</label>
            <select v-model="filterSets.traffic">
              <option :value="null">
                Весь
              </option>
              <option value="affiliate">
                Партнерский
              </option>
              <option value="own">
                Свой
              </option>
            </select>
          </div>
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Оффер</label>
            <mutiselect
              v-model="offers"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.offers"
              placeholder="Выберите оффер"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div
            v-if="$root.isAdmin"
            class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full "
          >
            <label class="mb-2">Команды</label>
            <mutiselect
              v-model="teams"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.teams"
              placeholder="Выберите команду"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div
            v-if="$root.user.role !== 'verifier'"
            class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full "
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
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
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
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Placements</label>
            <mutiselect
              v-model="placements"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.placements"
              placeholder="Выберите плейсменты"
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
  name: 'performance',
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
    filterSets: {
      users: [],
      accounts: [],
      offers:[],
      utmCampaigns:[],
      placements: [],
      traffic: null,
      teams: [],
    },
    utmCampaign:null,
    isBusy:false,
    users: [],
    accounts: [],
    offers:[],
    placements: [],
    teams: [],
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
        utm: this.utmCampaign,
        since: this.period.since,
        until: this.period.until,
        users: this.users === null ? null : this.users.map(user => user.id),
        offers:this.offers === null ? null : this.offers.map(offer => offer.id),
        accounts:
          this.accounts === null
            ? null
            : this.accounts.map(account => account.account_id),
        placements: this.placements,
        traffic: this.filterSets.traffic,
        teams: this.teams.map(team => team.id),
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
    this.getUtm();
    this.getOffers();
    this.getPlacements();
    this.getTeams();
    this.load();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/placements', { params: this.cleanFilters })
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
    getOffers(){
      axios.get('/api/offers')
        .then(r => this.filterSets.offers = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getUtm(){
      axios.get('/api/utm-campaigns')
        .then(r => this.filterSets.utmCampaigns = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getPlacements(){
      axios.get('/api/placement-list')
        .then(r => this.filterSets.placements = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getTeams() {
      axios.get('/api/teams', {params: {all: true}})
        .then(({data}) => this.filterSets.teams = data)
        .catch(err => this.$toast.error({title: 'Could not get team list.', message: err.response.data.message}));
    },
  },
};
</script>
