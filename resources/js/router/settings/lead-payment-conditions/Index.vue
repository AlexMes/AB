<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск условий"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'lead-payment-conditions.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>

    <div class="flex flex-no-wrap items-center mb-6">
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Офис</label>
        <mutiselect
          v-model="filters.offices"
          :show-labels="false"
          :multiple="true"
          :options="offices"
          placeholder="Выберите офисы"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Офер</label>
        <mutiselect
          v-model="filters.offers"
          :show-labels="false"
          :multiple="true"
          :options="offers"
          placeholder="Выберите оферы"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="flex flex-col ml-auto mr-2 w-1/5">
        <button
          class="button btn-primary text-lg -mb-3 mt-2"
          :class="{'bg-gray-700' : isBusy}"
          :disabled="isBusy"
          @click="load(1)"
        >
          <span v-if="isBusy"><fa-icon
            :icon="['far','spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon> Загрузка</span>
          <span v-else>Применить</span>
        </button>
      </div>
    </div>

    <div
      v-if="hasConditions"
    >
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      <sort
                        title="ID"
                        column="id"
                        :by="sortBy"
                        :asc="sortAsc"
                        @changed="sort"
                      ></sort>
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Офис
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Офер
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Модель
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      <sort
                        title="Цена"
                        column="cost"
                        :by="sortBy"
                        :asc="sortAsc"
                        @changed="sort"
                      ></sort>
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <lead-payment-condition-list-item
                    v-for="condition in conditions"
                    :key="condition.id"
                    :condition="condition"
                    @deleted="remove"
                  ></lead-payment-condition-list-item>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Условий не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import LeadPaymentConditionListItem from '../../../components/settings/lead-payment-condition-list-item';
import Sort from '../../../components/sort';
export default {
  name: 'lead-payment-conditions-index',
  components: {Sort, LeadPaymentConditionListItem},
  data:() => ({
    conditions:{},
    response: {},
    search: null,
    isBusy: false,
    filters: {
      offices: [],
      offers: [],
    },
    offices: [],
    offers: [],
    sortBy: 'id',
    sortAsc: false,
  }),
  computed:{
    hasConditions(){
      return this.conditions !== undefined && this.conditions.length > 0;
    },
    cleanFilters() {
      return {
        office_id: this.filters.offices === null ? null : this.filters.offices.map(office => office.id),
        offer_id: this.filters.offers === null ? null : this.filters.offers.map(offer => offer.id),
      };
    },
  },
  watch: {
    search() {
      this.load();
    },
  },
  created() {
    this.load();
    this.loadOffices();
    this.loadOffers();
  },
  methods: {
    load(page = 1) {
      this.isBusy = true;
      axios.get('/api/lead-payment-conditions', {
        params: {
          page: page,
          sort: this.sortBy,
          asc: this.sortAsc,
          search: this.search === '' ? null : this.search,
          ...this.cleanFilters,
        },
      })
        .then(response => {
          this.response = response.data;
          this.conditions = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить условия.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    loadOffices() {
      axios.get('/api/offices')
        .then(r => this.offices = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список офисов.', message: e.response.data.message}));
    },

    loadOffers() {
      axios.get('/api/offers')
        .then(r => this.offers = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список оферов.', message: e.response.data.message}));
    },

    remove(event) {
      const index = this.conditions.findIndex(condition => condition.id === event.condition.id);
      if (index !== -1) {
        this.conditions.splice(index, 1);
      }
    },
    sort(event) {
      this.sortBy = event.column;
      this.sortAsc = event.asc;
      this.load();
    },
  },
};
</script>
