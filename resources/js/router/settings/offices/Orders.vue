<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <span class="inline-flex rounded-md shadow-sm">
          <router-link
            v-if="$root.isAdmin"
            class="cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            :to="{ name: 'leads-orders.create', params: { officeId: id } }"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400"
              fixed-width
            ></fa-icon>
            <span>
              Добавить
            </span>
          </router-link>
        </span>
      </div>
    </div>
    <div
      v-if="hasOrders"
    >
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
                Прогресс
              </th>
              <th class="px-2 py-3">
                Последний
              </th>
              <th class="px-2 py-3"></th>
            </tr>
          </thead>
          <tbody class="w-full">
            <tr
              v-for="order in orders"
              :key="order.id"
            >
              <td
                class="pl-5"
                v-text="order.id"
              ></td>
              <td>
                <fa-icon
                  v-if="!isFinished(order)"
                  class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                  :icon="['far', 'play']"
                  fixed-width
                  @click.prevent="startOrder(order.id)"
                ></fa-icon>
                <fa-icon
                  v-if="!isFinished(order)"
                  class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                  :icon="['far', 'pause']"
                  fixed-width
                  @click.prevent="pauseOrder(order.id)"
                ></fa-icon>
                <fa-icon
                  v-if="!isFinished(order)"
                  class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                  :icon="['far', 'stop']"
                  fixed-width
                  @click.prevent="stopOrder(order.id)"
                ></fa-icon>
                <fa-icon
                  class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                  :icon="['far', 'copy']"
                  fixed-width
                  @click.prevent="$modal.show('clone-order-modal', {order: order})"
                ></fa-icon>
              </td>
              <td v-text="order.date"></td>
              <td class="text-sm">
                <div class="flex">
                  <progress-widget
                    class="flex w-1/3"
                    :current="order.received"
                    :goal="order.ordered"
                  ></progress-widget>
                  <span
                    class="flex ml-3"
                    v-text="
                      `${order.received} / ${order.ordered}`
                    "
                  ></span>
                </div>
              </td>
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
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Заказы не найдено</p>
    </div>
    <clone-order-modal></clone-order-modal>
  </div>
</template>

<script>
import CloneOrderModal from '../../../components/leads-orders/clone-order-modal';
export default {
  name: 'offices-orders',
  components: {CloneOrderModal},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    orders: [],
    response: null,
  }),
  computed:{
    hasOrders() {
      return this.orders.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/offices/${this.id}/orders`, {params: {page, paginate: true}})
        .then(response => {
          this.orders = response.data.data;
          this.response = response.data;
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load orders',
          }),
        );
    },
    isFinished(order) {
      return order.ordered === order.received;
    },
    startOrder(orderId) {
      if(confirm('Возобновить выдачу по заказу? ' )){
        axios.post(`/api/leads-order/${orderId}/start`)
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
    pauseOrder(orderId) {
      if(confirm('Приостановить заказ? ')){
        axios.post(`/api/leads-order/${orderId}/pause`)
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
    stopOrder(orderId) {
      if(confirm('Остановка заказа приведет к потере информации о не выданном объеме лидов. Продолжить?')){
        axios.post(`/api/leads-order/${orderId}/stop`)
          .then(response => {
            const order = this.orders.find(o => o.id === response.data.id);
            if (order !== undefined) {
              order.ordered = response.data.leadsOrdered;
              order.received = response.data.leadsReceived;
            }
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
  },
};
</script>
