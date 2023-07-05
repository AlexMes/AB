<template>
  <div class="bg-white w-full border-b p-4 tracking-normal flex flex-col items-center">
    <div class="flex w-full items-center">
      <div class="w-1/5">
        <span v-text="page.id"></span>
      </div>
      <div
        v-if="showProfile"
        class="w-1/5"
      >
        <span
          v-if="page.profile"
          v-text="page.profile.name"
        ></span>
        <span v-else></span>
      </div>
      <div class="w-2/5">
        <span v-text="page.name"></span>
      </div>
      <div class="w-1/5">
        <fa-icon
          :icon="['far','eye']"
          class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
          fixed-width
          @click="$modal.show('page-comments-list',{page:page})"
        ></fa-icon>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'profile-pages-list-item',
  props: {
    page: {
      type: Object,
      required: true,
    },
    showProfile: {
      type: Boolean,
      default: true,
    },
  },
};
</script>
