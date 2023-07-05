<template>
  <div class="relative h-2 rounded overflow-hidden">
    <div class="absolute w-full h-full bg-gray-400"></div>
    <div
      class="absolute h-full transition"
      :style="{width: `${width}%`, backgroundColor: color}"
    ></div>
  </div>
</template>

<script>
export default {
  name: 'order-progress-widget',
  props: {
    color: {
      type: String,
      default: '#2c7a7b',
    },
    current: {
      type: Number,
      default: 0,
    },
    goal: {
      type: Number,
      default: 0,
    },
    progress: {
      type: Number,
      default: null,
    },
  },
  computed: {
    shouldCalculate() {
      return this.progress === null;
    },
    width() {
      return this.shouldCalculate ? Math.round( (this.current / this.goal) * 100 ) : this.progress;
    },
  },
};
</script>

<style scoped>
.transition {
    transition: all 0.4s ease;
}
</style>
