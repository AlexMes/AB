<template>
  <div class="flex flex-col">
    <div class="container flex flex-col mx-auto">
      <div
        class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
      >
        <h1 class="flex text-gray-700">
          Долеты
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
          <div class="flex w-3/4">
            <div
              class="flex flex-col w-1/4 mx-2 my-2 align-middle md:my-0"
            >
              <label class="mb-2">Период</label>
              <date-picker
                id="datetime"
                v-model="dates"
                class="w-full px-1 py-2 text-gray-600 border rounded"
                :config="pickerConfig"
                placeholder="Выберите даты"
              ></date-picker>
            </div>

            <div
              class="flex flex-col w-1/4 mx-2 my-2 align-middle md:my-0"
            >
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

            <div
              class="flex flex-col w-1/4 mx-2 my-2 align-middle md:my-0"
            >
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
            <div
              class="flex flex-col w-1/4 mx-2 my-2 align-middle md:my-0"
            >
              <label class="mb-2">Вертикаль</label>
              <mutiselect
                v-model="filterSets.verticals"
                :show-labels="false"
                :multiple="true"
                :options="verticals"
                placeholder="Выберите вертикаль"
                track-by="id"
                label="name"
              ></mutiselect>
            </div>
          </div>

          <div
            class="flex justify-end w-1/4 pt-6 mx-2 my-2 align-middle md:my-0"
          >
            <button
              type="button"
              class="mr-2 button btn-primary"
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
  name: 'lead-office-assignments',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment()
      .startOf('month')
      .format('YYYY-MM-DD')} — ${moment().format('YYYY-MM-DD')}`,
    pickerConfig: {
      defaultDates: `${moment()
        .startOf('month')
        .format('YYYY-MM-DD')} — ${moment().format('YYYY-MM-DD')}`,
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
      offices: [],
      officeGroups: [],
      verticals: [],
    },
    verticals: [
      {id: 'Burj', name: 'Burj'},
      {id: 'Crypt', name: 'Crypt'},
    ],
    isBusy: false,
    offices: [],
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
        offices:
          this.filterSets.offices === null
            ? null
            : this.filterSets.offices.map(office => office.id),
        verticals:
          this.filterSets.verticals === null
            ? null
            : this.filterSets.verticals.map(vertical => vertical.id),
        office_groups:
          this.filterSets.officeGroups === null
            ? null
            : this.filterSets.officeGroups.map(group => group.id),
      };
    },
  },
  created() {
    this.load();
    this.getOffices();
    this.getOfficeGroups();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/conversion-timeline', {
          params: this.cleanFilters,
        })
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

    getOfficeGroups() {
      axios.get('/api/office-groups').then(r => {
        this.officeGroups = r.data;
      });
    },
  },
};
</script>
