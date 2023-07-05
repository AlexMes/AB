<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Telegram performance report
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
            <label class="mb-2">Уровень</label>
            <select
              v-model="level"
            >
              <option
                v-for="level in filterSets.levels"
                :key="level.id"
                :value="level.id"
                v-text="level.name"
              ></option>
            </select>
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
            <label class="mb-2">Тематика</label>
            <select
              v-model="subject"
            >
              <option :value="null">
                Все
              </option>
              <option
                v-for="(subj) in filterSets.subjects"
                :key="subj.id"
                :value="subj.id"
                v-text="subj.name"
              ></option>
            </select>
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
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'telegram-performance',
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
      levels: [
        {id:'campaign', name:'Кампания'},
      ],
      utmCampaigns: [],
      subjects:[],
    },
    level:'campaign',
    isBusy:false,
    utmCampaign: null,
    subject: null,
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
        level: this.level,
        since: this.period.since,
        until: this.period.until,
        campaign: this.utmCampaign,
        subject: this.subject,
      };
    },
  },
  created() {
    this.load();
    this.getUtm();
    this.getTelegramSubjects();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/telegram-performance', { params: this.cleanFilters })
        .then(response => this.report = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        ).finally(() => this.isBusy = false);
    },
    getUtm(){
      axios.get('/api/utm-campaigns')
        .then(r => this.filterSets.utmCampaigns = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },
    getTelegramSubjects() {
      axios
        .get('/api/telegram/subjects')
        .then(response => (this.filterSets.subjects = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить тематики',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
