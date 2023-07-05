<template>
  <div
    v-show="shouldShow"
    class="w-full relative"
  >
    <h2
      v-if="group.name"
      class="mb-2 text-gray-700"
      v-text="group.name"
    ></h2>
    <table
      v-if="hasItems"
      class="table table-auto w-full shadow"
    >
      <thead class="w-full">
        <tr class="px-3">
          <th class="pl-5">
            Название
          </th>
          <th>results</th>
          <th>reach</th>
          <th>frequency</th>
          <th>cpr</th>
          <th>budget</th>
          <th>spend</th>
          <th>impressions</th>
          <th>CPM</th>
          <th>clicks</th>
          <th>cpc</th>
          <th>ctr</th>
        </tr>
      </thead>
      <tbody
        class="bg-white"
      >
        <insights-list-item
          v-for="(insight,index) in cleanItems"
          :key="index"
          :insightful="insight"
          :mode="filters.mode"
        ></insights-list-item>
      </tbody>
      <tfoot>
        <tr>
          <td class="font-bold">
            ВСЕГО
          </td>
          <td
            class="font-bold leading-loose"
            v-text="results"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="reach"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="frequency"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="`$ ${cpr}`"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="`$ ${budget}`"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="`$ ${spend}`"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="impressions"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="`$ ${cpm}`"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="clicks"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="`$ ${cpc}`"
          ></td>
          <td
            class="font-bold leading-loose"
            v-text="`${ctr} %`"
          ></td>
        </tr>
      </tfoot>
    </table>
    <div
      v-else
      class="w-full flex justify-center bg-white shadow p-4 font-medium text-xl text-gray-700"
    >
      <span v-if="isBusy">
        <fa-icon
          :icon="['far','spinner']"
          class="fill-current mr-2"
          size="lg"
          spin
          fixed-width
        ></fa-icon>Загрузка результатов
      </span>
      <span
        v-else
        class="flex text-center"
      >
        Нет результатов
      </span>
    </div>
  </div>
</template>

<script>
import {uniq,uniqBy} from 'lodash-es';
export default {
  name: 'insights-group',
  props:{
    group:{
      type: Object,
      default: () => ({ id:null, name : null}),
    },
    // eslint-disable-next-line vue/require-default-prop
    filters:{
      type: Object,
    },
    only:{
      type: String,
      default :null,
    },
  },
  data:() => ({
    isBusy: false,
    isLoaded:false,
    items: [],
  }),
  computed:{
    hasItems(){
      return this.items !== null && this.items.length > 0;
    },
    shouldShow(){
      if(! this.isLoaded){
        return true;
      }
      if(this.group.id){
        return this.isBusy || this.hasItems;
      }
      return true;
    },
    itemsCnt() {
      return Object.values(this.items).length;
    },
    cleanItems(){
      return this.hasItems ? Object.values(uniqBy(this.items, item => item.id)) : [];
    },
    bid(){
      return this.hasItems ? (this.cleanItems
        .map(result => result.bid_amount)
        .reduce((a,b) => parseFloat(a) + parseFloat(b), 0) / this.cleanItems.length)
        .toFixed(2) : 0;
    },
    results() {
      return this.hasItems ? this.cleanItems
        .map(result => result.results)
        .reduce((a,b) => parseInt(a) + parseInt(b), 0) : 0;
    },
    reach() {
      return this.hasItems ? this.cleanItems
        .map(result => result.reach)
        .reduce((a,b) => parseInt(a) + parseInt(b), 0) : 0;
    },
    frequency() {
      return this.hasItems ? (this.cleanItems
        .map(result => result.frequency)
        .reduce((a,b) => parseFloat(a) + parseFloat(b), 0) / this.cleanItems.length)
        .toFixed(2) : 0;
    },
    cpr() {
      return this.hasItems && this.results > 0 ? (this.spend / this.results).toFixed(2) : 0;
    },
    budget() {
      return this.hasItems ? this.cleanItems
        .map(result => result.budget)
        .reduce((a,b) => parseFloat(a) + parseFloat(b), 0)
        .toFixed(2) : 0;
    },
    spend() {
      return this.hasItems ? this.cleanItems
        .map(result => result.spend)
        .reduce((a,b) => parseFloat(a) + parseFloat(b), 0)
        .toFixed(2) : 0;
    },
    impressions() {
      return this.hasItems ? this.cleanItems
        .map(result => result.impressions)
        .reduce((a,b) => parseInt(a) + parseInt(b), 0) : 0;
    },
    cpm() {
      return this.hasItems ? (this.spend / (this.impressions / 1000)).toFixed(2) : 0;
    },
    clicks() {
      return this.hasItems ? this.cleanItems
        .map(result => result.link_clicks)
        .reduce((a,b) => parseInt(a) + parseInt(b), 0): 0;
    },
    cpc() {
      if(this.hasItems){
        if(this.clicks === 0){
          return 0;
        }
        return (this.spend / this.clicks).toFixed(2);
      }
      return 0;
    },
    ctr() {
      return this.hasItems ? ((this.clicks / this.impressions) * 100).toFixed(2) : 0;
    },
    accounts(){
      if(this.only === 'buyer') {
        let profiles = this.filters.profiles
          .filter(profile => profile.user_id === this.group.id)
          .map(profile => profile.id);
        return this.filters.accounts.filter(account => profiles.includes(account.profile_id))
          .map(account => account.id);
      }
      if(this.only === 'profile'){
        return this.filters.accounts.filter(account => account.profile_id === this.group.id)
          .map(account => account.id);
      }
      if(this.only === 'account'){
        return [this.group.id];
      }

      return this.filters.accounts.map(account => account.id);
    },
  },
  beforeDestroy() {
    this.items = [];
  },
  methods:{
    load(){
      this.items = [];
      this.isBusy = true;
      this.isLoaded = false;
      Promise.all(uniq(this.accounts).map(account => axios.get('/api/reports/statistic/',{params: {
        period: this.filters.period,
        mode: this.filters.mode,
        statuses: this.filters.statuses,
        accounts: account,
      }})))
        .then(responses => responses.forEach(response => {
          let items = Object.values(response.data);
          items.forEach(item => this.items.push(item));
        }))
        .catch(failures => console.error)
        .finally(() => {this.isBusy = false; this.isLoaded = true;});
    },
  },
};
</script>

<style scoped>
tfoot{@apply bg-gray-200;@apply text-left;}
th {
    @apply text-left;
    @apply px-2;
    @apply py-3;
    @apply sticky;
    @apply top-0;
    @apply bg-gray-300;
    @apply text-gray-700;
    @apply uppercase;
    @apply font-semibold;
}
tfoot > td {@apply py-2;@apply text-gray-600;@apply text-left; @apply pl-1;}
</style>
