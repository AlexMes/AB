<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <mutiselect
            v-model="offer"
            :show-labels="false"
            :options="allOffers"
            placeholder="Выберите оффер"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>
        <div class="w-auto flex items-center">
          <button
            class="button btn-primary flex items-center ml-3"
            @click="add"
          >
            <fa-icon
              :icon="['far','plus']"
              class="fill-current mr-2"
            ></fa-icon> Добавить
          </button>
        </div>
      </div>
    </div>
    <div
      v-if="hasOffers"
    >
      <div class="shadow">
        <allowed-offer-list-item
          v-for="offer in offers"
          :key="offer.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :offer="offer"
          @deleted="remove(offer)"
        ></allowed-offer-list-item>
      </div>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Пусто</p>
    </div>
  </div>
</template>

<script>
import AllowedOfferListItem from '../../../components/users/allowed-offer-list-item';
export default {
  name: 'users-allowed-offers',
  components: {AllowedOfferListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    offer: null,
    offers: [],
    allOffers: [],
  }),
  computed:{
    hasOffers() {
      return this.offers.length > 0;
    },
  },
  created() {
    this.load();
    this.getAllOffers();
  },
  methods: {
    load() {
      axios
        .get(`/api/users/${this.id}/allowed-offers`)
        .then(response => this.offers = response.data)
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load offers.',
          }),
        );
    },
    add() {
      if (this.offer !== null) {
        axios
          .post(`/api/users/${this.id}/allowed-offers`, {offer_id: this.offer.id})
          .then(response => {
            if (this.offers.findIndex(offer => offer.id === response.data.id) === -1) {
              this.offers.push(response.data);
            }
            this.offer = null;
            this.$toast.success({title: 'Ok', message: 'Offer was added successfully.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to add offer.', message: err.response.data.message}));
      }
    },
    remove(offer) {
      axios.delete(`/api/users/${this.id}/allowed-offers/${offer.id}`)
        .then(response => {
          const index = this.offers.findIndex(offer => offer.id === response.data.id);
          if (index !== -1) {
            this.offers.splice(index, 1);
          }

          this.$toast.success({title: 'Ok', message: 'Offer was detached successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to detach offer.', message: err.response.data.message}));
    },
    getAllOffers() {
      axios
        .get('/api/offers')
        .then(response => this.allOffers = response.data)
        .catch(err => this.$toast.error({title: 'Unable to load all offers.', message: err.response.data.message}));
    },
  },
};
</script>
