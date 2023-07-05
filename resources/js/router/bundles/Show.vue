<template>
  <div class="container mx-auto flex mt-6">
    <div class="flex flex-col w-1/6">
      <router-link
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'bundles.general', params:{ id:id }}"
      >
        Основное
      </router-link>
      <router-link
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'bundles.comments', params:{ id:id }}"
      >
        Комментарии
      </router-link>
      <router-link
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'bundles.files', params:{ id:id }}"
      >
        Файлы
      </router-link>
    </div>
    <div class="w-5/6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>

export default {
  name: 'bundle-show',
  props: {
    id: {
      type: [String,Number],
      required: true,
      default: null,
    },
  },
};
</script>
