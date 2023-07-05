<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div
      class="w-1/12"
      v-text="blackLead.id"
    ></div>
    <div
      class="w-10/12 px-4 text-sm text-justify text-gray-700"
      v-text="blackLead.phone"
    >
    </div>
    <div class="flex justify-end w-1/12 px-4">
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
  name: 'black-lead-list-item',
  props: {
    blackLead: {
      type: Object,
      required: true,
    },
  },
  methods: {
    remove() {
      axios.delete(`/api/black-leads/${this.blackLead.id}`)
        .then(response => {
          this.$emit('deleted', {black_lead: this.blackLead});
          this.$toast.success({title: 'Ok', message: 'Lead was deleted from blacklist successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to delete lead from blacklist.', message: err.response.data.message}));
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
