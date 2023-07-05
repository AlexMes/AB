<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Серверы
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
      </div>
    </div>
    <div
      v-if="hasServers"
    >
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>Название</th>
              <th>Поставщик</th>
              <th>IP Address</th>
              <th>Аккаунт</th>
              <th>Кол-во ссылок</th>
            </tr>
          </thead>
          <tbody>
            <server-list-item
              v-for="server in response.data"
              :key="server.id"
              :server="server"
            ></server-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'index',
  components:{
    DatePicker,
  },
  data:() => ({
    filters:{
      effectiveDate:  null,
    },
    response: {},
    pickerConfig:{
      maxDate: moment().format('YYYY-MM-DD'),
    },
  }),
  computed:{
    hasServers(){
      if (this.response.data) {
        return this.response.data.length > 0;
      }
      return this.response.length > 0;
    },
  },
  beforeRouteEnter(from, to, next){
    next(vm => vm.load());
  },
  methods:{
    load(page = 1){
      axios.get('/api/servers', {params: {page: page, ...this.filters}})
        .then(response => {
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить серверы.', message: err.response.data.message});
        });
    },
    search(needle){
      axios.get('/api/servers', {params: {
        search: needle,
      }})
        .then(response=>{
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось найти серверы.', message: err.response.data.message});
        });
    },
  },
};
</script>

