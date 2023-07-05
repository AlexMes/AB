<template>
  <modal
    name="leftovers-change-offer"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <h3
        class="flex w-full mb-2 font-semibold"
      >
        Замена оферов
      </h3>
      <div class="flex flex-col w-full mb-2">
        <label
          for="leadsFile"
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1"
        >
          Загрузить файл
          <input
            id="leadsFile"
            ref="leadsFile"
            type="file"
            accept=".xlsx, .xls"
            class="w-full px-1 py-2 border rounded text-gray-600"
          />
        </label>
        <span
          v-if="errors.has('leads_file')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('leads_file')"
        ></span>
      </div>
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
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">С офера</label>
        <mutiselect
          v-model="formParams.offerFrom"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите офер"
          @input="errors.clear('offer_from')"
        ></mutiselect>
        <span
          v-if="errors.has('offer_from')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offer_from')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">В офер</label>
        <mutiselect
          v-model="formParams.offerTo"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите офер"
          @input="errors.clear('offer_to')"
        ></mutiselect>
        <span
          v-if="errors.has('offer_to')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offer_to')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-4">
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="changeOffer"
            >
              Заменить
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('leftovers-change-offer')"
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

export default {
  name: 'leftovers-change-offer',
  components: {DatePicker},
  data: () => ({
    isBusy: false,
    offers: [],
    formParams: {
      date: null,
      offerFrom: null,
      offerTo: null,
    },
    pickerConfig: {
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
    changeOffer() {
      this.isBusy = true;
      const formData = new FormData();
      if (this.$refs.leadsFile.files[0]) {
        formData.append('leads_file', this.$refs.leadsFile.files[0]);
      } else {
        formData.append('since', this.datePeriod === null ? null : this.datePeriod.since);
        formData.append('until', this.datePeriod === null ? null : this.datePeriod.until);
        formData.append('offer_from', this.formParams.offerFrom === null ? null : this.formParams.offerFrom.id);
        formData.append('offer_to', this.formParams.offerTo === null ? null : this.formParams.offerTo.id);
      }

      axios
        .post('/api/leads-leftovers-change-offer', formData, {headers: {
          'Content-Type': 'multipart/form-data',
        }})
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Замена офера выполнена.',
          });
          this.$modal.hide('leftovers-change-offer');
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
