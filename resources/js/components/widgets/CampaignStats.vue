<template>
  <div class="flex flex-col flex-shrink-0 bg-white rounded shadow mb-8">
    <div class="flex justify-between items-center p-3 flex-col md:flex-row">
      <h3 class="text-gray-700 flex text-left w-full mb-4 md:mb-0">
        Статистика по кампании
      </h3>
      <div class="flex flex-wrap items-center justify-between md:justify-end w-full">
        <date-picker
          id="us-datetime"
          v-model="date"
          class="w-40 flex px-3 py-2 pr-4 mx-2 border border-gray-400 rounded text-gray-700"
          placeholder="Дата"
          :config="pickerConfig"
        ></date-picker>
        <select
          v-model="campaign"
          name="us-key"
          class="w-40 flex text-gray-700 mt-4 md:mt-0 px-3 py-2 pr-4"
        >
          <option :value="null">
            Все
          </option>
          <option
            v-for="(campaign,index) in campaigns"
            :key="index"
            :value="campaign"
            v-text="campaign"
          ></option>
        </select>
        <select
          v-if="$root.user.role ==='admin'"
          v-model="user"
          name="us-user"
          class="w-40 ml-2 mt-4 md:mt-0 self-end flex text-gray-700"
        >
          <option :value="null">
            Все
          </option>
          <option
            v-for="user in users"
            :key="user.id"
            :value="user.id"
            v-text="user.name"
          ></option>
        </select>
      </div>
    </div>
    <div class="overflow-x-auto overflow-y-hidden flex w-full">
      <report-table
        v-if="hasStats"
        :report="report"
      ></report-table>
      <div
        v-else
        class="w-full flex border-t justify-center bg-white p-4 font-medium text-xl text-gray-700"
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
          Нет статистики
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
  name: 'campaign-stats',
  components:{
    DatePicker,
  },
  data:()=>({
    report:{
      headers: [],
      rows: [],
      summary: [],
    },
    isBusy:false,
    users:[],
    user:null,
    pickerConfig: {
      maxDate: moment().format('YYYY-MM-DD'),
    },
    date: moment().format('YYYY-MM-DD'),
    campaign:null,
    campaigns:[],

  }),
  computed:{
    hasStats(){
      return this.report.rows.length > 0;
    },
  },
  watch:{
    user(){
      this.load();
    },
    date(){
      this.load();
    },
    campaign(){
      this.load();
    },
  },
  created() {
    axios.get('/api/utm-campaigns')
      .then(response => {
        this.campaigns = response.data;
      })
      .catch(err => console.error(err));
    if(this.$root.isAdmin){
      axios.get('/api/users',{params:{all:true}})
        .then(response => this.users = response.data)
        .catch(error => console.error);
    }
    this.load();
  },
  methods:{
    load(){
      this.isBusy = true;
      axios.get('/api/reports/campaign-stats',{params:{user:this.user, date:this.date, campaign:this.campaign}})
        .then(response => this.report = response.data)
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
    }
    td{
        @apply py-4;
        @apply px-2;
        @apply border-b;
    }
</style>
