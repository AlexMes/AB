<template>
  <modal
    name="stop-adsets-modal"
    height="auto"
    styles="overflow: visible"
  >
    <div class="flex flex-col w-full h-full p-6 justify-between">
      <div
        v-if="isAdmin"
        class="flex flex-col w-full mb-4"
      >
        <label class="flex w-full mb-2 font-semibold">Баеры</label>
        <multiselect
          v-model="selectedUsers"
          :options="users"
          :show-labels="false"
          :multiple="true"
          label="name"
          placeholder="Выберите баеров"
          track-by="id"
        ></multiselect>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Оферы</label>
        <multiselect
          v-model="selectedOffers"
          :options="offers"
          :show-labels="false"
          :multiple="true"
          label="name"
          placeholder="Выберите оферы"
          track-by="id"
        ></multiselect>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isLoading"
          @click="save"
        >
          Остановить
        </button>
        <button
          class="button btn-secondary"
          @click="close"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';

export default {
  name: 'stop-adsets-modal',
  components: {
    Multiselect,
  },
  data: () => ({
    offers: [],
    selectedOffers: [],
    isLoading: false,
    users: [],
    selectedUsers: [],
  }),
  computed: {
    filter() {
      return {
        offers: this.selectedOffers.map(offer => offer.id),
        users: this.selectedUsers.map(user => user.id),
      };
    },
    isAdmin() {
      return this.$root.user.role === 'admin';
    },
  },
  created() {
    this.getOffers();
    this.isAdmin && this.getUsers();
  },
  methods: {
    save() {
      axios.post('/api/adsets/stop', this.filter)
        .then(r => {
          this.$toast.info({title: 'Ок', message: r.data});
        })
        .catch(e => {
          this.$toast.error({title: 'Ошибка', message: e.response.data.message});
        });
    },
    getOffers() {
      axios.get('/api/offers')
        .then(r => this.offers = r.data)
        .catch(e => {
          this.$toast.error({title: 'Ошибка', message: e.response.data.message});
        });
    },
    close() {
      this.selectedOffers = [];
      this.$modal.hide('stop-adsets-modal');
    },
    getUsers() {
      axios.get('/api/users', {params: { all: true}})
        .then(r => this.users = r.data)
        .catch(e => {
          this.$toast.error({title: 'Ошибка', message: e.response.data.message});
        });
    },
  },
};
</script>
