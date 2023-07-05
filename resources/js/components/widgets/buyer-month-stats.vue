<template>
  <div class="flex flex-col flex-shrink-0 bg-white rounded shadow mb-8">
    <div class="flex justify-between items-center p-3 flex-col md:flex-row">
      <h3 class="text-gray-700 flex text-left w-full mb-4 md:mb-0">
        Статистика по байерам за текущий месяц
      </h3>
      <div class="flex flex-wrap items-center justify-between md:justify-end w-full">
        <select
          v-if="$root.isAdmin && users.length > 1"
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
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'buyer-month-stats',
  data:()=>({
    report:{
      headers: [],
      rows: [],
      summary: [],
    },
    isBusy:false,
    users:[],
    user:null,
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
  },
  created() {
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
      axios.get('/api/reports/buyers-month-stats',{params:{user:this.user}})
        .then(response => this.report = response.data)
        .catch(error => console.error)
        .finally(() => this.isBusy = false);

    },
  },
};
</script>
