<template>
  <div class="container mx-auto flex flex-col">
    <div class="flex justify-between w-full mb-8">
      <div class="w-1/5 flex-col">
        <label class="flex w-full font-semibold mb-2">Период</label>
        <date-picker
          id="datetime"
          v-model="date"
          class="w-full px-1 py-2 mt-1 border rounded text-gray-600"
          placeholder="Дата"
          :config="pickerConfig"
        ></date-picker>
      </div>

      <div class="w-1/5 flex flex-col">
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

    <div class="overflow-x-auto overflow-y-hidden flex flex-col w-full">
      <div
        v-if="hasStoppedAdsets"
        class="flex flex-col w-full"
      >
        <div
          class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold"
        >
          <div class="w-2/12">
            Profile
          </div>
          <div class="w-1/12">
            Buyer
          </div>
          <div class="w-3/12">
            Account
          </div>
          <div class="w-4/12">
            Link
          </div>
          <div class="w-1/12">
            Spend
          </div>
          <div class="w-1/12">
            CPL
          </div>
        </div>
        <stopped-adsets-list-item
          v-for="adset in stoppedAdsets"
          :key="adset.id"
          :adset="adset"
        ></stopped-adsets-list-item>
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
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';
import StoppedAdsetsListItem from '../../components/logs/stopped-adsets-list-item';

export default {
  name: 'logs-stopped-adsets',
  components:{
    StoppedAdsetsListItem,
    DatePicker,
  },
  data:() => ({
    response: null,
    stoppedAdsets:[],
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
  }),
  computed:{
    hasStoppedAdsets(){
      return this.stoppedAdsets.length > 0;
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
      };
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1) {
      this.isBusy = true;
      axios.get('/api/stopped-adsets',{params: {page, ...this.cleanFilters}})
        .then(response => {
          this.response = response.data;
          this.stoppedAdsets = response.data.data;
        })
        .catch(error => console.error)
        .finally(() => this.isBusy = false);
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
