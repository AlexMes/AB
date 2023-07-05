<template>
  <div class="flex flex-col">
    <div class="container flex flex-col mx-auto">
      <div
        class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
      >
        <h1 class="flex text-gray-700">
          Resell doubles
        </h1>
        <span>
          <span
            v-if="report.period"
            class="mt-2 font-medium text-gray-600 md:mt-0"
            v-text="
              `( ${report.period.since} - ${report.period.until} )`
            "
          ></span>
        </span>
      </div>
      <div class="flex flex-col my-6">
        <div
          class="flex flex-wrap items-start md:flex-no-wrap md:items-center"
        >
          <div class="flex flex-col my-2 align-middlemx-2 md:my-0">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="dates"
              class="w-full px-1 py-2 text-gray-600 border rounded"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Разбивка</label>
            <select v-model="filterSets.groupBy">
              <option
                v-for="group in groupByTypes"
                :key="group.key"
                :value="group.key"
                v-text="group.name"
              ></option>
            </select>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Офисы</label>
            <mutiselect
              v-model="filterSets.offices"
              :show-labels="false"
              :multiple="true"
              :options="offices"
              placeholder="Выберите офисы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Группы офисов</label>
            <mutiselect
              v-model="filterSets.officeGroups"
              :show-labels="false"
              :multiple="true"
              :options="officeGroups"
              placeholder="Выберите группы"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div class="flex flex-col mx-2 my-2 align-middle md:my-0 ">
            <label class="mb-2">Оффер</label>
            <mutiselect
              v-model="offers"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.offers"
              placeholder="Выберите оффер"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>

          <div
            class="flex items-end mx-2 my-2 space-x-2 align-middle md:my-0"
          >
            <button
              type="button"
              class="mt-4 button btn-primary"
              :disabled="isBusy"
              @click="load"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far', 'spinner']"
                  class="mr-2 fill-current"
                  spin
                  fixed-width
                ></fa-icon>
                Загрузка
              </span>
              <span v-else>Загрузить</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto bg-white rounded shadow">
      <report-table
        auto-height
        :report="report"
      ></report-table>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'resell-doubles',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
      'YYYY-MM-DD',
    )}`,
    pickerConfig: {
      defaultDates: `${moment().format(
        'YYYY-MM-DD',
      )} to ${moment().format('YYYY-MM-DD')}`,
      minDate: '2019-12-02',
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    report: {
      headers: [],
      rows: [],
      summary: [],
      period: null,
    },
    filterSets: {
      groupBy: 'office',
      offices: [],
      offers: [],
      officeGroups: [],
    },
    isBusy: false,
    groupByTypes: [
      { key: 'office', name: 'Офис' },
    ],
    offices: [],
    offers: [],
    officeGroups: [],
  }),
  computed: {
    period() {
      const dates = this.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        since: this.period.since,
        until: this.period.until,
        groupBy: this.filterSets.groupBy,
        offices:
          this.filterSets.offices === null
            ? null
            : this.filterSets.offices.map(office => office.id),
        offers:
          this.offers === null
            ? null
            : this.offers.map(offer => offer.id),
        officeGroups:
          this.filterSets.officeGroups === null
            ? null
            : this.filterSets.officeGroups.map(group => group.id),
      };
    },
  },
  created() {
    this.load();
    this.getOffices();
    this.getOffers();
    this.getOfficeGroups();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/resell-doubles', { params: this.cleanFilters })
        .then(response => (this.report = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        )
        .finally(() => (this.isBusy = false));
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
    getOffers() {
      axios
        .get('/api/offers')
        .then(r => (this.filterSets.offers = r.data))
        .catch(err =>
          this.$toast.error({
            title: 'Ошибка',
            message: err.response.data.message,
          }),
        );
    },
    getOfficeGroups() {
      axios.get('/api/office-groups')
        .then(r => this.officeGroups = r.data)
        .catch(error => this.$toast.error({title: 'Не удалось загрузить группы офисов', message: error.response.data.message}));
    },
  },
};
</script>
