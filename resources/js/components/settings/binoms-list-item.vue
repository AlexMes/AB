<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="binom.id"
    ></div>
    <div class="w-1/4">
      <router-link
        :to="`binoms/${binom.id}`"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="binom.name"
      ></router-link>
    </div>
    <div
      class="w-2/4 px-5 font-medium text-sm text-gray-700"
      v-text="binom.url"
    >
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700 uppercase"
      v-text="binom.enabled ? 'Active' : 'Disabled'"
    >
    </div>
  </div>
</template>

<script>
export default {
  name: 'binoms-list-item',
  props:{
    binom:{
      required:true,
      type:Object,
    },
  },
};
</script>
