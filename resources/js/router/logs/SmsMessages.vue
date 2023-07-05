<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <div class="flex w-full px-2">
        <search-field
          class="w-3/4"
          @search="search"
        ></search-field>
        <div class="w-1/4 ml-2">
          <mutiselect
            v-model="filterSets.branch"
            :show-labels="false"
            :multiple="false"
            :options="branches"
            placeholder="Выберите филиал"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>
        <div class="w-1/4 ml-2">
          <date-picker
            id="datetime"
            v-model="dates"
            class="w-full px-1 py-2 border rounded text-gray-600"
            :config="pickerConfig"
            placeholder="Выберите даты"
          ></date-picker>
        </div>
      </div>
    </div>
    <div class="flex w-full mb-6">
      <div
        v-for="stat in stats"
        :key="stat.label"
        class="w-1/4 px-2"
      >
        <stat-card
          class="w-full"
          :label="stat.label"
          :measure="stat.measure"
        ></stat-card>
      </div>
    </div>
    <div
      v-if="hasMessages"
      class="w-full px-2"
    >
      <div class="w-full rounded shadow-md">
        <sms-messages-list-item
          v-for="message in messages"
          :key="message.id"
          :message="message"
        >
        </sms-messages-list-item>
      </div>
    </div>
    <div
      v-else
      class="flex justify-center w-full"
    >
      <span class="text-lg">Нет сообщений</span>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'logs-sms-messages',
  components: {
    DatePicker,
  },
  data: () => ({
    messages: [],
    response: {},
    stats: [],
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
    filterSets: {
      branch: {},
    },
    branches: [],
  }),
  computed: {
    hasMessages() {
      return this.messages.length > 0;
    },
    period() {
      const dates = this.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  watch: {
    period() {
      this.load();
      this.loadStats();
    },
    'filterSets.branch'(value, old) {
      this.load();
      this.loadStats();
    },
  },
  created() {
    this.load();
    this.loadStats();
    this.getBranches();
  },
  methods: {
    load(page = 1) {
      axios
        .get('/api/sms/messages', {
          params: {
            page: page,
            since: this.period.since,
            until: this.period.until,
            branch: this.filterSets.branch === null ? null : this.filterSets.branch.id,
          },
        })
        .then(response => {
          this.response = response.data;
          this.messages = response.data.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить.',
            message: err.response.data.message,
          });
        });
    },
    loadStats() {
      axios
        .get('/api/reports/sms-stats', {
          params: {
            since: this.period.since,
            until: this.period.until,
            branch: this.filterSets.branch === null ? null : this.filterSets.branch.id,
          },
        })
        .then(response => {
          this.stats = response.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить.',
            message: err.response.data.message,
          });
        });
    },
    search(needle){
      axios.get('/api/sms/messages', {params: {
        search: needle,
        branch: this.filterSets.branch === null ? null : this.filterSets.branch.id,
      }})
        .then(response => {
          this.response = response.data;
          this.messages = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось найти СМС.', message: err.response.data.message});
        });
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
  },
};
</script>

<style scoped>
    th{
        @apply px-2;
        @apply py-3;
        @apply text-left;
    }
</style>
