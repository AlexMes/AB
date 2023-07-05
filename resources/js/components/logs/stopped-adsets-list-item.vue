<template>
  <div>
    <div class="w-full bg-white p-4 border-b flex">
      <div class="flex flex-col justify-center w-2/12 text-left pr-2">
        <div
          class="flex text-gray-700"
        >
          <router-link
            :to="{name: 'profile.general', params: {id: adset.account.profile.id}}"
            v-text="adset.account.profile.name"
          >
          </router-link>
        </div>
      </div>
      <div
        class="flex w-1/12"
      >
        <router-link
          :to="{name: 'users.show', params: {id: adset.account.profile.user.id}}"
          v-text="adset.account.profile.user.name"
        >
        </router-link>
      </div>
      <div
        class="flex w-3/12"
      >
        <span v-text="adset.account.name"></span>
      </div>
      <div class="flex w-4/12">
        <a
          :href="adset.ad.creative_url"
          target="_blank"
          v-text="adset.ad.creative_url"
        ></a>
      </div>
      <div class="flex w-1/12">
        <span
          v-text="adset.spend"
        ></span>
      </div>
      <div class="flex w-1/12">
        <span
          v-text="adset.cpl"
        ></span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'stopped-adsets-list-item',
  props:{
    adset:{
      type:Object,
      required:true,
    },
  },
};
</script>
