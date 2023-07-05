<template>
  <tr>
    <td v-text="group.id">
    </td>
    <td class="font-semibold">
      <router-link
        :to="{name: 'office-groups.offices', params: {id: group.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
        v-text="group.name"
      ></router-link>
    </td>
    <td>
      <span
        v-if="group.branch"
        v-text="group.branch.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td>
      <router-link
        :to="{name: 'office-groups.update', params: {id: group.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        v-if="['admin'].includes($root.user.role)"
        :icon="['far', 'times-circle']"
        class="text-gray-500 cursor-pointer fill-current hover:text-red-600"
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>

export default {
  name: 'office-group-list-item',
  props: {
    group: {
      required: true,
      type: Object,
    },
  },
  methods: {
    remove() {
      if (confirm('Delete the group?')) {
        axios.delete(`/api/office-groups/${this.group.id}`)
          .then(response => this.$emit('deleted', {group: this.group}))
          .catch(err => this.$toast.error({title: 'Unable to delete the group', message: err.response.data.message}));
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
