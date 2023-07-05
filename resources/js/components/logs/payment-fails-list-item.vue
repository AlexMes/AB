<template>
  <div>
    <div class="w-full bg-white p-4 border-b flex">
      <div class="flex flex-col justify-center w-3/12 text-left pr-2">
        <span
          class="text-gray-700"
          v-text="date"
        ></span>
        <span
          class="text-sm text-gray-600"
          v-text="time"
        ></span>
      </div>
      <div class="w-4/12 flex flex-col">
        <span v-text="fail.account.name"></span>
        <span
          class="text-sm text-gray-600"
          v-text="fail.account_id"
        ></span>
      </div>
      <div class="flex justify-between items-center w-3/12">
        <span v-text="fail.user.name"></span>
      </div>
      <div class="flex justify-between items-center w-2/12">
        <span
          v-if="fail.card"
          v-text="fail.card"
        ></span>
        <span v-else> - </span>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'payment-fails-list-item',
  props: {
    fail: {
      type: Object,
      required: true,
    },
  },
  computed: {
    date() {
      return moment(this.fail.created_at).format('YYYY-MM-DD');
    },
    time() {
      return moment(this.fail.created_at).format('HH:mm:ss');
    },
  },
};
</script>
