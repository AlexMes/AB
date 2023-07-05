<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div class="px-4">
      <router-link
        :to="{name:'profile-logs.show', params:{id:profileLog.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="`#${profileLog.id}`"
      ></router-link>
    </div>
    <div>
      <img
        :src="`https://graph.facebook.com/${profileLog.profile.fbId}/picture?type=normal`"
        alt="FB profile image"
        class="rounded-full h-8 w-8"
      />
    </div>
    <div
      class="w-1/6 px-4 text-sm text-gray-700"
      v-text="profileLog.profile.name"
    >
    </div>
    <div
      class="w-1/12 px-4 text-sm text-justify text-gray-600"
      v-text="profileLog.duration"
    ></div>
    <div
      class="w-1/3 px-4"
      v-text="profileLog.creative"
    ></div>
    <div class="w-1/3 px-4">
      <a
        target="_blank"
        :href="profileLog.link"
        class="w-full block text-gray-700 hover:text-teal-700"
        rel="noreferrer noopener"
        v-text="profileLog.link"
      ></a>
    </div>
  </div>
  <!--<tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td class="pr-2 py-3 pl-5">
      <router-link
        :to="{name:'profile-logs.show', params:{id:profileLog.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="`# ${profileLog.id}`"
      >
      </router-link>
    </td>
    <td
      v-if="profileLog.profile"
      v-text="profileLog.profile.name"
    ></td>
    <td v-else></td>
    <td v-text="profileLog.duration"></td>
    <td v-text="profileLog.miniature"></td>
    <td v-text="profileLog.creative"></td>
    <td>
      <a
        target="_blank"
        :href="profileLog.link"
        class="w-full block text-gray-700 hover:text-teal-700"
        rel="noreferrer noopener"
        v-text="profileLog.link"
      ></a>
    </td>
  </tr>-->
</template>

<script>
export default {
  name:'profile-log-list-item',
  props:{
    profileLog:{
      required:true,
      type:Object,
    },
  },
};
</script>
<style>
    td{
        @apply py-4;
        @apply px-2;
    }
</style>
