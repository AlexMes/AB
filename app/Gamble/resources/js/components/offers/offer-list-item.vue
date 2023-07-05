<template>
  <tr class="hover:bg-gray-100">
    <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'offers.show', params: {id: offer.id}}"
        class="text-sm font-thin"
        v-text="`#${offer.id}`"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'offers.show', params: {id: offer.id}}"
        class="font-normal"
        v-text="offer.name"
      ></router-link>
    </td>
  </tr>
</template>

<script>
import moment from 'moment';

export default {
  name: 'offer-list-item',
  props: {
    offer: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped>

</style>
