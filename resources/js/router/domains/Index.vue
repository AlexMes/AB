<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Домены
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
        <router-link
          v-if="$root.isAdmin"
          :to="{'name':'domains.create'}"
          class="button btn-primary flex items-center ml-3"
        >
          <fa-icon
            :icon="['far','plus']"
            class="fill-current mr-2"
          ></fa-icon> Добавить
        </router-link>
      </div>
    </div>
    <div class="mt-4 mb-8 flex flex-wrap">
      <div class="flex flex-col mr-4 my-2 xl:my-0">
        <label class="mb-2">Дата</label>
        <date-picker
          v-model="filters.effectiveDate"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите даты"
        ></date-picker>
      </div>
      <div class="flex flex-col mr-4 my-2 xl:my-0">
        <label
          for="linkTypeFilter"
          class="mb-2"
        >Тип домена</label>
        <select
          id="linkTypeFilter"
          v-model="filters.linkType"
        >
          <option
            v-for="linkType in linkTypes"
            :key="linkType.id"
            :value="linkType.id"
            v-text="linkType.name"
          ></option>
        </select>
      </div>
      <div class="flex flex-col mr-4 my-2 xl:my-0">
        <label
          for="offerFilter"
          class="mb-2"
        >Оффер</label>
        <select
          id="offerFilter"
          v-model="filters.offer_id"
        >
          <option value="all">
            Все
          </option>
          <option :value="null">
            Без оффера
          </option>
          <option
            v-for="offer in offers"
            :key="offer.id"
            :value="offer.id"
            v-text="offer.name"
          ></option>
        </select>
      </div>
      <div
        v-if="$root.user.role === 'admin' "
        class="flex flex-col mr-4 my-2 xl:my-0"
      >
        <label
          for="userFilter"
          class="mb-2"
        >Пользователь</label>
        <select
          id="userFilter"
          v-model="filters.user_id"
        >
          <option value="all">
            Все
          </option>
          <option :value="null">
            Без пользователя
          </option>
          <option
            v-for="user in users"
            :key="user.id"
            :value="user.id"
            v-text="user.name"
          ></option>
        </select>
      </div>
      <div class="flex flex-col mr-4 my-2 xl:my-0">
        <label
          for="spFilter"
          class="mb-2"
        >Safe page</label>
        <select
          id="spFilter"
          v-model="filters.sp_id"
        >
          <option value="all">
            Все
          </option>
          <option :value="null">
            Без СП
          </option>
          <option
            v-for="safePage in safePages"
            :key="safePage.id"
            :value="safePage.id"
            v-text="safePage.name"
          ></option>
        </select>
      </div>
      <div class="flex flex-col mr-4 my-2 xl:my-0">
        <label
          for="bpFilter"
          class="mb-2"
        >Black page</label>
        <select
          id="bpFilter"
          v-model="filters.bp_id"
        >
          <option value="all">
            Все
          </option>
          <option :value="null">
            Без БП
          </option>
          <option
            v-for="blackPage in blackPages"
            :key="blackPage.id"
            :value="blackPage.id"
            v-text="blackPage.name"
          ></option>
        </select>
      </div>
      <div class="flex flex-col mr-4 my-2 md:my-0">
        <button
          type="button"
          class="button btn-secondary mt-6"
          @click="load(1)"
        >
          <fa-icon
            :icon="['far','filter']"
            class="fill-current mr-2"
            fixed-width
          ></fa-icon>Фильтровать
        </button>
      </div>
    </div>
    <div
      v-if="hasDomains"
    >
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>Дата</th>
              <th>url</th>
              <th>Баер</th>
              <th>Оффер</th>
              <th>СП</th>
              <th>БП</th>
              <th>Клоака</th>
              <th>Статус</th>
            </tr>
          </thead>
          <tbody>
            <domain-list-item
              v-for="domain in response.data"
              :key="domain.id"
              :domain="domain"
            ></domain-list-item>
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
      linkType: 'all',
      offer_id: 'all',
      user_id: 'all',
      sp_id: 'all',
      bp_id: 'all',
    },
    response: {},
    pickerConfig:{
      maxDate: moment().format('YYYY-MM-DD'),
    },
    offers:[],
    linkTypes:[
      {id:'all',name:'Все'},
      {id:'landing',name:'Ленд'},
      {id:'prelanding',name:'Преленд'},
      {id:'service',name:'Сервис'},
    ],
    users:[],
    landings:[],
    safePages:[],
    blackPages:[],
  }),
  computed:{
    hasDomains(){
      if (this.response.data) {
        return this.response.data.length > 0;
      }
      return this.response.length > 0;
    },
  },
  created(){
    this.loadFilters();
    this.load();
  },
  methods:{
    load(page = 1){
      axios.get('/api/domains', {params: {page: page, ...this.filters}})
        .then(response => {
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить домены.', message: err.response.data.message});
        });
    },
    search(needle){
      axios.get('/api/domains', {params: {
        search: needle,
      }})
        .then(response=>{
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось найти домены.', message: err.response.data.message});
        });
    },
    loadFilters(){
      if(this.$root.isAdmin){
        axios.get('/api/users',{params:{all:true}}).then(response => this.users = response.data);
      }
      axios.get('/api/offers').then(r => this.offers = r.data);
      axios.get('/api/pages/', {params: {all: true}}).then(r => {
        this.safePages = r.data.filter(page => page.type === 'safe');
        this.blackPages = r.data.filter(page => page.type === 'black');
      });

    },
  },
};
</script>

