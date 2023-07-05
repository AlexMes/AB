<template>
  <tr
    :class="deliveryHighLight"
  >
    <td
      class="pl-5 pr-5"
    >
      {{ order.id }}
      <fa-icon
        v-if="order.deliverCount > 0"
        class="fill-current mb-0.5 w-3 h-3 text-gray-700 text-lg"
        :icon="runSmoothSplitterIcon"
      ></fa-icon>
      <fa-icon
        v-if="order.missedDeliverCount > 0"
        class="fill-current w-4 h-4 text-red-700 text-lg"
        :icon="['far', 'exclamation-circle']"
      ></fa-icon>
    </td>
    <td>
      <fa-icon
        v-if="!isFinished && $root.user.role !== 'sales'"
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'play']"
        fixed-width
        @click.prevent="startOrder"
      ></fa-icon>
      <fa-icon
        v-if="!isFinished && $root.user.role !== 'sales'"
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'pause']"
        fixed-width
        @click.prevent="pauseOrder"
      ></fa-icon>
      <fa-icon
        v-if="!isFinished && $root.user.role !== 'sales'"
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'stop']"
        fixed-width
        @click.prevent="stopOrder"
      ></fa-icon>
      <fa-icon
        v-if="$root.user.role !== 'sales'"
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'copy']"
        fixed-width
        @click.prevent="$modal.show('clone-order-modal', {order: order})"
      ></fa-icon>
      <fa-icon
        v-if="$root.user.role !== 'sales'"
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="removeDelayedIcon"
        fixed-width
        @click.prevent="removeDelayedAssignments"
      ></fa-icon>
    </td>
    <td v-text="order.date"></td>
    <td
      class="text-sm"
      v-text="timeframe"
    ></td>
    <td v-if="['admin'].includes($root.user.role)">
      <span
        v-if="order.branch_id"
        v-text="order.branch.name"
      >
      </span>
      <span v-else>-</span>
    </td>
    <td>
      <router-link
        :to="{
          name: 'offices.show',
          params: { id: order.office_id }
        }"
        v-text="order.office.name"
      ></router-link>
    </td>
    <td class="text-sm">
      <div class="flex items-center">
        <progress-widget
          class="flex w-1/3"
          :current="order.received"
          :goal="order.ordered"
        ></progress-widget>
        <div class="w-2/3 pl-3">
          <span
            class="flex"
            v-text="
              `${order.received} / ${order.ordered}`
            "
          ></span>
          <span
            class="flex mt-1 text-xs text-gray-500"
            v-text="`${progressPercentage} %`"
          ></span>
        </div>
      </div>
    </td>
    <td class="text-sm">
      <div class="flex items-center">
        <fa-icon
          class="fill-current text-gray-700 text-xs"
          :icon="['far', 'pause']"
          fixed-width
        ></fa-icon>
        <span
          class="flex mx-2 font-medium"
          :class="{'text-orange-500': order.pausedRoutesCount > 0}"
          v-text="order.pausedRoutesCount"
        ></span> / <span
          class="ml-2"
          v-text="order.routes_count"
        ></span>
      </div>
    </td>
    <td
      class="text-sm"
      v-text="`${order.deliveryPercent} %`"
    ></td>
    <td
      v-if="order.lastReceivedAt"
      class="text-sm"
      v-text="order.lastReceivedAt"
    ></td>
    <td v-else>
      -
    </td>
    <td>
      <router-link
        :to="{
          name: 'leads-orders.show',
          params: { id: order.id }
        }"
      >
        Детали
      </router-link>
    </td>
  </tr>
</template>

<script>
import {faReplyAll} from '@fortawesome/pro-regular-svg-icons';
import {faTruckPlow} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'lead-order-list-item',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  computed: {
    isFinished() {
      return this.order.ordered === this.order.received;
    },
    timeframe(){
      if(this.order.start_at == null && this.order.stop_at == null){
        return '00:00 - 23:59';
      }

      if(this.order.start_at == null && this.order.stop_at != null){
        return `00:00 - ${this.order.stop_at}`;
      }

      if(this.order.start_at != null && this.order.stop_at == null){
        return `${this.order.start_at} - 23:59`;
      }

      return `${this.order.start_at} - ${this.order.stop_at}`;
    },
    deliveryHighLight(){
      if(this.order.deliveryPercent <= 80 && this.order.received > 0){
        return 'bg-red-300';
      }

      if(this.order.deliveryPercent < 90 && this.order.deliveryPercent >= 81){
        return 'bg-red-100';
      }

      if(this.order.deliveryPercent < 100 && this.order.deliveryPercent >= 90){
        return 'bg-orange-200';
      }

      return '';
    },
    progressPercentage() {
      return (this.order.ordered === 0 ? 0 : this.order.received / this.order.ordered * 100).toFixed(2);
    },
    runSmoothSplitterIcon() {
      return faReplyAll;
    },
    removeDelayedIcon() {
      return faTruckPlow;
    },
  },
  methods: {
    startOrder() {
      if (confirm('Возобновить выдачу по заказу? ')) {
        axios.post(`/api/leads-order/${this.order.id}/start`)
          .then(response => {
            this.$toast.success({
              title: 'OK',
              message: 'Order was started',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant start order',
            });
          });
      }
    },
    pauseOrder() {
      if (confirm('Приостановить заказ? ')) {
        axios.post(`/api/leads-order/${this.order.id}/pause`)
          .then(response => {
            this.$toast.success({
              title: 'OK',
              message: 'Order was paused',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant pause order',
            });
          });
      }
    },
    stopOrder() {
      if (confirm('Остановка заказа приведет к потере информации о не выданном объеме лидов. Продолжить?')) {
        axios.post(`/api/leads-order/${this.order.id}/stop`)
          .then(response => {
            this.$emit('stopped', {order: response.data});
            this.$toast.success({
              title: 'OK',
              message: 'Order was stopped',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant stop order',
            });
          });
      }
    },
    removeDelayedAssignments() {
      if (confirm('Уверены, что хотите удалить отложенные(неотправленные) назначения ?')) {
        axios.post(`/api/leads-order/${this.order.id}/remove-delayed-assignments`)
          .then(response => {
            this.$emit('delayed-assignments-deleted', {order: response.data});
            this.$toast.success({
              title: 'OK',
              message: 'Delayed assignments removed.',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant remove delayed assignments',
            });
          });
      }
    },
  },
};
</script>

<style scoped>

</style>
