<template>
  <tr>
    <td v-text="branch.id">
    </td>
    <td
      class="font-semibold"
      v-text="branch.name"
    ></td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[branch.stats_access ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <router-link
        :to="{name: 'branches.teams', params: {id: branch.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        v-if="branch.telegram_id"
        :icon="['far', 'comment-dots']"
        class="text-gray-500 cursor-pointer fill-current hover:text-teal-600"
        @click="$modal.show('branch-telegram-modal', {branch: branch})"
      ></fa-icon>
      <!-- <fa-icon
        :icon="['far', 'times-circle']"
        class="text-gray-500 cursor-pointer fill-current hover:text-red-600"
        @click="remove"
      ></fa-icon> -->
    </td>
  </tr>
</template>

<script>

export default {
  name: 'branch-list-item',
  props: {
    branch: {
      required: true,
      type: Object,
    },
  },
  methods: {
    remove() {
      if (confirm('Delete the branch?')) {
        axios.delete(`/api/branches/${this.branch.id}`)
          .then(response => this.$emit('deleted', {branch: this.branch}))
          .catch(err => this.$toast.error({title: 'Unable to delete the branch', message: err.response.data.message}));
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
