<template>
  <span
    class="toggle-wrapper"
    role="checkbox"
    :aria-checked="value.toString()"
    tabindex="0"
    @click="toggle"
    @keydown.space.prevent="toggle"
  >
    <span
      class="toggle-background"
      :style="backgroundStyles"
    ></span>
    <span
      class="toggle-indicator"
      :style="indicatorStyles"
    ></span>
  </span>
</template>

<script>
export default {
  name: 'toggle',
  // eslint-disable-next-line vue/require-prop-types
  props: ['value'],
  computed:{
    backgroundStyles() {
      return {
        backgroundColor: this.value ? '#319795' : '#dae1e7',
      };
    },
    indicatorStyles() {
      return { transform: this.value ? 'translateX(2rem)' : 'translateX(0)' };
    },
  },
  methods:{
    toggle() {
      this.$emit('input', !this.value);
    },
  },
};
</script>

<style scoped>
  .toggle-wrapper {
    @apply inline-block;
    @apply relative;
    @apply cursor-pointer;
    height: 2rem;
    width: 4rem;
    @apply rounded-full;
  }

  .toggle-wrapper:focus {
    outline: 0;
    box-shadow: 0 0 0 4px rgba(0, 135, 152, 0.3);
  }

  .toggle-background {
    @apply inline-block;
    @apply rounded-full;
    @apply w-full;
    @apply h-full;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    transition: background-color .2s ease;
  }

  .toggle-indicator {
    @apply absolute;
    top: .25rem;
    left: .25rem;
    height: 1.5rem;
    width: 1.5rem;
    background-color: #fff;
    border-radius: 9999px;
    box-shadow:  0 2px 4px rgba(0,0,0,0.1);
    transition: transform .2s ease;
  }
</style>