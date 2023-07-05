<template>
  <div
    class="w-full bg-white items-center border-b hover:bg-gray-200 text-gray-700 flex p-3"
  >
    <div class="flex items-center justify-center">
      <fa-icon
        class="mx-2 text-2xl fill-current"
        :class="{
          'text-orange-600' : message.status === 'sent',
          'text-green-600': message.status === 'delivered',
          'text-red-600': message.status === 'failed',
        }"
        :icon="icon"
      ></fa-icon>
    </div>
    <div class="flex px-3 mr-2">
      <span
        class="font-semibold whitespace-no-wrap"
        v-text="`# ${message.id}`"
      >
      </span>
    </div>
    <div class="flex flex-row justify-between py-2 w-3/5 mr-3">
      <span
        class="whitespace-pre-wrap leading-snug"
        v-text="message.message"
      >
      </span>
    </div>
    <div
      class="flex flex-1 justify-between whitespace-no-wrap"
    >
      <div class="flex whitespace-no-wrap flex-col text-left">
        <router-link
          :to="{name:'leads.show', params:{id:message.lead_id}}"
          class="text-gray-700 hover:text-teal-700 font-semibold"
          v-text="message.lead.firstname"
        ></router-link>
        <span
          class="mt-2"
          v-text="message.phone"
        ></span>
      </div>
      <div class="flex flex-col mr-1">
        <span
          class="text-right"
          v-text="date"
        ></span>
        <span
          class="text-right text-sm"
          v-text="time"
        ></span>
      </div>
      <span v-text="`${message.cost} $`"></span>
    </div>
  </div>
</template>

<script>
import {faCheckCircle,faTimesCircle, faClock} from '@fortawesome/pro-regular-svg-icons';
import moment from 'moment';

export default {
  name:'sms-messages-list-item',
  props:{
    message:{
      required:true,
      type:Object,
    },
  },
  computed:{
    icon(){
      if(this.message.status === 'delivered'){
        return faCheckCircle;
      }
      if(this.message.status === 'failed'){
        return faTimesCircle;
      }
      return faClock;
    },
    date() {
      return moment(this.message.created_at).format('YYYY-MM-DD');
    },
    time() {
      return moment(this.message.created_at).format('HH:mm:ss');
    },
  },
};
</script>

<style>
  td{
      @apply py-4;
      @apply px-2;
      @apply align-middle;
  }

</style>
