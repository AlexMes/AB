<template>
  <div class="rounded flex flex-col flex-1 p-4 shadow-md bg-white justify-between">
    <div class="align-middle">
      <p
        class="text-right text-gray-700 text-3xl font-bold pb-2 whitespace-no-wrap"
        v-text="measure"
      ></p>
    </div>
    <span
      class="text-gray-600 text-left mt-2 font-medium text-sm"
      v-text="label"
    ></span>
  </div>
</template>

<script>
export default {
  name: 'stat-card',
  props: {
    label: { type: String, required: true, default: 'Label' },
    measure: { type: [String, Number], required: true, default: '0' },
  },
};
</script>
