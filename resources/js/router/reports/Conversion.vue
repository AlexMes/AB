<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Conversion report
        </h1>
        <span>
          <span
            v-if="report.period"
            class="text-gray-600 font-medium mt-2 md:mt-0"
            v-text="`( ${report.period.since} - ${report.period.until} )`"
          ></span>
        </span>
      </div>
      <div class="flex my-6">
        <div class="flex w-5/6 flex-wrap md:flex-no-wrap items-start md:items-center">
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-auto ">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="filters.dates"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>

          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 w-auto w-1/5">
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
        </div>
        <div class="flex w-1/6 items-start md:items-center">
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
      class="bg-white shadow"
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
  name: 'conversion',
  components:{
    DatePicker,
  },

  data:()=>({
    isBusy: false,
    filters: {
      dates:`${moment().startOf('month').subtract(1, 'days').format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      users: [],
    },
    pickerConfig: {
      defaultDates: `${moment().startOf('month').subtract(1, 'days').format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    report: {
      headers: [],
      rows: [],
      summary: [],
      period:null,
    },
    users: [],
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
        users: this.filters.users === null ? null : this.filters.users.map(user => user.id),
      };
    },
  },

  created() {
    this.load();
    this.getUsers();
  },

  methods: {
    load() {
      this.isBusy = true;
      axios.get('/api/reports/conversion', {params:this.cleanFilters})
        .then(response => this.report = response.data)
        .catch(error => console.error)
        .finally(() => this.isBusy = false);
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
  },
};
</script>

