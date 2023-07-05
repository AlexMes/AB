<template>
  <div class="container mx-auto flex flex-col">
    <div class="flex w-full justify-between items-center mb-8">
      <h1 class="text-gray-700 flex">
        Доступы
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
        <router-link
          :to="{'name':'accesses.create'}"
          class="button btn-primary flex items-center ml-3"
        >
          <fa-icon
            :icon="['far','plus']"
            class="fill-current mr-2"
          ></fa-icon> Добавить
        </router-link>
      </div>
    </div>
    <div class="flex w-full mb-8">
      <div class="w-1/5 flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Дата получения</label>
        <date-picker
          id="datetime"
          v-model="filters.received_at"
          class="w-full px-1 py-2 border rounded text-gray-600"
          placeholder="Выберите дату"
        ></date-picker>
      </div>
      <div class="w-1/5 flex flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Поставщик</label>
        <mutiselect
          v-model="filters.suppliers"
          :options="filterSets.suppliers"
          :multiple="true"
          :allow-empty="true"
          :close-on-select="false"
          :clear-on-select="false"
          :show-labels="false"
          track-by="id"
          label="name"
        >
        </mutiselect>
      </div>
      <div class="w-1/5 flex flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Баер</label>
        <mutiselect
          v-model="filters.users"
          :options="filterSets.users"
          :multiple="true"
          :allow-empty="true"
          :close-on-select="false"
          :clear-on-select="false"
          :show-labels="false"
          track-by="id"
          label="name"
        >
        </mutiselect>
      </div>
      <div class="w-1/5 flex flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Тип </label>
        <mutiselect
          v-model="filters.types"
          :options="filterSets.accessTypes"
          :multiple="true"
          :allow-empty="true"
          :close-on-select="false"
          :clear-on-select="false"
          :show-labels="false"
          track-by="id"
          label="label"
        >
        </mutiselect>
      </div>
      <div class="w-1/5 flex flex-col px-2">
        <button
          type="button"
          class="button btn-primary mt-6"
          @click.prevent="load"
        >
          Применить
        </button>
      </div>
    </div>
    <div
      v-if="hasAccesses"
      class="flex flex-col w-full bg-white shadow"
    >
      <div>
        <!--headers-->
      </div>
      <accesses-list-item
        v-for="access in accesses"
        :key="access.id"
        :access="access"
      >
      </accesses-list-item>
    </div>
    <pagination
      v-if="hasAccesses"
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'access-index',
  components:{
    DatePicker,
  },
  data:() => ({
    response: null,
    filterSets:{
      users: [],
      suppliers:[],
      accessTypes: [
        {id:'farm', label:'Фарм'},
        {id:'brut', label:'Брут'},
        {id:'own',  label:'Личный'},
      ],
    },
    filters:{
      received_at: null,
      users: [],
      suppliers: [],
      types: [],
      search: null,
    },
  }),
  computed:{
    cleanFilters(){
      return {
        suppliers: this.filters.suppliers === null ? null : this.filters.suppliers.map(supplier => supplier.id),
        users: this.filters.users === null ? null : this.filters.users.map(user => user.id),
        types: this.filters.types === null ? null : this.filters.types.map(type => type.id),
        received_at: this.filters.received_at,
        search: this.filters.search,
      };
    },
    hasAccesses(){
      return this.response !== null && this.response.data.length > 0;
    },
    accesses(){
      if(this.hasAccesses){
        return this.response.data;
      }

      return [];
    },
  },
  created() {
    this.load();
    this.getUsers();
    this.getSuppliers();
  },
  methods:{
    load(page = 1){
      axios.get('/api/accesses',{params:{...this.cleanFilters, ...{page:page}} })
        .then(response => this.response = response.data)
        .catch(error => this.$toast.error({title:'Не удалось загрузить доступы', message:error.response.data.message}));
    },
    getUsers(){
      axios.get('/api/users', {params:{all:true}})
        .then(response=>this.filterSets.users = response.data)
        .catch(error => this.$toast.error({title:'Не удалось загрузить пользователей', message:error.response.data.message}));
    },
    getSuppliers(){
      axios.get('/api/suppliers')
        .then(response => this.filterSets.suppliers = response.data)
        .catch(error => this.$toast.error({title:'Не удалось загрузить поставщиков',message:error.response.data.message}));
    },
    search(needle) {
      this.filters.search = needle;
      this.load();
    },
  },
};
</script>
