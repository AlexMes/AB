<template>
  <div>
    <div v-if="hasAds">
      <div class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold">
        <div class="w-1/6 flex">
          Название
        </div>
        <div class="w-1/6 flex">
          Аккаунт
        </div>
        <div class="w-1/6 flex">
          Кампания
        </div>
        <div class="w-1/6 flex">
          Адсет
        </div>
        <div class="w-1/6 flex">
          Статус
        </div>
        <div class="w-1/6 flex">
        </div>
      </div>
      <ad-list-item
        v-for="ad in ads"
        :key="ad.id"
        :ad="ad"
      ></ad-list-item>
    </div>
  </div>
</template>

<script>
export default{
  name: 'profile-ads',
  props:{
    id:{
      type: Number,
      required: true,
      default: null,
    },
  },
  data:()=>({
    ads: [],
  }),
  computed:{
    hasAds(){
      return this.ads.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios.get(`/api/profiles/${this.id}/ads`)
        .then(response => this.ads = response.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить обьявления.', message: err.response.data.message});
        });
    },
  },
};
</script>

<style scoped>

</style>
