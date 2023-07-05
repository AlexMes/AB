<template>
  <div class="w-full">
    <div v-if="hasAds">
      <ad-list-item
        v-for="ad in ads"
        :key="ad.id"
        :ad="ad"
      ></ad-list-item>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Объявлений не найдено</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'domain-ads',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    ads: [],
  }),
  computed:{
    hasAds() {
      return this.ads.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios
        .get(`/api/domains/${this.id}/ads`)
        .then(({ data }) => (this.ads = data))
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: err.response.data.message,
          }),
        );
    },
  },
};
</script>
