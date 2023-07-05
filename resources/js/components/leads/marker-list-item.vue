<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div
      class="w-1/12"
      v-text="marker.id"
    ></div>
    <div
      class="w-6/12 px-4 text-sm text-justify text-gray-700"
      v-text="marker.name"
    >
    </div>
    <div class="flex justify-end w-5/12 px-4">
      <fa-icon
        :icon="['far', 'times-circle']"
        class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
        fixed-width
        @click="remove"
      ></fa-icon>
    </div>
  </div>
</template>

<script>

export default {
  name: 'marker-list-item',
  props: {
    marker: {
      type: Object,
      required: true,
    },
  },
  methods: {
    remove() {
      if (confirm('Точно удалить?')) {
        axios.delete(`/api/leads/${this.marker.lead_id}/markers/${this.marker.id}`)
          .then(() => this.$emit('deleted', {marker: this.marker}))
          .catch(() => this.$toast.error({title:'Unable to delete the marker.'}));
      }
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
