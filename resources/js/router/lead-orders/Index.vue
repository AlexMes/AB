<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Заказы лидов <router-link
          class="text-sm hover:text-teal-600"
          :to="{name:'leads-orders.old-leftovers'}"
        >
          Старые лиды
        </router-link>
      </h1>
      <div class="flex">
        <div
          v-if="$root.user.role !== 'sales'"
          class="flex mr-4 my-2"
        >
          <fa-icon
            class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
            :icon="['far', 'play']"
            fixed-width
            @click.prevent="startAll"
          ></fa-icon>
          <fa-icon
            class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
            :icon="['far', 'pause']"
            fixed-width
            @click.prevent="pauseAll"
          ></fa-icon>
          <fa-icon
            class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
            :icon="['far', 'stop']"
            fixed-width
            @click.prevent="stopAll"
          ></fa-icon>
        </div>
        <div class="flex flex-col mr-4 my-2 md:my-0">
          <date-picker
            v-model="filters.date"
            class="w-full px-1 py-2 border rounded text-gray-600"
            :config="pickerConfig"
            placeholder="Выберите даты"
            @input="refresh"
          ></date-picker>
        </div>
        <router-link
          v-if="$root.isAdmin && $root.user.role !== 'sales'"
          :to="{ name: 'leads-orders.create' }"
          class="button btn-primary flex items-center ml-3"
        >
          <fa-icon
            :icon="['far', 'plus']"
            class="fill-current mr-2"
          ></fa-icon>
          Добавить
        </router-link>
        <a
          v-if="['admin', 'support'].includes($root.user.role)"
          class="button btn-primary flex items-center ml-3"
          @click.prevent="$modal.show('mass-lead-benefit-modal')"
        >
          <fa-icon
            :icon="['far', 'edit']"
            class="fill-current mr-2"
          ></fa-icon>
          Бенефит
        </a>
      </div>
    </div>

    <!-- Orders progress start -->
    <leads-orders-stats-progress :date="datePeriod"></leads-orders-stats-progress>
    <!-- Order progress end -->
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
            <th
              v-if="['admin'].includes($root.user.role)"
              class="px-2 py-3"
            >
              Филиал
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
            @delayed-assignments-deleted="orderStopped"
          ></lead-order-list-item>
        </tbody>
      </table>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <clone-order-modal></clone-order-modal>
    <mass-lead-benefit-modal></mass-lead-benefit-modal>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import CloneOrderModal from '../../components/leads-orders/clone-order-modal';
import LeadsOrdersStatsProgress from '../../components/widgets/leads-orders-stats-progress';
import LeadOrderListItem from '../../components/leads-orders/lead-order-list-item';
import moment from 'moment';
import MassLeadBenefitModal from '../../components/leads-orders/mass-lead-benefit-modal';

export default {
  name: 'index-leads-orders',
  components: {
    MassLeadBenefitModal,
    LeadOrderListItem,
    LeadsOrdersStatsProgress,
    CloneOrderModal,
    DatePicker,
  },
  data: vm => ({
    orders: [],
    response: null,
    isBusy: false,
    filters: {
      date: null,
    },
    pickerConfig: {
      minDate: vm.$root.user.role === 'developer' ? moment().subtract(2, 'M').format('YYYY-MM-DD') : '2020-03-27',
      mode: 'range',
    },
  }),
  computed: {
    datePeriod() {
      if (this.filters.date === null || this.filters.date === '') {
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
  methods: {
    load(page = 1) {
      axios
        .get('/api/leads-orders', {
          params: {
            page: page,
            date: this.datePeriod,
          },
        })
        .then(({ data }) => {
          this.response = data;
          this.orders = data.data;
        });
    },
    refresh() {
      this.load();
    },

    orderStopped(event) {
      const order = this.orders.find(o => o.id === event.order.id);
      if (order !== undefined) {
        order.ordered = event.order.leadsOrdered;
        order.received = event.order.leadsReceived;
      }
    },

    startAll() {
      if (confirm('Возобновить выдачу текущих заказов? ')) {
        axios.post('/api/leads-order-routes/start')
          .then(response => {
            this.$toast.success({
              title: 'OK',
              message: 'All routes were started',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant start routes',
            });
          });
      }
    },
    pauseAll() {
      if(confirm('Приостановить все текущие заказы? ')){
        axios.post('/api/leads-order-routes/pause')
          .then(response => {
            this.$toast.success({
              title: 'OK',
              message: 'All routes were paused',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant pause routes',
            });
          });
      }

    },
    stopAll() {
      if(confirm('Остановка выдачи приведет к потере информации о невыполненых заказах. Продолжить?')){
        axios.post('/api/leads-order-routes/stop')
          .then(response => {
            this.refresh();
            this.$toast.success({
              title: 'OK',
              message: 'All routes were stopped',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant stop routes',
            });
          });
      }
    },
  },
};
</script>

<style scoped>
td {
    @apply px-2;
    @apply py-3;
}
</style>
