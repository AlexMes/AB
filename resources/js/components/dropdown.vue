<template>
  <div>
    <div
      v-if="open"
      class="fixed inset-0"
      @click="open = false"
    ></div>
    <div @click="open = !open">
      <trigger>
        <slot name="trigger"></slot>
      </trigger>
    </div>

    <transition
      enter-class="transform scale-90 opacity-0"
      enter-active-class="transition duration-200 ease-out"
      enter-to-class="transform scale-100 opacity-100"
      leave-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-to-class="transform scale-90 opacity-0"
    >
      <div
        v-show="open"
        class="absolute z-50 right-0 w-64 mt-2 origin-top-right border border-gray-200 rounded-md shadow-lg"
        style="display: none;"
        @click="open = false"
      >
        <div
          class="py-3 px-1 bg-white rounded-md ring-1 ring-black ring-opacity-5"
        >
          <slot></slot>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'dropdown',
  data: () => ({
    open: false,
  }),
};
</script>
