<template>
  <div class="flex items-center">
    <span v-text="title"></span>
    <div class="flex flex-col ml-1">
      <fa-icon
        class="cursor-pointer hover:text-teal-800 -mb-1"
        :class="{'text-teal-700':column === by && asc}"
        :icon="['far', 'sort-up']"
        @click="$emit('changed', {column, asc: true})"
      ></fa-icon>
      <fa-icon
        class="cursor-pointer hover:text-teal-800 -mt-1"
        :class="{'text-teal-700':column === by && !asc}"
        :icon="['far', 'sort-down']"
        @click="$emit('changed', {column, asc: false})"
      ></fa-icon>
    </div>
  </div>
</template>

<script>
export default {
  name: 'sort',
  props: {
    title: {
      type: String,
      default: '',
    },
    column: {
      type: String,
      default: 'id',
    },
    by: {
      type: String,
      default: 'id',
    },
    asc: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped>

</style>
