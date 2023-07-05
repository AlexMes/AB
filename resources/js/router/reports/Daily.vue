<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Daily report
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
          <div
            v-if="$root.isAdmin"
            class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full "
          >
            <label class="mb-2">Офисы</label>
            <mutiselect
              v-model="filters.offices"
              :show-labels="false"
              :multiple="true"
              :options="offices"
              placeholder="Выберите офисы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div
            v-if="$root.isAdmin"
            class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full "
          >
            <label class="mb-2">Пользователь</label>
            <mutiselect
              v-model="filters.users"
              :show-labels="false"
              :multiple="true"
              :options="users"
              placeholder="Выберите офисы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div
            v-if="$root.isAdmin"
            class="flex ml-2 my-2 md:my-0 w-full "
          >
            <div class="flex flex-col">
              <label v-if="filters.simple">Упрощенный</label>
              <label v-else>Полный</label>
              <toggle
                v-model="filters.simple"
                class="mt-2"
              ></toggle>
            </div>
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
export default {
  name: 'daily',
  components:{
    DatePicker,
  },
  data:()=>({
    isBusy: false,
    filters:{
      dates:`${moment().format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      offers:[],
      offices:[],
      showAllDays:false,
      simple: false,
      users:[],
    },
    pickerConfig:{
      defaultDates: `${moment().format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
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
    offices:[],
    users:[],
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
        until:this.period.until,
        offers:this.filters.offers === null ? null : this.filters.offers.map(offer => offer.id),
        offices:this.filters.offices === null ? null : this.filters.offices.map(office => office.id),
        users:this.filters.users === null ? null : this.filters.users.map(user => user.id),
        showAllDays:this.filters.showAllDays,
        simple: this.filters.simple,
      };
    },
  },
  created(){
    if(this.$root.isAdmin) {
      axios.get('/api/offices')
        .then(response => this.offices = response.data)
        .catch(err => console.error);
      axios.get('/api/users', {params: {all: true}})
        .then(response => this.users = response.data)
        .catch(error => console.error);
    }
    axios.get('/api/offers')
      .then(response => this.offers = response.data)
      .catch(error => console.error);
    this.load();
  },
  methods:{
    load(){
      this.isBusy = true;
      axios.get('/api/reports/daily', {params:this.cleanFilters})
        .then(response => this.report = response.data)
        .catch(error => console.error)
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
