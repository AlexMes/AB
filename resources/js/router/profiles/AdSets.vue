<template>
  <div>
    <div v-if="hasAdSets">
      <div class="w-full py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
        <div class="w-20"></div>
        <div class="w-2/3 flex">
          Название
        </div>
        <div class="w-1/5 flex">
          Аккаунт
        </div>
        <div class="w-1/6 flex">
          Статус
        </div>
        <div class="w-1/5 flex">
          Бюджет
        </div>
        <div class="w-1/5">
        </div>
      </div>
      <adset-list-item
        v-for="adset in adsets"
        :key="adset.id"
        :adset="adset"
        @adset-changed="updateAdset"
      ></adset-list-item>
    </div>
    <div
      v-else
      class="text-center"
    >
      <h1 class="text-gray-700">
        Адсетов не найдено
      </h1>
    </div>
  </div>
</template>

<script>
import {set} from 'vue';

export default {
  name: 'ad-sets',
  props:{
    id:{
      type: Number,
      required: true,
      default: null,
    },
  },
  data:()=>({
    adsets: [],
  }),
  computed:{
    hasAdSets(){
      return this.adsets.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios.get(`/api/profiles/${this.id}/adsets`)
        .then(response => this.adsets = response.data)
        .catch(err=> {
          this.$toast.error({title: 'Не удалось загрузить адсеты.', message: err.response.data.message});
        });
    },
    updateAdset(event) {
      const index = this.adsets.findIndex(adset => adset.id === event.adset.id);
      if (index !== -1) {
        set(this.adsets, index, event.adset);
      }
    },
  },
};
</script>

