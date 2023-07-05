<template>
  <modal
    name="delete-duplicates-modal"
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
          v-model="formParams.offers"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          :multiple="true"
          track-by="id"
          label="name"
          placeholder="Выберите офер"
          @input="errors.clear('offers')"
        ></mutiselect>
        <span
          v-if="errors.has('offers')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offers')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="deleteDuplicates"
            >
              Удалить дубли
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('delete-duplicates-modal')"
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
  name: 'delete-duplicates-modal',
  components: {DatePicker},
  data: () => ({
    isBusy: false,
    offers: [],
    formParams: {
      date: null,
      offers: [],
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
    deleteDuplicates() {
      this.isBusy = true;
      axios
        .post('/api/leads-delete-duplicates', {
          since: this.datePeriod === null ? null : this.datePeriod.since,
          until: this.datePeriod === null ? null : this.datePeriod.until,
          offers: this.formParams.offers.map(offer => offer.id),
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Удаление дублей успешно',
          });
          this.$modal.hide('delete-duplicates-modal');
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
        .then(response => {
          this.offers = response.data;
          this.formParams.offers = this.offers.filter(offer => offer.branch_id === 19);
        })
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
