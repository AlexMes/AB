<template>
  <modal
    name="mass-lead-benefit-modal"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <h2>Массовое изменение бенефита</h2>
      <div class="flex flex-col w-full">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">На модель</label>
        <select
          v-model="params.model"
          @input="errors.clear('to_model')"
        >
          <option
            v-for="model in models"
            :key="model.id"
            :value="model.id"
            v-text="model.id"
          ></option>
        </select>
        <span
          v-if="errors.has('to_model')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('to_model')"
        ></span>
      </div>
      <div class="flex flex-col w-full">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">Офисы</label>
        <mutiselect
          v-model="params.office"
          :show-labels="false"
          :limit="3"
          :options="offices"
          :max-height="70"
          track-by="id"
          label="name"
          class="mt-1"
          placeholder="Выберите офисы"
          @input="errors.clear('office')"
        ></mutiselect>
        <span
          v-if="errors.has('office')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('office')"
        ></span>
      </div>
      <div class="flex flex-col w-full">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">Оферы</label>
        <mutiselect
          v-model="params.offer"
          :show-labels="false"
          :limit="3"
          :options="offers"
          :max-height="70"
          track-by="id"
          label="name"
          class="mt-1"
          placeholder="Выберите оферы"
          @input="errors.clear('offer')"
        ></mutiselect>
        <span
          v-if="errors.has('offer')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offer')"
        ></span>
      </div>

      <div class="flex flex-col w-full">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">За период(пусто - за все время)</label>
        <date-picker
          v-model="params.period"
          class="w-full px-1 py-2 mt-1 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите даты"
          @input="errors.clear('since');errors.clear('until')"
        ></date-picker>
        <span
          v-if="errors.has('since')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('since')"
        ></span>
        <span
          v-if="errors.has('until')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('until')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-4">
        <label
          for="benefit"
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
        >
          Сумма
        </label>
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg rounded-md shadow-sm">
            <input
              id="benefit"
              v-model="params.benefit"
              type="number"
              step="0.1"
              placeholder="999"
              class="block w-full mt-1 transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
              required
              @input="errors.clear('benefit')"
            />
          </div>
          <span
            v-if="errors.has('benefit')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('benefit')"
          ></span>
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="setBenefit"
            >
              Установить бенефит
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('mass-lead-benefit-modal')"
            >
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>
  </modal>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import ErrorBag from '../../utilities/ErrorBag';
import moment from 'moment';

export default {
  name: 'mass-lead-benefit-modal',
  components: {DatePicker},
  data: () => ({
    isBusy: false,
    offices: [],
    offers: [],
    models: [
      {id: 'cpl'},
      {id: 'cpa'},
    ],
    params: {
      office: null,
      offer: null,
      period: null,
      benefit: null,
      model: 'cpa',
    },
    pickerConfig: {
      mode: 'range',
    },
    errors: new ErrorBag(),
  }),
  computed: {
    cleanParams() {
      const dates = (this.params.period === null || this.params.period === '') ? null : this.params.period.split(' ');

      return {
        office:
          this.params.office === null
            ? null
            : this.params.office.id,
        offer:
          this.params.offer === null
            ? null
            : this.params.offer.id,
        benefit: this.params.benefit,
        to_model: this.params.model,
        since: !dates ? null : dates[0],
        until: !dates ? null : (dates[2] || dates[0]),
      };
    },
  },
  created() {
    this.getOffices();
    this.getOffers();
  },
  methods: {
    setBenefit() {
      this.isBusy = true;
      axios
        .post('/api/mass-set-lead-benefit', this.cleanParams)
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Успешно',
          });
        })
        .catch(err => {
          this.$toast.error({
            title: 'Ошибка',
            message: err.response.data.message,
          });
          this.errors.fromResponse(err);
        })
        .finally(() => (this.isBusy = false));
    },

    getOffices() {
      axios
        .get('/api/offices')
        .then(response => (this.offices = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить офисы',
          }),
        );
    },

    getOffers() {
      axios
        .get('/api/offers')
        .then(response => (this.offers = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить оферы',
          }),
        );
    },

    clearForm() {
      this.params.office = null;
      this.params.offer = null;
      this.params.period = null;
      this.params.benefit = null;
      this.params.model = 'cpa';
    },
  },
};
</script>
