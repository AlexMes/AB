<template>
  <button
    v-show="isVisible"
    @click="scrollToTop"
  >
    <fa-icon
      :icon="['far','angle-up']"
      class="text-teal-700"
      size="lg"
      fixed-width
    ></fa-icon>
  </button>
</template>

<script>
export default {
  name: 'scroll-to-top',
  data:() =>({
    isVisible: false,
  }),
  created() {
    window.addEventListener('scroll',this.roll);
  },
  destroyed() {
    window.removeEventListener('scroll',this.roll);
  },
  methods:{
    roll(){
      this.isVisible = window.scrollY > 250;
    },
    scrollToTop() {
      window.scrollTo(0,0);
    },
  },
};
</script>

<style scoped>
 button{
     @apply fixed;
     @apply right-0;
     @apply bottom-0;
     @apply mr-6;
     @apply mb-6;
     @apply p-3;
     @apply rounded;
     @apply text-white;
     @apply bg-white;
     @apply shadow;
     @apply z-50;
 }
</style>
