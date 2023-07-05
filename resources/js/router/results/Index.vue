<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Результаты
      </h1>
      <div
        v-if="$root.isAdmin"
        class="flex items-center"
      >
        <router-link
          :to="{name:'results.create'}"
          class="button btn-primary"
        >
          Добавить
        </router-link>
      </div>
    </div>
    <div class="flex flex-no-wrap items-center my-6">
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Период</label>
        <date-picker
          id="datetime"
          v-model="filters.dates"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите даты"
        ></date-picker>
      </div>
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Офферы</label>
        <mutiselect
          v-model="filters.offers"
          :show-labels="false"
          :multiple="true"
          :options="offers"
          placeholder="Выберите офферы"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Офисы</label>
        <mutiselect
          v-model="filters.offices"
          :show-labels="false"
          :multiple="true"
          :options="offices"
          placeholder="Выберите офисы"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="flex flex-col ml-2 w-1/5">
        <button
          class="button btn-primary text-lg -mb-3 mt-2"
          :class="{'bg-gray-700' : isBusy}"
          :disabled="isBusy"
          @click="load"
        >
          <span v-if="isBusy"><fa-icon
            :icon="['far','spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon> Загрузка</span>
          <span v-else>Применить</span>
        </button>
      </div>
    </div>
    <div v-if="hasResults">
      <table class="table table-auto w-full">
        <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full">
          <tr class="px-3">
            <th class="px-2 py-3 pl-5">
              #
            </th>
            <th class="px-2 py-3">
              Дата
            </th>
            <th class="px-2 py-3">
              Оффер
            </th>
            <th class="px-2 py-3">
              Офис
            </th>
            <th class="px-2 py-3">
              LEADS
            </th>
            <th class="px-2 py-3">
              NA
            </th>
            <th class="px-2 py-3">
              REJECT
            </th>
            <th class="px-2 py-3">
              WRONG NB
            </th>
            <th class="px2 py-3">
              DEMO
            </th>
            <th class="px-2 py-3">
              FTD
            </th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody class="w-full">
          <result-list-item
            v-for="result in results"
            :key="result.id"
            :result="result"
            @gone="load"
          >
          </result-list-item>
        </tbody>
      </table>
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

export default {
  name: 'index',
  components:{
    DatePicker,
  },
  data:() => ({
    response: null,
    isBusy:false,
    filters:{
      dates: null,
      offers:[],
      offices:[],
    },
    pickerConfig:{
      defaultDates: null,
      mode: 'range',
    },
    offers:[],
    offices:[],
  }),
  computed:{
    hasResults(){
      return this.response !== null && this.response.data.length > 0;
    },
    results(){
      if(this.hasResults){
        return this.response.data;
      }
      return null;
    },
    period(){
      if(this.filters.dates == null){
        return null;
      }
      const dates = this.filters.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters(){
      return {
        since: this.period === null ? null : this.period.since,
        until : this.period ===null ? null : this.period.until,
        offers:this.filters.offers === null ? null : this.filters.offers.map(offer => offer.id),
        offices:this.filters.offices === null ? null : this.filters.offices.map(office => office.id),
      };
    },
  },
  created() {
    this.load();
    axios.get('/api/offices').then(response => this.offices = response.data).catch(error => console.error);
    axios.get('/api/offers').then(response => this.offers = response.data).catch(error => console.error);
  },
  methods:{
    load(page = 1){
      this.isBusy = true;
      axios.get('/api/results', {params: { ...{page: page}, ...this.cleanFilters }}).then(response => {
        this.response = response.data;
      }).catch(e => {
        this.$toast.error({title: 'Не удалось загрузить результаты.', message: e.response.data.message});
      })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

<style>
    th{
        @apply .text-left;
    }
</style>
