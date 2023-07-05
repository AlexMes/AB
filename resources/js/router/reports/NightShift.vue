<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Night shift lead stats
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
          <div class="flex flex-col align-middlemx-2 my-2 md:my-0">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
            <label class="mb-2">Группировка</label>
            <select v-model="filterSets.groupBy">
              <option
                v-for="group in groupByTypes"
                :key="group.key"
                :value="group.key"
                v-text="group.name"
              ></option>
            </select>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
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

          <div
            v-if="filterSets.groupBy === 'status'"
            class="flex flex-col align-middle mx-2 my-2 md:my-0"
          >
            <label class="mb-2">Статус</label>
            <select v-model="status">
              <option :value="null">
                Все
              </option>
              <option
                v-for="(status,index) in statuses"
                :key="index"
                :value="status"
                v-text="status"
              ></option>
            </select>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
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

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 ">
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

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
            <label class="mb-2">Разбивать по офису</label>
            <toggle v-model="filterSets.groupByOffice"></toggle>
          </div>

          <div class="flex flex-col align-middle items-end mx-2 my-2 md:my-0">
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
      <div class="flex flex-col mb-6">
        <div class="flex flex-wrap md:flex-no-wrap items-start md:items-center">
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
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
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
            <label class="mb-2">UTM campaign</label>
            <mutiselect
              v-model="filterSets.utmCampaign"
              :show-labels="false"
              :options="utmCampaigns"
              placeholder="Выберите utm campaign"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
            <label class="mb-2">UTM content</label>
            <mutiselect
              v-model="filterSets.utmContent"
              :show-labels="false"
              :options="utmContents"
              placeholder="Выберите utm content"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
            <label class="mb-2">Афилиаты</label>
            <mutiselect
              v-model="filterSets.affiliates"
              :show-labels="false"
              :multiple="true"
              :options="affiliates"
              placeholder="Выберите афилиатов"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
            <label class="mb-2">Теги</label>
            <mutiselect
              v-model="filterSets.labels"
              :show-labels="false"
              :multiple="true"
              :options="labels"
              placeholder="Выберите теги"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
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
import {uniqBy} from 'lodash-es';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'lead-stats',
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
    statuses:[],
    status:null,
    report: {
      headers: [],
      rows: [],
      summary: [],
      period: null,
    },
    filterSets: {
      groupBy: 'status',
      offices: [],
      offers:[],
      groupByOffice: false,
      traffic:null,
      users: [],
      utmCampaign: null,
      utmContent: null,
      affiliates: [],
      labels: [],
      officeGroups: [],
    },
    isBusy:false,
    groupByTypes: [
      {key: 'status', name: 'Статус'},
      {key: 'note', name: 'Причина отказа'},
      {key: 'age', name: 'Возраст'},
      {key: 'gender-age', name: 'Пол и возраст'},
      {key: 'profession', name: 'Профессии'},
    ],
    offices: [],
    offers:[],
    users: [],
    utmCampaigns: [],
    utmContents: [],
    affiliates: [],
    labels: [],
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
        groupBy: this.filterSets.groupBy,
        status:this.status,
        offices: this.filterSets.offices === null ? null : this.filterSets.offices.map(office => office.id),
        offers:this.offers === null ? null : this.offers.map(offer => offer.id),
        groupByOffice: this.filterSets.groupByOffice,
        traffic : this.filterSets.traffic,
        users: this.filterSets.users === null ? null : this.filterSets.users.map(user => user.id),
        utmCampaign: this.filterSets.utmCampaign,
        utmContent: this.filterSets.utmContent,
        affiliates: this.filterSets.affiliates === null ? null : this.filterSets.affiliates.map(user => user.id),
        labels: this.filterSets.labels === null ? null : this.filterSets.labels.map(label => label.id),
        officeGroups: this.filterSets.officeGroups === null ? null : this.filterSets.officeGroups.map(group => group.id),
      };
    },
  },
  created() {
    this.load();
    this.getOffices();
    this.getStatuses();
    this.getOffers();
    this.getUsers();
    this.getUtmCampaigns();
    this.getUtmContents();
    this.getAffiliates();
    this.getLabels();
    this.getOfficeGroups();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/night-shift', { params: this.cleanFilters })
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

    getStatuses() {
      axios.get('/api/statuses')
        .then(response => this.statuses = response.data.map(status => status.name))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить статусы',
            message: error.response.data.message,
          }),
        );
    },
    getOffers(){
      axios.get('/api/offers')
        .then(r => this.filterSets.offers = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
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
    getUtmCampaigns(){
      axios.get('/api/utm-campaigns')
        .then(r => this.utmCampaigns = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getUtmContents(){
      axios.get('/api/utm-content')
        .then(r => this.utmContents = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getAffiliates() {
      axios.get('/api/affiliates')
        .then(r => this.affiliates = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getLabels() {
      axios.get('/api/labels')
        .then(r => this.labels = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getOfficeGroups() {
      axios.get('/api/office-groups')
        .then(r => this.officeGroups = r.data)
        .catch(error => this.$toast.error({title: 'Не удалось загрузить группы офисов', message: error.response.data.message}));
    },
  },
};
</script>
