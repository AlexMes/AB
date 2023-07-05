<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="group.id"
    ></div>
    <div>
      <router-link
        :to="{name:'groups.update', params: {id: group.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="group.name"
      ></router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'group-list-item',
  props:{
    group:{
      required:true,
      type:Object,
    },
  },
};
</script>
