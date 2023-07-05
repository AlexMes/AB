<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="`#${condition.id}`"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новое условие
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
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="condition.office"
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
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="condition.offer"
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

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="model"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Модель
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="model"
                v-model="condition.model"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('model')"
              >
                <option :value="null">
                  Не выбрано
                </option>
                <option
                  v-for="(model, index) in models"
                  :key="`model-${index}`"
                  :value="model"
                  v-text="model"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('model')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('model')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="cost"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Цена
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="cost"
                v-model="condition.cost"
                type="number"
                step="1"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('cost')"
              />
            </div>
            <span
              v-if="errors.has('cost')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('cost')"
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
  name: 'lead-payment-conditions-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    condition: {
      office: {},
      offer: {},
      model: null,
      cost: null,
    },
    offices: [],
    offers: [],
    models: ['cpa', 'cpl'],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
    cleanForm() {
      return {
        office_id: !this.condition.office ? null : this.condition.office.id,
        offer_id: !this.condition.offer ? null : this.condition.offer.id,
        model: this.condition.model,
        cost: this.condition.cost,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      if (this.isUpdating) {
        this.load();
      }
      this.loadOffices();
      this.loadOffers();
    },

    load() {
      axios.get(`/api/lead-payment-conditions/${this.id}`)
        .then(r => this.condition = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить условие.', message: err.response.data.message}));
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

    save() {
      this.isUpdating ? this.update() : this.create();
    },

    create() {
      this.isBusy = true;
      axios.post('/api/lead-payment-conditions/', this.cleanForm)
        .then(r => {
          this.$toast.success({title: 'Ok', message: 'Условие оплаты создано.'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить условие.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    update() {
      this.isBusy = true;
      axios.put(`/api/lead-payment-conditions/${this.id}`, this.cleanForm)
        .then(r => this.$router.push({name:'lead-payment-conditions.show',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить условие.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    cancel() {
      if (this.isUpdating) {
        this.$router.push({name:'lead-payment-conditions.show', params:{id: this.id}});
      } else {
        this.$router.push({name:'lead-payment-conditions.index'});
      }
    },
  },
};
</script>
