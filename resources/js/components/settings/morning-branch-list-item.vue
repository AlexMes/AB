<template>
  <tr>
    <td>
      <span v-text="branch.name"></span>
    </td>
    <td v-text="branch.pivot.time"></td>
    <td v-text="branch.pivot.duration"></td>
    <td>
      <fa-icon
        :icon="['far', 'times-circle']"
        class="text-gray-500 fill-current cursor-pointer hover:text-red-600"
        fixed-width
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>

export default {
  name: 'morning-branch-list-item',
  props: {
    branch: {
      type: Object,
      required: true,
    },
  },
  methods: {
    remove() {
      axios.delete(`/api/offices/${this.branch.pivot.office_id}/morning-branches/${this.branch.id}`)
        .then(response => {
          this.$toast.success({title: 'Удалено', message: 'Филиал успешно удален.'});
          this.$emit('deleted', {branch: this.branch});
        })
        .catch(err => this.$toast.error({title: 'Не удалось удалить филиал', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>
    td {
        @apply px-6;
        @apply py-3;
        @apply whitespace-no-wrap;
        @apply text-sm;
        @apply leading-5;
        @apply text-gray-500;
    }
</style>
