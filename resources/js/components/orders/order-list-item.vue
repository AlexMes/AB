<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td class="px-2 py-3 pl-5">
      <router-link
        :to="{name:'orders.show', params:{id:order.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="`# ${order.id}`"
      >
      </router-link>
    </td>
    <td v-text="order.deadline_at"></td>
    <td>
      <order-progress-widget
        class="w-2/3"
        :current="order.links_done_count"
        :goal="order.links_count"
      >
      </order-progress-widget>
      <span
        class="mt-3 text-xs text-gray-700 inline-block"
        v-text="`${order.links_done_count} / ${order.links_count}`"
      ></span>
    </td>
    <td v-text="order.binom_id">
    </td>
    <td
      v-if="order.sp"
      v-text="order.sp.name"
    >
    </td>
    <td v-else>
      -
    </td>
    <td
      v-if="order.bp"
      v-text="order.bp.name"
    >
    </td>
    <td v-else>
      -
    </td>
    <td
      v-if="order.registrar"
      v-text="order.registrar.name"
    >
    </td>
    <td v-else>
      -
    </td>
    <td
      v-if="order.hosting"
      v-text="order.hosting.name"
    >
    </td>
    <td v-else>
      -
    </td>
    <td
      v-if="order.landing"
      v-text="order.landing.url"
    >
    </td>
    <td v-else>
      -
    </td>
  </tr>
</template>

<script>
import OrderProgressWidget from './progress-widget';

export default {
  name:'orders-list-item',
  components: {OrderProgressWidget},
  props:{
    order:{
      required:true,
      type:Object,
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
