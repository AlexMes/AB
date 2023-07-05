<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="`# ${rule.id}`"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новое правило
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                v-model="rule.office"
                :show-labels="false"
                :options="offices"
                placeholder="Выберите офис"
                track-by="id"
                label="name"
                @input="errors.clear('office_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('office_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('office_id')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Офер
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                v-model="rule.offer"
                :show-labels="false"
                :options="offers"
                placeholder="Выберите офер"
                track-by="id"
                label="name"
                @input="errors.clear('offer_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('offer_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('offer_id')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Страна
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                v-model="rule.geo"
                :show-labels="false"
                :allow-empty="false"
                :options="countries"
                placeholder="Выберите страну"
                track-by="country"
                label="country_name"
                @input="errors.clear('geo')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('geo')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('geo')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Запретить/Разрешить
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="rule.allowed"></toggle>
            </div>
            <span
              v-if="errors.has('allowed')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('allowed')"
            ></span>
          </div>
        </div>
        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="save"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';
export default {
  name: 'distribution-rules-form',

  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },

  data:()=>({
    isBusy: false,
    rule: {
      office: null,
      offer: null,
      geo: null,
      allowed: false,
    },
    offices: [],
    offers: [],
    countries: [],
    errors: new ErrorBag(),
  }),

  computed:{
    isUpdating() {
      return this.id !== null && this.id !== undefined;
    },

    cleanForm() {
      return {
        office_id: !this.rule.office ? null : this.rule.office.id,
        offer_id: !this.rule.offer ? null : this.rule.offer.id,
        geo: !this.rule.geo ? null : this.rule.geo.country,
        country_name: !this.rule.geo ? null : this.rule.geo.country_name,
        allowed: this.rule.allowed,
      };
    },
  },

  created() {
    this.boot();
  },

  methods: {
    boot() {
      this.loadOffices();
      this.loadOffers();
      if (this.isUpdating) {
        this.load();
      } else {
        this.loadCountries();
      }
    },

    load() {
      axios.get(`/api/distribution-rules/${this.id}`)
        .then(r => this.rule = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить правило.', message: err.response.data.message});
        })
        .finally(() => this.loadCountries());
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

    loadCountries() {
      axios.get('/api/geo/countries')
        .then(r => {
          this.countries = r.data;
          /*this.rule.geo = this.countries.find(country => country.country === this.rule.geo);*/
          const index = this.countries.findIndex(country => country.country === this.rule.geo);
          if (index !== -1) {
            this.rule.geo = this.countries[index];
          }
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список стран.', message: e.response.data.message}));
    },

    save() {
      this.isUpdating ? this.update() : this.create();
    },

    create() {
      this.isBusy = true;
      axios.post('/api/distribution-rules/', this.cleanForm)
        .then(r => {
          this.$router.push({name:'distribution-rules.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить правило.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    update() {
      this.isBusy = true;
      axios.put(`/api/distribution-rules/${this.id}`, this.cleanForm)
        .then(r => this.$router.push({name:'distribution-rules.index',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить правило.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    cancel() {
      this.$router.push({name:'distribution-rules.index'});
    },
  },
};
</script>
