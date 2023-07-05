<template>
  <tr>
    <td v-text="proxy.id">
    </td>
    <td class="font-semibold">
      <router-link
        :to="{name: 'proxies.show', params: {id: proxy.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
        v-text="proxy.name"
      ></router-link>
    </td>
    <td v-text="`${proxy.protocol}://${proxy.host}:${proxy.port}`">
    </td>
    <td v-text="proxy.login">
    </td>
    <td v-text="proxy.geo">
    </td>
    <td>
      <span
        v-if="proxy.branch"
        v-text="proxy.branch.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[proxy.is_active ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <router-link
        :to="{name: 'proxies.update', params: {id: proxy.id}}"
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
  name: 'proxy-list-item',
  props: {
    proxy: {
      required: true,
      type: Object,
    },
  },
  methods: {
    remove() {
      if (confirm('Delete the proxy?')) {
        axios.delete(`/api/proxies/${this.proxy.id}`)
          .then(response => this.$emit('deleted', {proxy: this.proxy}))
          .catch(err => this.$toast.error({title: 'Unable to delete the proxy', message: err.response.data.message}));
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
