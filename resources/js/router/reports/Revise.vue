<template>
  <div class="flex flex-col">
    <div class="container flex flex-col mx-auto">
      <div
        class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
      >
        <h1 class="flex text-gray-700">
          Revise
        </h1>
        <span>
          <span
            v-if="report.period"
            class="mt-2 font-medium text-gray-600 md:mt-0"
            v-text="
              `( ${report.period.since} - ${report.period.until} )`
            "
          ></span>
        </span>
      </div>
      <div class="flex flex-col my-6">
        <div
          class="flex flex-wrap items-start md:flex-no-wrap md:items-center"
        >
          <div class="flex flex-col my-2 align-middlemx-2 md:my-0">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 text-gray-600 border rounded"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Разбивка</label>
            <select v-model="filterSets.groupBy">
              <option
                v-for="group in groupByTypes"
                :key="group.key"
                :value="group.key"
                v-text="group.name"
              ></option>
            </select>
          </div>

          <div
            v-if="[
              'admin',
              'support'
            ].includes(this.$root.user.role)"
            class="flex flex-col mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Батчез</label>
            <select v-model="filterSets.batches">
              <option
                v-for="batch in batches"
                :key="batch"
                :value="batch.id"
                v-text="batch.name"
              ></option>
            </select>
          </div>

          <div
            class="flex flex-col mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Модель</label>
            <select v-model="filterSets.payments">
              <option
                v-for="payment in payments"
                :key="payment"
                :value="payment.id"
                v-text="payment.name"
              ></option>
            </select>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Филиалы</label>
            <mutiselect
              v-model="filterSets.branches"
              :show-labels="false"
              :multiple="true"
              :options="branches"
              placeholder="Выберите филиалы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Команды</label>
            <mutiselect
              v-model="filterSets.teams"
              :show-labels="false"
              :multiple="true"
              :options="teams"
              placeholder="Выберите команды"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Баеры</label>
            <mutiselect
              v-model="filterSets.users"
              :show-labels="false"
              :multiple="true"
              :options="users"
              placeholder="Выберите баера"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
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

          <div
            class="flex items-end mx-2 my-2 space-x-2 align-middle md:my-0"
          >
            <button
              type="button"
              class="mt-4 button btn-primary"
              :disabled="isBusy"
              @click="load"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far', 'spinner']"
                  class="mr-2 fill-current"
                  spin
                  fixed-width
                ></fa-icon>
                Загрузка
              </span>
              <span v-else>Загрузить</span>
            </button>
            <button
              type="button"
              class="mt-4 ml-3 button btn-primary"
              :disabled="isBusy"
              @click="download"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far', 'spinner']"
                  class="mr-2 fill-current"
                  spin
                  fixed-width
                ></fa-icon>
                Загрузка
              </span>
              <span v-else>Скачать</span>
            </button>
          </div>
        </div>
      </div>
      <div class="flex flex-col mb-6">
        <div
          class="flex flex-wrap items-start md:flex-no-wrap md:items-center"
        >
          <div class="flex flex-col mx-2 my-2 align-middle md:my-0 ">
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

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Вертикаль</label>
            <select v-model="filterSets.vertical">
              <option :value="null">
                Все
              </option>
              <option
                v-for="vertical in verticals"
                :key="vertical.key"
                :value="vertical.key"
                v-text="vertical.name"
              ></option>
            </select>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Тип офера</label>
            <select v-model="filterSets.offerType">
              <option :value="null">
                Все
              </option>
              <option
                v-for="type in offerTypes"
                :key="type.key"
                :value="type.key"
                v-text="type.name"
              ></option>
            </select>
          </div>
          <div class="flex flex-col mx-2 my-2 align-middle md:my-0 ">
            <label class="mb-2">UTM sources</label>
            <mutiselect
              v-model="filterSets.utmSources"
              :show-labels="false"
              :multiple="true"
              :options="utmSources"
              placeholder="Выберите utm sources"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0 ">
            <label class="mb-2">Драйвер</label>
            <mutiselect
              v-model="drivers"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.drivers"
              placeholder="Выберите драйвер"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
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

          <div class="flex flex-col my-2 mx-2 align-middle md:my-0">
            <label class="mb-2">Источник трафика</label>
            <select v-model="filterSets.trafficSource">
              <option
                v-for="source in trafficSources"
                :key="`traffic-source-${source.id}`"
                :value="source.id"
                v-text="source.name"
              ></option>
            </select>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
            <label class="mb-2">Тип трафика</label>
            <select v-model="filterSets.trafficType">
              <option
                v-for="type in trafficTypes"
                :key="`traff-type-${type.id}`"
                :value="type.id"
                v-text="type.name"
              ></option>
            </select>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">WN (fact)</label>
            <toggle v-model="filterSets.wnFact"></toggle>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto bg-white rounded shadow">
      <report-table
        auto-height
        :report="report"
      ></report-table>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import { uniqBy } from 'lodash-es';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import downloadLink from '../../utilities/helpers/downloadLink';
export default {
  name: 'revise',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
      'YYYY-MM-DD',
    )}`,
    pickerConfig: {
      defaultDates: `${moment().format(
        'YYYY-MM-DD',
      )} to ${moment().format('YYYY-MM-DD')}`,
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
      groupBy: 'office',
      branches: [],
      batches:null,
      payments: null,
      teams: [],
      users: [],
      offers: [],
      offices: [],
      vertical: null,
      offerType: null,
      drivers: [],
      officeGroups: [],
      wnFact: false,
      trafficSource: null,
      trafficType: null,
      utmSources:[],
    },
    isBusy: false,
    groupByTypes: [
      { key: 'office', name: 'Офис' },
      { key: 'branch', name: 'Филиал' },
      { key: 'user', name: 'Байер' },
      { key: 'offer', name: 'Офер' },
      { key: 'destination', name: 'Destination' },
      { key: 'office-offer', name: 'Офис-оффер' },
      { key: 'office-offer-destination', name: 'Офис-оффер-дестинейшен' },
      { key: 'user-office', name: 'Байер-офис' },
      { key: 'user-office-offer', name: 'Байер-офис-офер' },
      { key: 'offer-geo', name: 'Офер-гео' },
      { key: 'office-offer-geo', name: 'Офис-офер-гео' },
      { key: 'user-geo', name: 'Байер-гео' },
      { key: 'user-geo-office', name: 'Баер-Гео-Офис' },
      { key: 'user-office-offer-geo', name: 'Байер-офис-офер-гео' },
      { key: 'office-offer-model-cost', name: 'Офис-офер-прайс' },
      { key: 'status', name: 'Статус' },
      { key: 'office-status', name: 'Офис-статус' },
      { key: 'office-offer-status', name: 'Офис-оффер-статус' },
      { key: 'app', name: 'Приложение' },
      { key: 'offer-source', name: 'Офер-сорс' },
      { key: 'user-destination', name: 'Байер-дестинайшен' },
      { key: 'user-offer-destination', name: 'Байер-офер-дестинайшен' },
    ],
    branches: [],
    batches:[
      {id: null, name: 'Все'},
      {id: 'onBatch', name: 'На батче'},
      {id: 'notOnBatch', name: 'Без Батчез'},
    ],
    payments: [
      {id: null, name: 'Все'},
      {id: 'cpa', name: 'CPA'},
      {id: 'cpl', name: 'CPL'},
    ],
    teams: [],
    users: [],
    offers: [],
    offices: [],
    verticals: [
      { key: 'Burj', name: 'Burj' },
      { key: 'Crypt', name: 'Crypt' },
    ],
    offerTypes: [
      { key: 'lo', name: 'LO' },
      { key: 'not_lo', name: 'Не LO' },
      { key: 'cd', name: 'CD' },
    ],
    drivers: [],
    officeGroups: [],
    trafficTypes: [
      {id: null, name: 'Все'},
      {id: 'affiliated', name: 'Аффилиатский'},
      {id: 'not_affiliated', name: 'Не аффилиатский'},
    ],
    trafficSources: [
      {id: null, name: 'Весь'},
      {id: 'in-app', name: 'IN/APP'},
      {id: 'ps', name: 'PS'},
      {id: 't', name: 'T'},
      {id: 'vk', name: 'VK'},
    ],
    utmSources: [],
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
        groupBy: this.filterSets.groupBy,
        branches:
          this.filterSets.branches === null
            ? null
            : this.filterSets.branches.map(branch => branch.id),
        batches: this.filterSets.batches,
        payments: this.filterSets.payments,
        teams:
          this.filterSets.teams === null
            ? null
            : this.filterSets.teams.map(team => team.id),
        users:
          this.filterSets.users === null
            ? null
            : this.filterSets.users.map(user => user.id),
        offices:
          this.filterSets.offices === null
            ? null
            : this.filterSets.offices.map(office => office.id),
        offers:
          this.offers === null
            ? null
            : this.offers.map(offer => offer.id),
        drivers: this.drivers === null ? null : this.drivers.map(driver => driver.id),
        vertical: this.filterSets.vertical,
        offerType: this.filterSets.offerType,
        officeGroups:
          this.filterSets.officeGroups === null
            ? null
            : this.filterSets.officeGroups.map(group => group.id),
        wnFact: this.filterSets.wnFact,
        traffic_type: this.filterSets.trafficType,
        traffic_source: this.filterSets.trafficSource,
        utmSources: this.filterSets.utmSources,
      };
    },
  },
  created() {
    this.load();
    this.getBranches();
    this.getTeams();
    this.getUsers();
    this.getOffers();
    this.getOffices();
    this.getDrivers();
    this.getOfficeGroups();
    this.getUtmSources();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/revise', { params: this.cleanFilters })
        .then(response => (this.report = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        )
        .finally(() => (this.isBusy = false));
    },
    download() {
      this.isBusy = true;
      axios
        .get('/api/reports/exports/revise', {
          params: this.cleanFilters,
          responseType: 'blob',
        })
        .then(({ data }) => downloadLink(data, 'revise.xlsx'))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось скачать отчет',
            message: error.response.data.message,
          }),
        )
        .finally(() => (this.isBusy = false));
    },
    getBranches() {
      axios
        .get('/api/branches')
        .then(response => (this.branches = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить филиалы',
            message: error.response.data.message,
          }),
        );
    },
    getTeams() {
      axios
        .get('/api/teams', { params: { all: true } })
        .then(response => (this.teams = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить команды',
            message: error.response.data.message,
          }),
        );
    },
    getUsers() {
      axios
        .get('/api/report-buyers')
        .then(response => (this.users = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить пользователей',
            message: error.response.data.message,
          }),
        );
    },
    getOffers() {
      axios
        .get('/api/offers')
        .then(r => (this.filterSets.offers = r.data))
        .catch(err =>
          this.$toast.error({
            title: 'Ошибка',
            message: err.response.data.message,
          }),
        );
    },
    getOffices() {
      axios
        .get('/api/offices')
        .then(response => (this.offices = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить офисы',
            message: error.response.data.message,
          }),
        );
    },
    getDrivers() {
      axios
        .get('/api/lead-destination-drivers')
        .then(response => this.filterSets.drivers = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить драйвера',
            message: error.response.data.message,
          }),
        );
    },
    getOfficeGroups() {
      axios.get('/api/office-groups')
        .then(r => this.officeGroups = r.data)
        .catch(error => this.$toast.error({title: 'Не удалось загрузить группы офисов', message: error.response.data.message}));
    },
    getUtmSources() {
      axios.get('/api/utm-sources')
        .then(r => this.utmSources = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
  },
};
</script>
