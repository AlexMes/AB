<template>
  <div class="bg-white w-full border-b p-4 tracking-normal flex flex-col items-center">
    <div class="flex w-full items-center justify-between">
      <div class="flex items-center w-1/12">
      </div>
      <div class="flex w-3/12">
        <div class="flex">
          <span v-text="notification.data.title"></span>
        </div>
      </div>
      <div class="flex w-5/12">
        <span
          v-if="notification.data.body"
          v-text="notification.data.body"
        >
        </span>
      </div>
      <div
        class="flex w-1/12"
        v-text="date"
      ></div>
      <div
        class="flex w-1/12"
        v-text="time"
      ></div>
      <div class="w-1/12 text-right mx-2">
        <fa-icon
          :icon="['far','check']"
          class="fill-current text-gray-500 hover:text-teal-700 cursor-pointer"
          :spin="isBusy"
          @click="markRead"
        >
        </fa-icon>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
export default {
  name: 'notifications-list-item',
  props:{
    notification:{
      type: Object,
      required: true,
    },
  },
  data: () => ({
    isBusy: false,
  }),
  computed: {
    date() {
      return moment(this.notification.created_at).format('YYYY-MM-DD');
    },
    time() {
      return moment(this.notification.created_at).format('hh:mm');
    },
  },
  methods:{
    markRead(){
      this.isBusy = true;
      axios.post(`/api/users/${this.notification.notifiable_id}/notifications/${this.notification.id}/mark-read`)
        .then(response => {
          this.$emit('changed', response.data);
          this.$toast.success({title:'ОК', message:''});
          this.$eventHub.$emit('db-notification-read', this.notification);
        })
        .catch(error => {
          this.$toast.error({title:'Не удалось отметить уведомление прочитаным.', message:error.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
