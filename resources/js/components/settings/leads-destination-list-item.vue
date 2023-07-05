<template>
  <tr>
    <td v-text="destination.id">
    </td>
    <td class="font-semibold">
      <router-link
        :to="{name: 'leads-destinations.show', params: {id: destination.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
        v-text="destination.name"
      ></router-link>
    </td>
    <td v-text="destination.driver">
    </td>
    <td v-text="destination.autologin">
    </td>
    <td>
      <span
        v-if="destination.branch"
        v-text="destination.branch.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td>
      <span
        v-if="destination.office"
        v-text="destination.office.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[destination.is_active ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <router-link
        :to="{name: 'leads-destinations.update', params: {id: destination.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <router-link
        :to="{name: 'leads-destinations.create', params: {destination: destination}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'copy']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        :icon="['far', 'file-check']"
        class="font-semibold text-gray-500 cursor-pointer fill-current hover:text-teal-600"
        @click.prevent="$modal.show('test-lead-destination-modal', {destination: destination})"
      ></fa-icon>
      <fa-icon
        v-if="['admin', 'support'].includes($root.user.role) && destination.is_active"
        :icon="['far', 'ballot-check']"
        class="font-semibold text-gray-500 cursor-pointer fill-current hover:text-teal-600"
        @click.prevent="collectStatuses"
      ></fa-icon>
      <fa-icon
        v-if="['admin', 'support'].includes($root.user.role) && destination.is_active"
        :icon="['far', 'hands-usd']"
        class="font-semibold text-gray-500 cursor-pointer fill-current hover:text-teal-600"
        @click.prevent="$modal.show('collect-lead-destination-results-modal', {destination: destination})"
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
  name: 'leads-destination-list-item',
  props: {
    destination: {
      required: true,
      type: Object,
    },
  },
  methods: {
    remove() {
      if (confirm('Delete the destination?')) {
        axios.delete(`/api/leads-destinations/${this.destination.id}`)
          .then(response => this.$emit('deleted', {destination: this.destination}))
          .catch(err => this.$toast.error({title: 'Unable to delete the destination', message: err.response.data.message}));
      }
    },
    collectStatuses() {
      axios
        .post(`/api/leads-destinations/${this.destination.id}/collect-statuses`)
        .then(r => (this.$toast.success({title: 'Ok', message: 'Сбор статусов запущен.'})))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось запустить сбор статусов.',
            message: e.response.data.message,
          });
        });
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
