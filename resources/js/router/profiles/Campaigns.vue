<template>
  <div>
    <div v-if="hasCampaigns">
      <div class="w-full py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
        <div class="w-20"></div>
        <div class="w-1/3 flex">
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
      <campaign-list-item
        v-for="campaign in campaigns"
        :key="campaign.id"
        :campaign="campaign"
        @campaign-changed="updateCampaign"
      ></campaign-list-item>
    </div>
    <div
      v-else
      class="text-center"
    >
      <h1 class="text-gray-700">
        Кампаний не найдено
      </h1>
    </div>
  </div>
</template>

<script>
import {set} from 'vue';

export default {
  name: 'campaigns',
  props:{
    id:{
      type: [Number, String],
      required: true,
      default: null,
    },
  },
  data:() => ({
    campaigns: [],
  }),
  computed:{
    hasCampaigns(){
      return this.campaigns.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios.get(`/api/profiles/${this.id}/campaigns`)
        .then(response => this.campaigns = response.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить кампании.', message: err.response.data.message});
        });
    },
    updateCampaign(event) {
      const index = this.campaigns.findIndex(campaign => campaign.id === event.campaign.id);
      if (index !== -1) {
        set(this.campaigns, index, event.campaign);
      }
    },
  },
};
</script>

