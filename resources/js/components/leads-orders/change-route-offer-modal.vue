<template>
  <modal
    name="change-route-offer-modal"
    :height="250"
    :adaptive="true"
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
        <label class="flex w-full mb-2 font-semibold">Оффер</label>
        <mutiselect
          v-model="offer"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          placeholder="Выберите офер"
          track-by="id"
          label="name"
          @input="errors.clear('offer_id')"
        ></mutiselect>
        <span
          v-if="errors.has('offer_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('offer_id')"
        ></span>
        <span
          v-if="errors.has('route')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('route')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="changeOffer"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('change-route-offer-modal')"
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
  name: 'change-route-offer-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    route: {},
    offer: null,
    offers: [],
    isLoading: false,
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasOffers() {
      return this.offers.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.route = event.params.route;
      if (!this.hasOffers) {
        this.load();
      }
    },
    load() {
      this.isLoading = true;
      axios.get('/api/offers')
        .then(response => {
          this.offers = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить офферы.', message: e.data.message}))
        .finally(() => this.isLoading = false);
    },
    changeOffer() {
      this.isBusy = true;
      axios.post(`/api/leads-order-routes/${this.route.id}/change-offer`, {
        offer_id: this.offer !== null ? this.offer.id : null,
      })
        .then(response => {
          this.$modal.hide('change-route-offer-modal');
          this.$toast.success({title: 'OK', message: 'Оффер невыданных заказов изменён.'});
          this.errors = new ErrorBag();
          this.$eventHub.$emit('route-offer-changed', this.route, response.data);
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось изменить оффер.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

