<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div
      class="w-1/12"
      v-text="user.id"
    ></div>
    <div
      class="w-10/12 px-4 text-sm text-justify text-gray-700"
      v-text="user.name"
    >
    </div>
    <div class="flex justify-end w-1/12 px-4">
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
  name: 'shared-user-list-item',
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped>
    td{
        @apply py-4;
        @apply px-2;
        @apply text-gray-800;
        @apply text-base;
    }
</style>
