<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Уведомления
      </h1>
      <a
        class="button btn-primary text-white"
        @click.prevent="markAllRead"
      > Прочитать все</a>
    </div>

    <div class="flex w-full justify-between mb-8">
      <div class="w-1/4 flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Тип уведомлений</label>
        <mutiselect
          v-model="filters.notification_type"
          :show-labels="false"
          :options="notification_types"
          :multiple="false"
          track-by="id"
          label="name"
          placeholder="Выберите тип"
        ></mutiselect>
      </div>

      <div class="w-1/4 flex flex-col px-2">
        <button
          type="button"
          class="button btn-primary mt-7"
          :disabled="isBusy"
          @click.prevent="load(1)"
        >
          <span v-if="isBusy">
            <fa-icon
              :icon="['far','spinner']"
              class="mr-2 fill-current"
              spin
              fixed-width
            ></fa-icon> Загрузка
          </span>
          <span v-else>Загрузить</span>
        </button>
      </div>
    </div>

    <div
      v-if="hasNotifications"
    >
      <div class="shadow">
        <notifications-list-item
          v-for="notification in notifications"
          :key="notification.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :notification="notification"
          @changed="changed"
        ></notifications-list-item>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Нет уведомлений</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'notifications-index',
  data: () => ({
    notifications: [],
    response: null,
    isBusy: false,
    notification_types: [
      {id: null, name: 'Все'},
      {id: 'App\\Facebook\\Notifications\\PaymentFails\\Created', name: 'Сбои оплат'},
      {id: 'App\\Facebook\\Notifications\\Accounts\\AdvertisingDisabled', name: 'Рекламная функция РК отключена'},
      {id: 'App\\Facebook\\Notifications\\Accounts\\Updated', name: 'Статус кабинета изменен'},
      {id: 'App\\Facebook\\Notifications\\Ads\\Updated', name: 'Статус объявления изменен'},
      {id: 'App\\Facebook\\Notifications\\Adsets\\Updated', name: 'Статус адсета изменен'},
      {id: 'App\\Facebook\\Notifications\\Campaigns\\Updated', name: 'Статус кампании изменен'},
      {id: 'App\\Facebook\\Notifications\\Profiles\\Banned', name: 'Профиль забанен'},
      {id: 'App\\Facebook\\Notifications\\Profiles\\Restored', name: 'Профиль разбанен'},
    ],
    filters: {
      notification_type: {id: null, name: 'Все'},
    },
  }),
  computed:{
    hasNotifications() {
      return this.notifications.length > 0;
    },
    cleanFilters() {
      return {
        notification_type: this.filters.notification_type === null ? null : this.filters.notification_type.id,
      };
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.$root.user.id}/notifications`, {params: {page:page, ...this.cleanFilters}})
        .then(({data}) => {this.notifications = data.data; this.response = data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить уведомления.',
            message: e.response.data.message,
          }),
        );
    },
    changed(notification) {
      const index = this.notifications.findIndex(n => n.id === notification.id);
      if (index !== -1) {
        this.notifications.splice(index, 1);
      }
    },
    markAllRead() {
      axios.post(`/api/users/${this.$root.user.id}/notifications/mark-all-read`)
        .then(response => {
          this.notifications = [];
          this.response = null;
          this.$eventHub.$emit('db-notification-read-all');
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось отметить все уведомления как прочитанные.', message: e.response.data.message});
        });
    },
  },
};
</script>

