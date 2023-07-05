<template>
  <div class="bg-white py-4 flex items-center border-b">
    <div class="flex w-full items-center">
      <div
        class="flex justify-center w-20"
      >
        <fa-icon
          v-if="isBusy"
          :icon="['far','spinner']"
          class="fill-current text-gray-700 text-lg"
          spin
          fixed-width
        ></fa-icon>
        <fa-icon
          v-else
          class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
          :icon="['far', icon]"
          fixed-width
          @click.prevent="toggle"
        ></fa-icon>
      </div>
      <div class="flex flex-col w-1/3">
        <span
          class="font-medium text-base"
          v-text="campaign.name"
        ></span>
        <span
          class="text-gray-600 text-xs mt-2"
          v-text="`ID: ${campaign.id}`"
        ></span>
      </div>
      <div class="flex items-center w-1/5 text-left">
        <router-link
          v-if="campaign.account"
          :to="{ name: 'accounts.show', params: { id: campaign.account_id }}"
          v-text="campaign.account.name"
        ></router-link>
      </div>
      <div
        v-if="campaign.account && campaign.account.profile"
        class="flex items-center w-1/5 text-left"
      >
        <router-link
          :to="{ name: 'profile.general', params: { id: campaign.account.profile.id }}"
          v-text="campaign.account.profile.name"
        ></router-link>
      </div>
      <div class="flex items-center w-1/6 text-left">
        <span v-text="status"></span>
      </div>
      <div class="flex flex-col w-1/5 text-left">
        <span class="text-gray-600 text-xs">
          дневной / оставшийся
        </span>
        <div class="mt-1">
          <span v-text="campaign.daily_budget || 0"></span> /
          <span v-text="campaign.budget_remaining || 0"></span>
        </div>
      </div>
      <div class="flex flex-col w-1/5 text-left">
        <div class="mt-1">
          <span v-text="campaign.spend || 0"></span>
        </div>
      </div>
      <div class="flex flex-col w-1/5 text-left">
        <div class="mt-1">
          <span v-text="campaign.cpl || 0"></span>
        </div>
      </div>
      <!--      <div class="flex justify-center w-1/5">-->
      <!--        <fa-icon-->
      <!--          v-if="campaign.can_update"-->
      <!--          :icon="['far','dollar-sign']"-->
      <!--          class="mx-3 fill-current text-gray-700 hover:text-teal-700 cursor-pointer"-->
      <!--        ></fa-icon>-->
      <!--        <fa-icon-->
      <!--          v-if="campaign.can_update"-->
      <!--          :icon="['far','pencil-alt']"-->
      <!--          class="mx-3 fill-current text-gray-700 hover:text-teal-700 cursor-pointer"-->
      <!--        ></fa-icon>-->
      <!--      </div>-->
    </div>
  </div>
</template>

<script>
export default {
  name: 'campaign-list-item',
  props:{
    campaign: {
      type: Object,
      required: true,
    },
  },
  data:()=>({
    isBusy:false,
  }),
  computed:{
    isActive(){
      return ['ACTIVE','IN_PROCESS'].includes(this.campaign.effective_status);
    },
    icon(){
      if(this.campaign.effective_status === 'ACTIVE'){
        return 'pause';
      }
      if (['PAUSED','ADSET_PAUSED'].includes(this.campaign.effective_status)){
        return 'play';
      }

      return 'pencil-alt';
    },
    status(){
      switch (this.campaign.effective_status) {
      case 'ACTIVE':
        return 'Активная';
      case 'PAUSED':
        return  'Остановлена';
      case 'DELETED':
        return 'Удалена';
      case 'ARCHIVED':
        return 'В архиве';
      case 'IN_PROCESS':
        return 'В прогрессе';
      case 'WITH_ISSUES':
        return 'С проблемами';
      default:
        return this.campaign.effective_status;
      }
    },
  },
  methods:{
    start(){
      this.isBusy = true;
      setTimeout(null,300);
      axios.post(`/api/campaigns/${this.campaign.id}/status`)
        .then(response =>{
          this.$emit('campaign-changed',{campaign : response.data});
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось обновить статус.', message: err.response.data.message});
        }).finally(() => this.isBusy = false);
    },
    stop(){
      this.isBusy = true;
      axios.delete(`/api/campaigns/${this.campaign.id}/status`)
        .then(response => {
          this.$emit('campaign-changed', {campaign : response.data});
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось обновить статус.', message: err.response.data.message});
        }).finally(() => this.isBusy = false);
    },
    toggle(){
      this.isActive ? this.stop() : this.start();
    },
  },
};
</script>

<style scoped>

</style>
