<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Conversion stats
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
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="filters.dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Уровень</label>
            <select
              v-model="filters.level"
            >
              <option
                v-for="level in levels"
                :key="level.id"
                :value="level.id"
                v-text="level.name"
              ></option>
            </select>
          </div>
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Офферы</label>
            <mutiselect
              v-model="filters.offers"
              :show-labels="false"
              :multiple="true"
              :options="offers"
              placeholder="Выберите офферы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col align-middle pr-4 my-2 md:my-0 w-full ">
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
          <div class="flex flex-col align-middle pr-4 my-2 md:my-0 w-full ">
            <button
              type="button"
              class="button btn-primary mt-4 ml-3"
              :disabled="isBusy"
              @click="download"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far','spinner']"
                  class="mr-2 fill-current"
                  spin
                  fixed-width
                ></fa-icon> Загрузка
              </span>
              <span v-else>Скачать</span>
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
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import downloadLink from '../../utilities/helpers/downloadLink';
export default {
  name: 'daily',
  components:{
    DatePicker,
  },
  data:()=>({
    isBusy: false,
    filters:{
      dates: `${moment('2020-12-01').format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      offers: [],
      level: 'month',
    },
    pickerConfig:{
      defaultDates: `${moment('2020-12-01').format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    report:{
      headers: [],
      rows: [],
      summary: [],
      period:null,
    },
    offers:[],
    levels: [
      {id: 'month', name: 'Месяц'},
      {id: 'date', name: 'Дата'},
      {id: 'creo', name: 'Креатив'},
    ],
  }),
  computed:{
    period(){
      const dates = this.filters.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters(){
      return {
        since: this.period.since,
        until: this.period.until,
        offers: this.filters.offers === null ? null : this.filters.offers.map(offer => offer.id),
        level: this.filters.level,
      };
    },
  },
  created(){
    axios.get('/api/offers')
      .then(response => this.offers = response.data)
      .catch(error => console.error);
    this.load();
  },
  methods:{
    load(){
      this.isBusy = true;
      axios.get('/api/reports/conversion-stats', {params:this.cleanFilters})
        .then(response => this.report = response.data)
        .catch(error => console.error)
        .finally(() => this.isBusy = false);
    },
    download() {
      this.isBusy = true;
      axios.get('/api/reports/exports/conversion-stats', {
        params: this.cleanFilters,
        responseType: 'blob',
      })
        .then(({data}) => downloadLink(data, 'conversion-stats.xlsx'))
        .catch(error => this.$toast.error({
          title: 'Не удалось скачать отчет',
          message: error.response.data.message,
        }),
        ).finally(() => this.isBusy = false);
    },
  },
};
</script>
