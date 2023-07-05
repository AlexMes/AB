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
      <div
        class="w-5/12 flex flex-col"
      >
        <span v-text="disapproval.ad.name"></span>
        <span
          class="text-sm text-gray-600"
          v-text="disapproval.ad.id"
        ></span>
      </div>
      <div class="flex justify-between items-center w-4/12">
        <span
          v-text="disapproval.reason"
        ></span>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'ads-disapprovals-list-item',
  props:{
    disapproval:{
      type:Object,
      required:true,
    },
  },
  computed: {
    date() {
      return moment(this.disapproval.created_at).format('YYYY-MM-DD');
    },
    time() {
      return moment(this.disapproval.created_at).format('HH:mm:ss');
    },
  },
};
</script>
