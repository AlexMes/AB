<template>
  <tr>
    <td v-text="batch.id">
    </td>
    <td
      class="font-semibold"
      v-text="batch.name"
    ></td>
    <td>
      <span
        class="text-gray-800 rounded-full py-1 px-2"
        :class="status.color"
        v-text="status.text"
      ></span>
    </td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[batch.create_offer ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[batch.simulate_autologin ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <div
        class="w-4 h-4 border-0 rounded-full"
        :class="[batch.ignore_paused_routes ? 'bg-green-500' : 'bg-red-500']"
      ></div>
    </td>
    <td>
      <router-link
        :to="{name: 'resell-batches.show', params: {id: batch.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
      >
        <fa-icon
          :icon="icon"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <router-link
        :to="{name: 'resell-batches.create', params: {batch: batch}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'copy']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
    </td>
  </tr>
</template>

<script>
import {faEdit} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'resell-batch-list-item',
  props:{
    batch:{
      required:true,
      type:Object,
    },
  },
  computed:{
    status() {
      let status = {
        color: 'bg-gray-200',
        text: 'В ожидании',
      };

      if (this.batch.status === 'in_process') {
        status.color = 'bg-yellow-200';
        status.text = 'В процесе';
      }
      if (this.batch.status === 'paused') {
        status.color = 'bg-blue-200';
        status.text = 'На паузе';
      }
      if (this.batch.status === 'canceled') {
        status.color = 'bg-red-200';
        status.text = 'Отменён';
      }
      if (this.batch.status === 'finished') {
        status.color = 'bg-green-200';
        status.text = 'Завершён';
      }

      return status;
    },
    icon: () => faEdit,
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
