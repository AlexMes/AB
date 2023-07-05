<template>
  <tr class="hover:bg-gray-100">
    <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'tech-costs.show', params: {id: techCost.id}}"
        class="text-sm font-thin"
        v-text="`#${techCost.id}`"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'tech-costs.show', params: {id: techCost.id}}"
        class="font-normal"
        v-text="date"
      ></router-link>
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="techCost.user.name"
    >
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="`${techCost.spend} $`"
    >
    </td>
  </tr>
</template>

<script>
import moment from 'moment';

export default {
  name: 'tech-cost-list-item',
  props: {
    techCost: {
      type: Object,
      required: true,
    },
  },
  computed: {
    date() {
      return moment(this.techCost.date).format('DD.MM.YYYY');
    },
  },
};
</script>

<style scoped>

</style>
