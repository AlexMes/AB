<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Заказ ссылок
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
        <router-link
          v-if="$root.isAdmin"
          :to="{'name':'orders.create'}"
          class="button btn-primary flex items-center ml-3"
        >
          <fa-icon
            :icon="['far','plus']"
            class="fill-current mr-2"
          ></fa-icon> Добавить
        </router-link>
      </div>
    </div>
    <div
      v-if="hasOrders"
    >
      <div
        class="overflow-x-auto overflow-y-hidden flex w-full"
      >
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>Дедлайн</th>
              <th>Прогресс</th>
              <th>Связка</th>
              <th>СП</th>
              <th>БП</th>
              <th>Регистратор</th>
              <th>Хостинг</th>
              <th>Лендинг</th>
            </tr>
          </thead>
          <tbody>
            <order-list-item
              v-for="order in orders"
              :key="order.id"
              :order="order"
              @updated="swapOrder"
            >
            </order-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
  </div>
</template>

<script>
import {set} from 'vue';
export default {
  name: 'orders-index',
  data:() => ({
    orders: [],
    response: [],
  }),
  computed:{
    hasOrders(){
      return this.orders.length > 0;
    },
  },
  created() {
    this.load();
    this.listen();
  },
  beforeDestroy() {
    Echo.leaveChannel('App.Orders');
  },
  methods:{
    load(page = 1){
      axios.get('/api/orders', {params:{page: page}}).then(response => {
        this.response = response.data;
        this.orders = response.data.data;
      })
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить заказы.', message: e.response.data.message});
        });
    },
    search(needle) {
      axios.get('/api/orders', {
        params:{
          search: needle,
        },
      }).then(response => {
        this.response = response.data;
        this.orders = response.data.data;
      });
    },
    listen(){
      Echo.private('App.Orders')
        .listen('.Created',event=> this.orders.unshift(event.order));
    },
    swapOrder(event){
      let index = this.orders.findIndex(order => order.id === event.order.id);
      if (index !== -1) {
        set(this.orders, index, event.order);
      }
    },
  },
};
</script>
