<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div class="mr-3">
      <img
        :src="`https://eu.ui-avatars.com/api/?name=${user.name}&background=2C7A7B&color=F7FAFC`"
        alt="AdsBoard avatar"
        class="rounded-full h-8 w-8"
      />
    </div>
    <div class="w-1/5">
      <router-link
        :to="{name:'users.profiles', params:{id: user.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="user.name"
      ></router-link>
    </div>
    <div
      class="px-5 font-medium text-sm text-gray-700 uppercase"
      v-text="user.role"
    >
    </div>
    <div
      class="px-4 text-sm text-justify text-gray-600"
      v-text="user.email"
    ></div>
    <div
      v-if="showDelete"
      class="ml-auto px-4"
    >
      <fa-icon
        :icon="['far', 'times-circle']"
        class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
        fixed-width
        @click="$emit('deleted')"
      ></fa-icon>
    </div>
  </div>
</template>

<script>
export default {
  name:'user-list-item',
  props:{
    user:{
      required:true,
      type:Object,
    },
  },
  computed: {
    showDelete() {
      return this.$listeners.hasOwnProperty('deleted');
    },
  },
};
</script>
