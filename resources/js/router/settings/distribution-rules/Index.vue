<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск правил"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'distribution-rules.create'}"
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
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Страна</label>
        <mutiselect
          v-model="filters.countries"
          :show-labels="false"
          :multiple="true"
          :options="countries"
          placeholder="Выберите страны"
          track-by="country"
          label="country_name"
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
      v-if="hasRules"
    >
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Офис
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Офер
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Страна
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Разрешено/Запрещено
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <distribution-rule-list-item
                    v-for="rule in rules"
                    :key="rule.id"
                    :rule="rule"
                    @updated="update"
                    @deleted="remove"
                  ></distribution-rule-list-item>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Правил не найдено</h2>
    </div>
  </div>
</template>

<script>
import DistributionRuleListItem from '../../../components/settings/distribution-rule-list-item';
export default {
  name: 'distribution-rules-index',
  components: {DistributionRuleListItem},
  data:() => ({
    rules:{},
    response: {},
    search: null,
    isBusy: false,
    filters: {
      offices: [],
      offers: [],
      countries: [],
    },
    offices: [],
    offers: [],
    countries: [],
  }),
  computed: {
    hasRules() {
      return this.rules !== undefined && this.rules.length > 0;
    },
    cleanFilters() {
      return {
        office_id: this.filters.offices === null ? null : this.filters.offices.map(office => office.id),
        offer_id: this.filters.offers === null ? null : this.filters.offers.map(offer => offer.id),
        geo: this.filters.countries === null ? null : this.filters.countries.map(country => country.country),
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
    this.loadCountries();
  },
  methods: {
    load(page = 1) {
      this.isBusy = true;
      axios.get('/api/distribution-rules', {
        params: {
          paginate: true,
          page: page,
          search: this.search === '' ? null : this.search,
          ...this.cleanFilters,
        },
      })
        .then(response => {
          this.response = response.data;
          this.rules = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить правила.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    loadOffices() {
      axios.get('/api/offices')
        .then(r => {
          this.offices = r.data;
          this.offices.unshift({id: null, name: 'Без офиса'});
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список офисов.', message: e.response.data.message}));
    },

    loadOffers() {
      axios.get('/api/offers')
        .then(r => this.offers = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список оферов.', message: e.response.data.message}));
    },

    loadCountries() {
      axios.get('/api/geo/countries')
        .then(r => this.countries = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список стран.', message: e.response.data.message}));
    },

    update(event) {
      const index = this.rules.findIndex(rule => rule.id === event.rule.id);
      if (index !== -1) {
        this.$set(this.rules, index, event.rule);
      }
    },

    remove(event) {
      const index = this.rules.findIndex(rule => rule.id === event.rule.id);
      if (index !== -1) {
        this.rules.splice(index, 1);
      }
    },
  },
};
</script>
