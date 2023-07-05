<template>
  <div class="container mx-auto flex mt-6">
    <div class="flex flex-col w-1/6">
      <router-link
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.general', params:{ id:id }}"
      >
        Основное
      </router-link>
      <router-link
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.accounts', params:{ id:id }}"
      >
        Рекламные аккаунты
      </router-link>
      <router-link
        v-if="$root.user.role !== 'farmer'"
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.campaigns', params:{ id:id }}"
      >
        Кампании
      </router-link>
      <router-link
        v-if="$root.user.role !== 'farmer'"
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.adsets', params:{ id:id }}"
      >
        Адсеты
      </router-link>
      <router-link
        v-if="$root.user.role !== 'farmer'"
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.ads', params:{ id:id }}"
      >
        Объявления
      </router-link>
      <router-link
        v-if="$root.user.role !== 'farmer'"
        class="my-2 text-base tracking-wide "
        active-class="text-teal-700 font-semibold"
        :to="{name: 'profile.pages', params:{ id:id }}"
      >
        Страницы
      </router-link>
    </div>
    <div class="w-5/6">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'profiles-show',
  props:{
    id:{
      type: [Number, String],
      required: true,
      default: null,
    },
  },
};
</script>

