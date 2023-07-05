<template>
  <div class="flex relative">
    <div class="flex rounded overflow-hidden">
      <a
        class="button btn-primary text-white"
        :href="`/vk/connect`"
        target="_blank"
        rel="noopener"
      > Прикрепить </a>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'attach-vk-profile',
  data: () => ({
    errors: new ErrorBag(),
  }),
  computed: {
    //
  },
  created() {
    //
  },
  methods: {
    //
  },
};
</script>

<style scoped>
    .button {
        @apply font-medium;
        @apply text-center;
        @apply px-3 py-2;
        @apply shadow;
        @apply cursor-pointer;
        border-radius: 0;
    }
</style>
