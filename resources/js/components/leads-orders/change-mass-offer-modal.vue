<template>
  <modal
    name="change-mass-offer-modal"
    :height="350"
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
        <label class="flex w-full mb-2 font-semibold">Оффер с:</label>
        <mutiselect
          v-model="fromOffer"
          :show-labels="false"
          :options="fromOffers"
          :max-height="200"
          placeholder="Выберите офер"
          track-by="id"
          label="name"
          @input="errors.clear('from_offer_id')"
        ></mutiselect>
        <span
          v-if="errors.has('from_offer_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('from_offer_id')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Оффер на:</label>
        <mutiselect
          v-model="toOffer"
          :show-labels="false"
          :options="toOffers"
          :max-height="100"
          placeholder="Выберите офер"
          track-by="id"
          label="name"
          @input="errors.clear('to_offer_id')"
        ></mutiselect>
        <span
          v-if="errors.has('to_offer_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('to_offer_id')"
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
          @click="$modal.hide('change-mass-offer-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css';
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'change-mass-offer-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    fromOffer: null,
    toOffer: null,
    toOffers: [],
    fromOffers: [],
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasToOffers() {
      return this.toOffers.length > 0;
    },
    hasFromOffers() {
      return this.fromOffers.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.loadFromOffers();

      if (!this.hasToOffers) {
        this.loadToOffers();
      }
    },
    loadFromOffers() {
      axios.get(`/api/leads-order/${this.order.id}/offers`)
        .then(response => this.fromOffers = response.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить офферы заказа.', message: err.response.data.message}));
    },
    loadToOffers() {
      axios.get('/api/offers')
        .then(response => {
          this.toOffers = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить офферы.', message: e.response.data.message}));
    },
    changeOffer() {
      this.isBusy = true;
      axios.post(`/api/leads-order/${this.order.id}/change-offer`, {
        to_offer_id: this.toOffer !== null ? this.toOffer.id : null,
        from_offer_id: this.fromOffer !== null ? this.fromOffer.id : null,
      })
        .then(response => {
          this.$modal.hide('change-mass-offer-modal');
          this.$toast.success({title: 'OK', message: 'Оффер невыданных заказов изменён.'});
          this.errors = new ErrorBag();
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

