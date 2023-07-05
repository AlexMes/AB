<template>
  <div
    class="overflow-x-auto overflow-y-hidden flex w-full bg-white shadow no-last-border"
  >
    <table class="w-full table table-auto relative">
      <thead
        class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky"
      >
        <tr>
          <th class="pl-5 px-2 py-3">
            #
          </th>
          <th class="px-2 py-3">
          </th>
          <th class="px-2 py-3">
            Дата
          </th>
          <th class="px-2 py-3">
            Время
          </th>
          <th class="px-2 py-3">
            Офис
          </th>
          <th class="px-2 py-3">
            Прогресс
          </th>
          <th class="px-2 py-3">
            Роуты
          </th>
          <th class="px-2 py-3">
            Доставлено
          </th>
          <th class="px-2 py-3">
            Последний
          </th>
          <th class="px-2 py-3"></th>
        </tr>
      </thead>
      <tbody class="w-full">
        <lead-order-list-item
          v-for="order in orders"
          :key="order.id"
          :order="order"
          @stopped="orderStopped"
        ></lead-order-list-item>
      </tbody>
    </table>
    <clone-order-modal></clone-order-modal>
  </div>
</template>

<script>
import CloneOrderModal from '../../components/leads-orders/clone-order-modal';
import moment from 'moment';
import LeadOrderListItem from '../leads-orders/lead-order-list-item';

export default {
  name: 'leads-orders',
  components: {
    LeadOrderListItem,
    CloneOrderModal,
  },
  data:()=>({
    orders: [],
    filters: {
      date: moment().format('YYYY-MM-DD'),
    },
  }),
  computed: {
    datePeriod() {
      if (this.filters.date === null) {
        return null;
      }
      const dates = this.filters.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios
        .get('/api/leads-orders', {
          params: {
            all: true,
            date: this.datePeriod,
          },
        })
        .then(({ data }) => {
          this.orders = data;
        });
    },
    orderStopped(event) {
      const order = this.orders.find(o => o.id === event.order.id);
      if (order !== undefined) {
        order.ordered = event.order.leadsOrdered;
        order.received = event.order.leadsReceived;
      }
    },
  },
};
</script>
