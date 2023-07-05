<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="supplier.id"
    ></div>
    <div class="w-1/4">
      <router-link
        :to="`suppliers/${supplier.id}`"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="supplier.name"
      ></router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'suppliers-list-item',
  props:{
    supplier:{
      required:true,
      type:Object,
    },
  },
};
</script>
