<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Offer & UTM source
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
          <!--          <div class="flex flex-col align-middlemx-2 my-2 md:my-0">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>-->

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

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 ">
            <label class="mb-2">Оффер</label>
            <mutiselect
              v-model="filterSets.offers"
              :show-labels="false"
              :multiple="true"
              :options="offers"
              placeholder="Выберите оффер"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
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

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0">
            <label class="mb-2">Разбивать по UTM source</label>
            <toggle v-model="filterSets.groupByUtmSource"></toggle>
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
        <div class="flex flex-col">
          <div class="flex flex-col align-middle mr-2 my-2 md:my-0">
            <label class="mb-2">Почты</label>
            <textarea
              v-model="filterSets.leads"
              rows="7"
              placeholder="jonny@gmail.com,zhora123@gmail.com"
              class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
              required
            ></textarea>
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
  name: 'offer-source',
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
      offers: [],
      traffic: null,
      users: [],
      leads: '',
      groupByUtmSource: true,
    },
    isBusy:false,
    offers:[],
    users: [],
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
        offers: this.filterSets.offers === null ? null : this.filterSets.offers.map(offer => offer.id),
        traffic: this.filterSets.traffic,
        users: this.filterSets.users === null ? null : this.filterSets.users.map(user => user.id),
        leads: this.filterSets.leads !== '' ? this.filterSets.leads.split(/,|;|\s|\n/) : null,
        groupByUtmSource: this.filterSets.groupByUtmSource,
      };
    },
  },
  created() {
    /*this.load();*/
    this.getOffers();
    this.getUsers();
  },
  methods: {
    load() {
      if (!!this.filterSets.leads) {
        this.isBusy = true;
        axios
          .post('/api/reports/offer-source', this.cleanFilters)
          .then(response => this.report = response.data)
          .catch(error =>
            this.$toast.error({
              title: 'Не удалось загрузить отчет',
              message: error.response.data.message,
            }),
          ).finally(() => this.isBusy = false);
      } else {
        alert('Enter e-mails first !');
      }
    },
    getOffers(){
      axios.get('/api/offers')
        .then(r => this.offers = r.data)
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
  },
};
</script>
