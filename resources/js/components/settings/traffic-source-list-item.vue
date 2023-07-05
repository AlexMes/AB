<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="trafficSource.id"
    ></div>
    <div class="w-1/4">
      <router-link
        :to="{name: 'traffic-sources.domains', params:{id:trafficSource.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="trafficSource.name"
      ></router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'traffic-source-list-item',
  props:{
    trafficSource:{
      required:true,
      type:Object,
    },
  },
};
</script>
