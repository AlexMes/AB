<template>
  <tr class="hover:bg-gray-100">
    <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'insights.show', params: {id: insight.id}}"
        class="text-sm font-thin"
        v-text="`#${insight.id}`"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'insights.show', params: {id: insight.id}}"
        class="font-normal"
        v-text="date"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <div
        class="font-semibold"
        v-text="insight.account.name"
      ></div>
      <div
        class="text-xs text-gray-600"
        v-text="insight.account.account_id"
      ></div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <div
        class="font-semibold"
        v-text="insight.campaign.name"
      ></div>
      <div
        class="text-xs text-gray-600"
        v-text="insight.campaign.campaign_id"
      ></div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="insight.google_app_id"
        v-text="insight.google_app.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="insight.sales"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="insight.deposit_cnt"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="insight.deposit_sum"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="insight.impressions"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="insight.installs"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="`${insight.spend} $`"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="insight.registrations"
    >
    </td>
  </tr>
</template>

<script>
import moment from 'moment';

export default {
  name: 'insight-list-item',
  props: {
    insight: {
      type: Object,
      required: true,
    },
  },
  computed: {
    date() {
      return moment(this.insight.date).format('DD.MM.YYYY');
    },
  },
};
</script>

<style scoped>

</style>
