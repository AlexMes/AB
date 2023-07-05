<template>
  <div class="container mx-auto flex mt-6">
    <div class="flex flex-col w-1/6">
      <router-link
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'vk-profile.general', params:{ id:id }}"
      >
        Основное
      </router-link>
<!--      <router-link
        v-if="$root.user.role !== 'farmer'"
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.pages', params:{ id:id }}"
      >
        Страницы
      </router-link>-->
    </div>
    <div class="w-5/6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'vk-profiles-show',
  props:{
    id:{
      type: [Number, String],
      required: true,
      default: null,
    },
  },
};
</script>

