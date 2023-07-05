<template>
  <tr>
    <td>
      <router-link
        :to="{name: 'lead-payment-conditions.show', params: {id: condition.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
        v-text="condition.id"
      ></router-link>
    </td>
    <td v-text="condition.office.name">
    </td>
    <td v-text="condition.offer.name">
    </td>
    <td v-text="condition.model">
    </td>
    <td v-text="condition.cost">
    </td>
    <td>
      <router-link
        :to="{name: 'lead-payment-conditions.update', params: {id: condition.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        :icon="['far', 'times-circle']"
        class="text-gray-500 cursor-pointer fill-current hover:text-red-600"
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>

export default {
  name: 'lead-payment-condition-list-item',
  props: {
    condition: {
      required: true,
      type: Object,
    },
  },
  methods: {
    remove() {
      if (confirm('Delete the condition?')) {
        axios.delete(`/api/lead-payment-conditions/${this.condition.id}`)
          .then(response => this.$emit('deleted', {condition: this.condition}))
          .catch(err => this.$toast.error({title: 'Unable to delete the condition', message: err.response.data.message}));
      }
    },
  },
};
</script>

<style scoped>
td{
    @apply px-6;
    @apply py-4;
    @apply whitespace-no-wrap;
    @apply text-sm;
    @apply leading-5;
    @apply text-gray-500;
}
</style>
