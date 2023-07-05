<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Кампании
      </h1>
      <div class="flex items-center">
        <div>
          <button
            class="button btn-primary mr-2"
            @click="$modal.show('stop-campaigns-modal')"
          >
            Остановить
          </button>
        </div>
        <router-link
          :to="{name:'campaigns.create'}"
          class="button btn-primary"
        >
          Добавить
        </router-link>
      </div>
    </div>
    <div class="flex w-full mb-6">
      <input
        v-model="search"
        type="search"
        class="w-full border-b px-3 py-2 bg-transparent pb-1 text-grey-700 outline-none"
        placeholder="Поиск кампании"
        maxlength="50"
      />
    </div>
    <div
      v-if="hasCampaigns"
      class="bg-white shadow w-full"
    >
      <div class="w-full py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
        <div class="w-20"></div>
        <div class="w-1/3 flex">
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
        <div class="w-1/5 flex">
          Spend
        </div>
        <div class="w-1/5 flex">
          CPL
        </div>
      </div>
      <campaign-list-item
        v-for="campaign in campaigns"
        :key="campaign.id"
        :campaign="campaign"
        @campaign-changed="updateCampaign"
      >
      </campaign-list-item>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <stop-campaigns-modal></stop-campaigns-modal>
  </div>
</template>

<script>
import {set} from 'vue';
import Pagination from '../../components/pagination';
import StopCampaignsModal from '../../components/campaigns/stop-campaigns-modal';

export default {
  name: 'campaigns-index',
  components: {StopCampaignsModal, Pagination},
  data:() => ({
    campaigns: [],
    response: null,
    search: null,
  }),
  computed:{
    hasCampaigns(){
      return this.campaigns.length > 0;
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
    updateCampaign(event) {
      const index = this.campaigns.findIndex(campaign => campaign.id === event.campaign.id);
      if (index !== -1) {
        set(this.campaigns, index, event.campaign);
      }
    },
    boot(){
      this.load();
    },
    load(page = 1){
      axios.get('/api/campaigns', {params: {search: this.search, page: page }})
        .then(response => {
          this.response = response.data;
          this.campaigns = response.data.data;
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить кампании.', message: e.response.data.message});
        });
    },
  },
};
</script>


