<template>
  <div class="flex flex-col">
    <div class="flex flex-row justify-between w-full mb-8">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full pb-1 mr-5 bg-transparent border-b outline-none border-grey-500 text-grey-700"
          placeholder="Поиск офферов"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        v-if="$root.user.role !== 'subsupport'"
        :to="{ name: 'offers.create' }"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasOffers"
      class="flex flex-col w-full bg-white shadow"
    >
      <offers-list-item
        v-for="offer in offers"
        :key="offer.id"
        :offer="offer"
      ></offers-list-item>
    </div>
    <div
      v-else
      class="p-4 text-center"
    >
      <h2>Офферов не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import OffersListItem from '../../../components/settings/offers-list-item';
export default {
  name: 'offers-index',
  components: { OffersListItem },
  data: () => ({
    response: {},
    offers: [],
    needle: null,
  }),
  computed: {
    hasOffers() {
      return this.offers.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios
        .get('/api/offers', { params: { page, search: this.needle, paginate: true } })
        .then(response => {
          this.response = response.data;
          this.offers = response.data.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить оферы.',
            message: err.response.data.message,
          });
        });
    },
    search(event) {
      this.needle = event.target.value === '' ? null : event.target.value;
      this.load();
    },
  },
};
</script>
