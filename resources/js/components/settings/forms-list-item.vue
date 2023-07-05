<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="form.id"
    ></div>
    <div class="w-1/4">
      <router-link
        :to="`forms/${form.id}`"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="form.name"
      ></router-link>
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700"
      v-text="form.url"
    >
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700 uppercase"
      v-text="form.form_id"
    >
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700 uppercase"
      v-text="form.provider"
    >
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700"
    >
      {{ form.landing ? form.landing.url : '-' }}
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700 uppercase"
      v-text="form.status ? 'Active' : 'Disabled'"
    >
    </div>
  </div>
</template>

<script>
export default {
  name: 'forms-list-item',
  props:{
    form:{
      required:true,
      type:Object,
    },
  },
};
</script>
