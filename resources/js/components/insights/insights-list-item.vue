<template>
  <tr class="w-full bg-white items-center border-b px-3 hover:bg-gray-200">
    <td
      class="px-2 py-3 pl-5 flex flex-col border-b-0"
    >
      <span
        class="font-medium"
        v-text="insightful.name"
      ></span>
      <span
        class="mt-2 text-sm text-gray-700"
        v-text="`ID: ${insightful.id}`"
      ></span>
      <span
        class="mt-2 text-sm text-gray-700"
        v-text="`Статус: ${insightful.status} | Ставка: ${insightful.bid_strategy}`"
      ></span>
    </td>
    <td
      class="py-4 px-2"
      v-text="insightful.results"
    ></td>
    <td
      class="py-4 px-2"
      v-text="insightful.reach"
    ></td>
    <td
      class="py-4 px-2"
      v-text="insightful.frequency"
    ></td>
    <td
      class="py-4 px-2"
      v-text="`$ ${insightful.cpr}`"
    ></td>
    <td
      class="py-4 px-2"
    >
      <span>$ {{ insightful.budget }}</span>
      <fa-icon
        v-if="isBudgetEditable"
        :icon="['far','edit']"
        class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
        fixed-width
        @click="$modal.show('insight-budget-modal',{insightful: insightful})"
      ></fa-icon>
    </td>
    <td
      class="py-4 px-2"
      v-text="`$ ${insightful.spend}`"
    ></td>
    <td
      class="py-4 px-2"
      v-text="insightful.impressions"
    ></td>
    <td
      class="py-4 px-2"
      v-text="`$ ${cpm}`"
    ></td>
    <td
      class="py-4 px-2"
      v-text="insightful.link_clicks"
    ></td>
    <td
      class="py-4 px-2"
      v-text="`$ ${cpc}`"
    ></td>
    <td
      class="py-4 px-2"
      v-text="`${ctr} %`"
    ></td>
  </tr>
</template>

<script>
export default {
  name: 'insights-list-item',
  props:{
    insightful:{
      type:Object,
      required:true,
    },
    mode: {
      type: String,
      required: true,
    },
  },
  computed:{
    cpm() {
      return (this.insightful.spend / (this.insightful.impressions / 1000)).toFixed(2);
    },
    cpc() {
      if(this.insightful.link_clicks === 0){
        return 0;
      }
      return (this.insightful.spend / this.insightful.link_clicks).toFixed(2);
    },
    ctr() {
      return ((this.insightful.link_clicks / this.insightful.impressions) * 100).toFixed(2);
    },
    isBudgetEditable() {
      return ['campaign', 'adset'].indexOf(this.mode) !== -1
        && ['daily_budget', 'lifetime_budget'].indexOf(this.insightful.budget_field) !== -1;
    },
  },
};
</script>

<style scoped>
tr {@apply text-left;}
td {@apply text-left;}
</style>
