<template>
  <div class="bg-white py-4 flex items-center border-b flex flex-col">
    <div
      class="flex w-full items-center"
      :class="{'mb-2': hasIssues}"
    >
      <div
        class="flex justify-center w-24"
      >
        <fa-icon
          v-if="isBusy"
          :icon="['far','spinner']"
          class="fill-current text-gray-700 text-lg"
          spin
          fixed-width
        ></fa-icon>
        <div v-else>
          <fa-icon
            class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
            :icon="['far', icon]"
            fixed-width
            @click.prevent="toggle"
          ></fa-icon>
          <fa-icon
            class="fill-current cursor-pointer text-lg hover:text-teal-700"
            :class="{'text-green-400': adset.start_midnight}"
            :icon="['far', 'ad']"
            fixed-width
            @click.prevent="toggleStartingMidnight"
          ></fa-icon>
        </div>
      </div>
      <div class="w-2/3 flex flex-col">
        <span v-text="adset.name"></span>
        <span
          class="text-gray-600 text-xs mt-2"
          v-text="`ID: ${adset.id}`"
        ></span>
      </div>
      <div
        class="flex items-center w-1/5 text-left"
      >
        <router-link
          v-if="adset.account"
          :to="{ name: 'accounts.show', params: { id: adset.account_id }}"
          v-text="adset.account.name"
        ></router-link>
      </div>
      <div
        v-if="adset.account && adset.account.profile"
        class="flex items-center w-1/5 text-left"
      >
        <router-link
          :to="{ name: 'profile.general', params: { id: adset.account.profile.id }}"
          v-text="adset.account.profile.name"
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
          <span v-text="adset.daily_budget || 0"></span> /
          <span v-text="adset.budget_remaining || 0"></span>
        </div>
      </div>
      <div
        v-if="adset.spend !== undefined"
        class="flex flex-col w-1/6 text-left"
      >
        <div class="mt-1">
          <span v-text="adset.spend || 0"></span> /
          <span v-text="adset.cpl || 0"></span>
        </div>
      </div>
      <div class="flex justify-center w-1/5">
        <fa-icon
          v-if="adset.can_update"
          :icon="['far','dollar-sign']"
          class="mx-3 fill-current text-gray-700 hover:text-teal-700 cursor-pointer"
        ></fa-icon>
        <fa-icon
          v-if="adset.can_update"
          :icon="['far','pencil-alt']"
          class="mx-3 fill-current text-gray-700 hover:text-teal-700 cursor-pointer"
        ></fa-icon>
        <fa-icon
          v-if="hasIssues"
          :icon="['far', 'exclamation-circle']"
          class="mx-3 fill-current text-red-700 cursor-pointer"
          @click="isToggle = !isToggle"
        ></fa-icon>
      </div>
    </div>
    <div
      v-if="hasIssues && isToggle"
      class="flex w-full px-4 truncate hover:whitespace-pre-line"
    >
      <span>
        <fa-icon
          :icon="['far' ,'exclamation-circle']"
          class="fill-current text-red-700 mr-2"
          fixed-width
        ></fa-icon>
        <span
          class="text-xs"
          v-text="issue_message"
        ></span>
      </span>
    </div>
  </div>
</template>

<script>
export default  {
  name:'adset-list-item',
  props:{
    adset:{
      type:Object,
      required:true,
    },
  },
  data:()=>({
    isBusy: false,
    isToggle: false,
  }),
  computed:{
    isActive(){
      return ['ACTIVE','IN_PROCESS'].includes(this.adset.effective_status);
    },
    icon(){
      if(this.adset.effective_status === 'ACTIVE'){
        return 'pause';
      }
      if (['PAUSED','ADSET_PAUSED','CAMPAIGN_PAUSED'].includes(this.adset.effective_status)){
        return 'play';
      }

      return 'pencil-alt';
    },
    status(){
      switch (this.adset.effective_status) {
      case 'ACTIVE':
        return 'Активен';
      case 'PAUSED':
      case 'CAMPAIGN_PAUSED':
        return  'Остановлен';
      case 'DELETED':
        return 'Удален';
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
    hasIssues() {
      return this.adset.issues_info !== null;
    },
    issue_message() {
      if (this.hasIssues) {
        if (Array.isArray(this.adset.issues_info)) {
          return this.adset.issues_info[0].error_summary;
        }
        return this.adset.issue_info.error_summary;
      }
      return null;
    },
  },
  methods:{
    start(){
      this.isBusy = true;
      axios.post(`/api/adsets/${this.adset.id}/status`)
        .then(response =>{
          this.$emit('adset-changed',{adset : response.data});
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось обновить статус.', message: err.response.data.message});
        })
        .finally(()=>this.isBusy = false);
    },
    stop(){
      this.isBusy = true;
      axios.delete(`/api/adsets/${this.adset.id}/status`)
        .then(response => {
          this.$emit('adset-changed', {adset : response.data});
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось обновить статус.', message: err.response.data.message});
        })
        .finally(()=>this.isBusy = false);
    },
    toggle(){
      this.isActive ? this.stop() : this.start();
    },

    startMidnight() {
      this.isBusy = true;
      axios.post(`/api/adsets/${this.adset.id}/start-midnight`)
        .then(response => {
          this.$emit('adset-changed', {adset : response.data});
        })
        .catch(error => {
          this.$toast.error({title: 'Не удалось поставить адсет на запуск.', message: error.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    stopMidnight() {
      this.isBusy = true;
      axios.delete(`/api/adsets/${this.adset.id}/start-midnight`)
        .then(response => {
          this.$emit('adset-changed', {adset : response.data});
        })
        .catch(error => {
          this.$toast.error({title: 'Не удалось убрать адсет из запуска.', message: error.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    toggleStartingMidnight() {
      this.adset.start_midnight ? this.stopMidnight() : this.startMidnight();
    },
  },

};
</script>
