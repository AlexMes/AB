<template>
  <label
    class="cursor-pointer checkbox flex items-center"
  >
    <span
      class="border-2 p-1 rounded flex items-center justify-center h-full w-auto mr-2"
      :class="{
        'border-teal-700 bg-teal-700': value,
        'border-gray-700 bg-transparent': !value
      }"
    >
      <fa-icon
        :icon="icon"
        class="icon text-xs font-bold"
        :class="{
          'opacity-100 text-white': value,
          'opacity-75': !value,
        }"
      ></fa-icon>
    </span>

    <slot>Label</slot>

    <input
      class="hidden"
      type="checkbox"
      :checked="value"
      hidden
      @change="$emit('input', $event.target.checked)"
    />
  </label>
</template>

<script>
export default {
  name: 'ads-checkbox',
  props: {
    value: {
      type: null,
      default: false,
    },
    icon: {
      type: Array,
      default: () => (['far', 'check']),
    },
  },
};
</script>

<style scoped>
    .checkbox .icon,
    .checkbox span {
        transition: all 300ms ease;
    }
    .checkbox span {
        @apply text-gray-700;
    }
</style>
