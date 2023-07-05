<template>
  <div class="w-full h-auto bg-white rounded shadow mb-8">
    <h2 class="text-gray-700 border-b p-3">
      Телеграм уведомления
    </h2>
    <div class="flex flex-wrap p-2">
      <div
        v-for="notification in allNotifications"
        :key="notification.id"
        class="flex w-1/2 border-b-2 border-dashed"
      >
        <div class="w-full flex items-center justify-between my-3">
          <span class="ml-3">{{ notification.name }}</span>
          <toggle
            :value="userHasNotification(notification.id)"
            @input="toggle(notification.id)"
          ></toggle>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'user-telegram-notifications',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
    notifications: {
      type: [Array],
      default: () => [],
    },
  },

  data() {
    return {
      allNotifications: [],
    };
  },

  created() {
    this.boot();
  },

  methods: {
    boot() {
      axios.get('/api/notification-types/')
        .then(r => {
          this.allNotifications = r.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список уведомлений.', message: e.response.data.message}));
    },

    deny(id) {
      axios.post(`/api/users/${this.id}/denied-telegram-notifications`, {id})
        .then(({data}) => {
          this.$toast.success({title: 'Уведомление выключено'});
          this.$emit('add', data);
        })
        .catch(e => this.$toast.error({title: 'Не удалось убрать уведомление.', message: e.response.data.message}));
    },

    allow(id) {
      axios.delete(`/api/users/${this.id}/denied-telegram-notifications/${id}`)
        .then(r => {
          this.$toast.success({title: 'Уведомление влючено'});
          this.$emit('remove', id);
        })
        .catch(e => this.$toast.error({title: 'Не удалось добавить уведомление.', message: e.response.data.message}));
    },

    toggle(id) {
      if (this.userHasNotification(id)) {
        this.deny(id);
      } else {
        this.allow(id);
      }
    },
    userHasNotification(id) {
      return this.notifications.find(notification => notification.id === id) === undefined;
    },
  },
};
</script>

<style scoped>

</style>
