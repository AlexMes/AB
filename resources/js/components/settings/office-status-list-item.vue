<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div
      class="w-8/12 px-4 text-sm text-justify text-gray-700"
      v-text="status.status"
    >
    </div>
    <div class="w-3/12 px-4 text-sm text-justify text-gray-700">
      <toggle
        v-model="status.selectable"
        @input="update"
      ></toggle>
    </div>
    <div class="flex justify-end w-1/12 px-4">
      <fa-icon
        v-if="$root.user.role === 'admin'"
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
  name: 'office-status-list-item',
  props: {
    status: {
      type: Object,
      required: true,
    },
  },
  methods: {
    update() {
      axios.put(`/api/office-statuses/${this.status.id}`, this.status)
        .then(response => {
          this.$emit('updated', {status: response.data});
          this.$toast.success({title: 'OK', message: 'Статус обновлён.'});
        }).catch(err => this.$toast.error({title: 'Не удалось обновить статус.', message: err.response.data.message}));
    },
    remove() {
      axios.delete(`/api/office-statuses/${this.status.id}`)
        .then(response => {
          this.$toast.success({title: 'Удалено', message: 'Статус успешно удалён'});
          this.$emit('deleted', {status: this.status});
        })
        .catch(err => this.$toast.error({title: 'Не удалось удалить статус', message: err.response.data.message}));
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
