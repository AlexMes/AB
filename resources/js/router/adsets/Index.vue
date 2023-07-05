<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Адсеты
      </h1>
      <div class="flex items-center">
        <div>
          <button
            class="button btn-primary mr-2"
            @click="$modal.show('stop-adsets-modal')"
          >
            Остановить
          </button>
        </div>
      </div>
    </div>
    <div class="flex w-full mb-6">
      <input
        v-model="search"
        type="search"
        class="w-full border-b px-3 py-2 bg-transparent pb-1 text-grey-700 outline-none"
        placeholder="Поиск адсета"
        maxlength="50"
      />
    </div>
    <div
      v-if="hasAdSets"
      class="bg-white rounded shadow"
    >
      <div class="w-full py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
        <div class="w-20"></div>
        <div class="w-2/3 flex">
          Название
        </div>
        <div class="w-1/5 flex">
          Аккаунт
        </div>
        <div class="w-1/5 flex">
          Профиль
        </div>
        <div class="w-1/6 flex">
          Статус
        </div>
        <div class="w-1/5 flex">
          Бюджет
        </div>
        <div class="w-1/6 flex">
          Spend/CPL
        </div>
        <div class="w-1/5">
        </div>
      </div>
      <adset-list-item
        v-for="adset in adsets"
        :key="adset.id"
        :adset="adset"
        @adset-changed="updateAdset"
      >
      </adset-list-item>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <stop-adsets-modal></stop-adsets-modal>
  </div>
</template>

<script>
import {set} from 'vue';
import StopAdsetsModal from '../../components/adsets/stop-adsets-modal';

export default {
  name: 'adsets-index',
  components: {StopAdsetsModal},
  data:() => ({
    adsets: [],
    response: null,
    search:null,
  }),
  computed:{
    hasAdSets(){
      return this.adsets.length > 0;
    },
  },
  watch:{
    search(){
      this.load();
    },
  },
  created() {
    this.boot();
  },
  methods:{
    updateAdset(event) {
      const index = this.adsets.findIndex(adset => adset.id === event.adset.id);
      if (index !== -1) {
        set(this.adsets, index, event.adset);
      }
    },
    boot(){
      this.load();
    },
    load(page = 1){
      axios
        .get('/api/adsets',{params: {search: this.search, page: page }})
        .then(response => {
          this.response = response.data;
          this.adsets = response.data.data;
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить адсеты.', message: e.response.data.message});
        });
    },
  },
};
</script>


