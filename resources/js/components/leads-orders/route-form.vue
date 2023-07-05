<template>
  <form
    class="items-center justify-between w-full p-4 space-y-4 md:flex md:space-y-0"
    @submit.prevent="save"
  >
    <div class="flex flex-col items-center mx-3 md:w-1/6">
      <label
        class="flex w-full mb-2 font-medium text-gray-700"
      >Менеджер</label>
      <mutiselect
        v-model="route.manager"
        :show-labels="false"
        :options="managers"
        placeholder="Выберите менеджера"
        track-by="id"
        label="name"
        @input="errors.clear('manager_id')"
      ></mutiselect>
      <span
        v-if="errors.has('manager_id')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('manager_id')"
      ></span>
    </div>
    <div class="flex flex-col items-center mx-3 md:w-1/6">
      <label
        class="flex w-full mb-2 font-medium text-gray-700"
      >Оффер:
      </label>
      <mutiselect
        v-model="route.offer"
        :show-labels="false"
        :options="offers"
        placeholder="Выберите оффер"
        track-by="id"
        label="name"
        @input="errors.clear('offer_id')"
      ></mutiselect>
      <span
        v-if="errors.has('offer_id')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('offer_id')"
      ></span>
    </div>
    <div class="flex flex-col items-center mx-3 md:w-1/6">
      <label
        class="flex w-full mb-2 font-medium text-gray-700"
      >Доставка:
      </label>
      <mutiselect
        v-model="route.destination"
        :show-labels="false"
        :allow-empty="false"
        :options="destinations"
        placeholder="Выберите доставку"
        track-by="id"
        label="name"
        @input="errors.clear('destination_id')"
      ></mutiselect>
      <span
        v-if="errors.has('destination_id')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('destination_id')"
      ></span>
    </div>
    <div class="flex flex-col items-center mx-3 md:w-1/6">
      <label class="flex w-full mb-2 font-medium text-gray-700">Количество: </label>
      <input
        v-model="route.leadsOrdered"
        type="number"
        class="w-full px-2 py-3 text-gray-700 placeholder-gray-400 border-b"
        @input="errors.clear('leadsOrdered')"
      />
      <span
        v-if="errors.has('leadsOrdered')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('leadsOrdered')"
      ></span>
    </div>
    <div class="flex flex-col items-center mx-3 md:w-1/12">
      <label class="flex w-full mb-2 font-medium text-gray-700">Старт:</label>
      <input
        v-model="route.start_at"
        type="time"
        class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        @input="errors.clear('start_at')"
      />
      <span
        v-if="errors.has('start_at')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('start_at')"
      ></span>
    </div>
    <div class="flex flex-col items-center mx-3 md:w-1/12">
      <label class="flex w-full mb-2 font-medium text-gray-700">Стоп:</label>
      <input
        v-model="route.stop_at"
        type="time"
        class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        @input="errors.clear('stop_at')"
      />
      <span
        v-if="errors.has('stop_at')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('stop_at')"
      ></span>
    </div>
    <div class="flex flex-col items-start mx-3 md:w-1/6">
      <label class="flex w-full mb-2 font-medium text-gray-700">Приоритет: </label>
      <toggle v-model="route.priority"></toggle>
      <span
        v-if="errors.has('priority')"
        class="mt-1 text-sm text-red-600"
        v-text="errors.get('priority')"
      ></span>
    </div>
    <div class="flex flex-col items-end mx-4 md:w-1/6 md:items-center">
      <button
        type="submit"
        class="button btn-primary"
        @click.prevent="save"
      >
        Сохранить
      </button>
    </div>
  </form>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'route-form',
  props: {
    order: {
      type: Object,
      required: true,
    },
    destinations: {
      type: Array,
      required: true,
    },
  },
  data: () => ({
    route: {
      manager: null,
      offer: null,
      destination: null,
      leadsOrdered: 0,
      priority:false,
      start_at: null,
      stop_at: null,
    },
    managers: [],
    offers: [],
    errors: new ErrorBag(),
  }),
  computed: {
    cleanRoute() {
      return {
        manager_id: this.route.manager !== null ? this.route.manager.id : null,
        offer_id: this.route.offer !== null ? this.route.offer.id : null,
        destination_id: this.route.destination !== null ? this.route.destination.id : null,
        leadsOrdered: this.route.leadsOrdered,
        start_at: this.route.start_at,
        stop_at: this.route.stop_at,
        priority: this.route.priority,
      };
    },
  },
  created() {
    this.loadManagers();
    this.loadOffers();
  },
  methods: {
    loadOffers() {
      axios
        .get('/api/offers')
        .then(({ data }) => (this.offers = data))
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Cant load offers',
          }),
        );
    },
    loadManagers() {
      axios
        .get(`/api/offices/${this.order.office_id}/managers`)
        .then(({ data }) => (this.managers = data))
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Cant load managers',
          }),
        );
    },
    save() {
      axios
        .post(`/api/leads-orders/${this.order.id}/routes`, this.cleanRoute)
        .then(response => {
          this.$toast.success({title: 'ok', message: 'route added'});
          this.$eventHub.$emit('lead-order-route-created', {route: response.data});
        })
        .catch(error => this.errors.fromResponse(error));
    },
  },
};
</script>
