<template>
  <tr>
    <td
      class="pl-5"
      v-text="config.id"
    ></td>
    <td v-text="config.new_status"></td>
    <td v-text="config.statuses.join(', ')"></td>
    <td>
      <span v-if="config.statuses_type === 'in'">В выбранных</span>
      <span v-if="config.statuses_type === 'out'">Кроме выбранных</span>
    </td>
    <td v-text="config.assigned_days_ago"></td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[config.enabled ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <fa-icon
        class="text-gray-500 fill-current cursor-pointer hover:text-teal-600"
        :icon="['far', 'play']"
        fixed-width
        @click.prevent="run"
      ></fa-icon>
      <router-link
        :to="{name: 'status-configs.update', params: {id: config.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        :icon="['far', 'times-circle']"
        class="text-gray-500 fill-current cursor-pointer hover:text-red-600"
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>

export default {
  name: 'status-config-list-item',
  props: {
    config: {
      required: true,
      type: Object,
    },
  },
  methods: {
    remove() {
      if (confirm('Удалить конфигурацию?')) {
        axios.delete(`/api/status-configs/${this.config.id}`)
          .then(response => this.$emit('deleted', {config: this.config}))
          .catch(err => this.$toast.error({title: 'Не удалось удалить конфигурацию.', message: err.response.data.message}));
      }
    },
    run() {
      if (confirm('Запустить конфигурацию?')) {
        axios.post(`/api/status-configs/${this.config.id}/run`)
          .then(response => this.$toast.success({title: 'OK', message: 'Конфигурация запущена'}))
          .catch(err => this.$toast.error({
            title: 'Не удолось запустить конфигурацию',
            message: err.response.data.message,
          }));
      }
    },
  },
};
</script>

<style scoped>
td{
    @apply pr-4;
    @apply py-4;
    @apply whitespace-no-wrap;
    @apply text-sm;
    @apply leading-5;
    @apply text-gray-500;
}
</style>
