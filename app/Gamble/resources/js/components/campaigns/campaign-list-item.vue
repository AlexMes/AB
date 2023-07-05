<template>
  <tr class="hover:bg-gray-100">
    <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'campaigns.show', params: {id: campaign.id}}"
        class="text-sm font-thin"
        v-text="`#${campaign.id}`"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <div class="font-semibold">
        <router-link
          :to="{name: 'campaigns.show', params: {id: campaign.id}}"
          v-text="campaign.name"
        ></router-link>
      </div>
      <div
        class="text-xs text-gray-600"
        v-text="campaign.campaign_id"
      ></div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <div
        class="font-semibold"
        v-text="campaign.account.name"
      ></div>
      <div
        class="text-xs text-gray-600"
        v-text="campaign.account.account_id"
      ></div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="campaign.offer"
        v-text="campaign.offer.name"
      ></span>
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="createdAt"
    >
    </td>
  </tr>
</template>

<script>
import moment from 'moment';

export default {
  name: 'campaign-list-item',
  props: {
    campaign: {
      type: Object,
      required: true,
    },
  },
  computed: {
    createdAt() {
      return moment(this.campaign.created_at).format('DD.MM.YYYY HH:mm:ss');
    },
  },
};
</script>

<style scoped>

</style>
