<template>
  <div class="flex flex-col flex-shrink-0 bg-white rounded shadow">
    <div class="flex justify-between items-center p-3 flex-col md:flex-row">
      <h3 class="text-gray-700 flex text-left w-full mb-4 md:mb-0">
        Потери траффика
      </h3>
      <div class="flex items-center flex-wrap justify-between md:justify-end w-full">
        <date-picker
          id="tl-datetime"
          v-model="date"
          class="w-40 px-3 py-2 pr-4 mx-2 border border-gray-400 rounded text-gray-700"
          placeholder="Дата"
          :config="pickerConfig"
        ></date-picker>
      </div>
    </div>
    <report-table
      v-if="hasResults"
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
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';

export default {
  name: 'traffic-loss',
  components:{
    DatePicker,
  },
  data: () => ({
    report:{
      headers: [],
      rows: [],
      summary: [],
    },
    isBusy:false,
    pickerConfig: {},
    date: moment().format('YYYY-MM-DD'),
  }),
  computed:{
    hasResults(){
      return this.report.rows.length > 0;
    },
  },
  watch:{
    date(){
      this.load();
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      this.isBusy = true;
      axios.get('/api/reports/traffic-loss',{params: {date:this.date}})
        .then(response => this.report = response.data)
        .catch(error => this.$toast.error({title:'Ошибка',message:'Не удалось загрузить отчет'}))
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
