<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Office performance Copy report
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
        <div class="flex flex-wrap md:flex-no-wrap justify-between items-start md:items-center">
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full">
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
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full ">
            <label class="mb-2">Группы офисов</label>
            <mutiselect
              v-model="filters.officeGroups"
              :show-labels="false"
              :multiple="true"
              :options="officeGroups"
              placeholder="Выберите группы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div
            v-if="$root.user.role === 'admin'"
            class="flex flex-col align-middle mx-2 my-2 md:my-0 w-full"
          >
            <label class="mb-2">Филиалы</label>
            <mutiselect
              v-model="filters.branches"
              :show-labels="false"
              :multiple="true"
              :options="branches"
              placeholder="Выберите филиалы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col my-2 mx-2 align-middle md:my-0">
            <label class="mb-2">Разбивать по UTM source</label>
            <toggle v-model="filters.groupByUtmSource"></toggle>
          </div>
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-1/6">
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
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'office-performance-copy',
  components: {
    DatePicker,
  },
  data: () => ({
    filters:{
      dates:`${moment().format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      offers:[],
      offices:[],
      branches:[],
      officeGroups:[],
      groupByUtmSource: false,
    },
    offers:[],
    offices:[],
    branches:[],
    officeGroups:[],
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
    },
    isBusy:false,
  }),
  computed: {
    period() {
      const dates = this.filters.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        since: this.period.since,
        until: this.period.until,
        offers:this.filters.offers === null ? null : this.filters.offers.map(offer => offer.id),
        offices:this.filters.offices === null ? null : this.filters.offices.map(office => office.id),
        branches:this.filters.branches === null ? null : this.filters.branches.map(branch => branch.id),
        officeGroups:this.filters.officeGroups === null ? null : this.filters.officeGroups.map(group => group.id),
        groupByUtmSource: this.filters.groupByUtmSource,
      };
    },
  },
  created() {
    this.load();
    if(this.$root.isAdmin) {
      axios.get('/api/offices')
        .then(response => this.offices = response.data)
        .catch(err => console.error);
    }
    axios.get('/api/offers')
      .then(response => this.offers = response.data)
      .catch(error => console.error);
    if (this.$root.user.role === 'admin') {
      axios.get('/api/branches')
        .then(response => this.branches = response.data)
        .catch(error => console.error);
    }
    axios.get('/api/office-groups')
      .then(r => this.officeGroups = r.data)
      .catch(error => this.$toast.error({title: 'Не удалось загрузить группы офисов', message: error.response.data.message}));
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/office-performance-copy', { params: this.cleanFilters })
        .then(response => this.report = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        ).finally(() => this.isBusy = false);
    },
  },
};
</script>
