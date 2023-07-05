<template>
  <div class="container mx-auto flex flex-col">
    <div class="flex w-full mb-8">
      <div class="w-1/5 flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Период</label>
        <date-picker
          id="datetime"
          v-model="date"
          class="w-full px-1 py-2 mt-1 border rounded text-gray-600"
          placeholder="Дата"
          :config="pickerConfig"
        ></date-picker>
      </div>

      <div class="w-1/5 flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Причина</label>
        <mutiselect
          v-model="filters.reasons"
          :show-labels="false"
          :options="reasons"
          :multiple="true"
          placeholder="Выберите причину"
        ></mutiselect>
      </div>

      <div class="w-1/5 flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Баеры</label>
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

      <div class="w-1/5 flex flex-col justify-end px-2">
        <search-field @search="search"></search-field>
      </div>

      <div class="w-1/5 flex flex-col px-2">
        <button
          type="button"
          class="button btn-primary mt-7"
          :disabled="isBusy"
          @click.prevent="load"
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

    <div class="overflow-x-auto overflow-y-hidden flex w-full">
      <div
        v-if="hasDisapprovals"
        class="flex flex-col w-full"
      >
        <div
          class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold"
        >
          <div class="w-3/12">
            Date
          </div>
          <div class="w-5/12">
            Ad
          </div>
          <div class="w-4/12">
            Reason
          </div>
        </div>
        <ads-disapprovals-list-item
          v-for="disapproval in disapprovals"
          :key="disapproval.id"
          :disapproval="disapproval"
        ></ads-disapprovals-list-item>
      </div>
      <div
        v-else
        class="w-full flex justify-center bg-white p-4 font-medium text-xl text-gray-700"
      >
        <span v-if="isBusy">
          <fa-icon
            :icon="['far','spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon>Загрузка
        </span>
        <span
          v-else
          class="flex text-center"
        >
          Нет логов
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';

export default {
  name: 'logs-ads-disapprovals',
  components:{
    DatePicker,
  },
  data:() => ({
    disapprovals:[],
    isBusy:false,
    pickerConfig: {
      /*defaultDates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
        'YYYY-MM-DD',
      )}`,*/
      minDate: '2019-12-02',
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    date: `${moment().format('YYYY-MM-DD')} to ${moment().format(
      'YYYY-MM-DD',
    )}`,
    needle: null,
    reasons: [],
    users: [],
    filters: {
      reasons: [],
      users:[],
    },
  }),
  computed:{
    hasDisapprovals(){
      return this.disapprovals.length > 0;
    },
    period() {
      const dates = this.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        since: this.period.since,
        until: this.period.until,
        search: this.needle,
        reasons: this.filters.reasons,
        users: this.filters.users === null ? null : this.filters.users.map(user => user.id),
      };
    },
  },
  watch:{
    needle() {
      this.load();
    },
  },
  created() {
    this.load();
    this.getReasons();
    this.getUsers();
  },
  methods:{
    load() {
      this.isBusy = true;
      axios.get('/api/ads/disapprovals',{params: this.cleanFilters})
        .then(response => this.disapprovals = response.data)
        .catch(error => console.error)
        .finally(() => this.isBusy = false);
    },
    search(needle) {
      this.needle = needle;
    },
    getReasons() {
      axios.get('/api/ads/disapproval-reasons')
        .then(response => this.reasons = response.data)
        .catch(error => this.$toast.error({
          title: 'Ошибка',
          message: 'Не удалось загрузить причины.',
        }))
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
            title: 'Не удалось загрузить баеров',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
<style>
    th{
        @apply .text-left;
        @apply px-2;
        @apply py-3;
        @apply whitespace-no-wrap;
    }
    td{
        @apply py-4;
        @apply px-2;
        @apply border-b;
        @apply whitespace-no-wrap;
    }
</style>
