<template>
  <modal
    name="export"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Период</label>
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
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Бранч</label>
        <mutiselect
          v-model="formParams.branch"
          :show-labels="false"
          :options="branches"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите бранч"
          @input="errors.clear('branch')"
        ></mutiselect>
        <span
          v-if="errors.has('branch')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('branch')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Оффис</label>
        <mutiselect
          v-model="formParams.office"
          :show-labels="false"
          :options="offices"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите бранч"
          @input="errors.clear('office')"
        ></mutiselect>
        <span
          v-if="errors.has('office')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('office')"
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
            <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Только остаток</label>
            <div class="max-w-lg">
                <toggle v-model="formParams.leftovers"></toggle>
            </div>
        </div>
      <div class="flex flex-col w-full mb-2">
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="start"
            >
              Начать экспорт
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('export')"
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
import downloadLink from '../../utilities/helpers/downloadLink';

export default {
  name: 'export',
  components: {DatePicker},
  data: () => ({
    isBusy: false,
    offers: [],
    branches: [],
    offices: [],
    leftovers: false,
    formParams: {
      date: `${moment('2022-02-01').format('YYYY-MM-DD')} — ${moment('2022-02-28').format('YYYY-MM-DD')}`,
      offer: null,
      branch: null,
      office: null,
      leftovers: false,
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
    this.getBranches();
    this.getOffices();
  },
  methods: {
    start() {
      this.isBusy = true;
      axios
        .post('/api/leads/export', {
          since: this.datePeriod === null ? null : this.datePeriod.since,
          until: this.datePeriod === null ? null : this.datePeriod.until,
          branch_id: this.formParams.branch === null ? null : this.formParams.branch.id,
          office_id: this.formParams.office === null ? null : this.formParams.office.id,
          offer_id: this.formParams.offer === null ? null : this.formParams.offer.id,
          leftovers: this.formParams.leftovers === null ? false : true,
          responseType: 'blob',
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Экспорт лидов запущен',
          });
          this.$modal.hide('export');
          downloadLink(response.data, 'leads.csv');
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
    getBranches() {
      axios
        .get('/api/branches')
        .then(response => (this.branches = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить филиалы',
            message: error.response.data.message,
          }),
        );
    },
    getOffices() {
      axios
        .get('/api/offices')
        .then(response => (this.offices = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить офисы',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
