<template>
  <modal
    name="pack-cold-base"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Период регистрации</label>
        <date-picker
          v-model="formParams.date"
          class="w-full px-1 py-2 border rounded text-gray-600"
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

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Офер</label>
        <mutiselect
          v-model="formParams.offer"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите офер"
          @input="errors.clear('offer')"
        ></mutiselect>
        <span
          v-if="errors.has('offer')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offer')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label
          for="amount"
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1"
        >
          Количество
        </label>
        <div class="max-w-lg rounded-md shadow-sm">
          <input
            id="amount"
            v-model="formParams.amount"
            type="number"
            placeholder="999"
            class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
            required
            @input="errors.clear('amount')"
          />
        </div>
        <span
          v-if="errors.has('amount')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('amount')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="pack"
            >
              Упаковать CD
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('pack-cold-base')"
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
import moment from 'moment';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'pack-cold-base',
  components: {DatePicker},
  data: () => ({
    isBusy: false,
    offers: [],
    formParams: {
      date: `${moment('2022-02-01').format('YYYY-MM-DD')} — ${moment('2022-02-28').format('YYYY-MM-DD')}`,
      offer: null,
      amount: 1000,
    },
    pickerConfig: {
      /*defaultDates: `${moment('2022-02-01').format('YYYY-MM-DD')} — ${moment('2022-02-28').format('YYYY-MM-DD')}`,*/
      mode: 'range',
    },
    errors: new ErrorBag(),
  }),
  computed: {
    datePeriod() {
      if (this.formParams.date === null || this.formParams.date === '') {
        return null;
      }
      const dates = this.formParams.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  created() {
    this.getOffers();
  },
  methods: {
    pack() {
      this.isBusy = true;
      axios
        .post('/api/leads-pack-cold-base', {
          since: this.datePeriod === null ? null : this.datePeriod.since,
          until: this.datePeriod === null ? null : this.datePeriod.until,
          offer: this.formParams.offer === null ? null : this.formParams.offer.id,
          amount: this.formParams.amount,
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Упаковка лидов запущена',
          });
          this.$modal.hide('pack-cold-base');
        })
        .catch(err => {
          this.$toast.error({
            title: 'Ошибка',
            message: err.response.data.message,
          });
          this.errors.fromResponse(err);
        },
        )
        .finally(() => (this.isBusy = false));
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
  },
};
</script>
