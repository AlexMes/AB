<template>
  <modal
    name="transfer-domains-modal"
    height="auto"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Заказ</label>
        <mutiselect
          v-model="order_id"
          :show-labels="false"
          :options="orders.map(ord => ord.id)"
          :loading="isLoading"
          :internal-search="false"
          :max-height="70"
          placeholder="Выберите заказ"
          @search-change="load"
        ></mutiselect>
        <span
          v-if="errors.has('order_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('order_id')"
        ></span>
        <span
          v-if="errors.has('date')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('date')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="changeOrder"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('transfer-domains-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'transfer-domains-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    order_id: null,
    orders: [],
    domain_ids: [],
    isLoading: false,
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasOrders() {
      return this.orders.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.domain_ids = event.params.domains;
      if (!this.hasOrders) {
        this.load();
      }
    },
    load(search = null) {
      this.isLoading = true;
      axios.get('/api/orders', {params: {search}})
        .then(response => {
          this.orders = response.data.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить заказы.', message: e.data.message}))
        .finally(() => this.isLoading = false);
    },
    changeOrder() {
      this.isBusy = true;
      axios.post(`/api/orders/${this.order.id}/transfer-domains`, {
        order_id: this.order_id,
        domain_ids: this.domain_ids,
      })
        .then(response => {
          this.$modal.hide('transfer-domains-modal');
          this.$toast.success({title: 'OK', message: 'Заказ в доменах изменён.'});
          this.errors = new ErrorBag();
          /*this.$eventHub.$emit('order-domain-changed');*/
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось изменить заказ в доменах.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

