<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="tag.id"
    ></div>
    <div>
      <router-link
        :to="{name:'tags.show', params: {id: tag.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="tag.name"
      ></router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'tag-list-item',
  props:{
    tag:{
      required:true,
      type:Object,
    },
  },
};
</script>
