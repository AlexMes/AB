<template>
  <div class="flex flex-col">
    <div class="flex flex-col container mx-auto">
      <div class="flex flex-col md:flex-row w-full justify-between items-start md:items-center">
        <h1 class="text-gray-700 flex">
          Статус прохождения ссылки
        </h1>
        <span>
          <!--          <span-->
          <!--            v-if="report.period"-->
          <!--            class="text-gray-600 font-medium mt-2 md:mt-0"-->
          <!--            v-text="`( ${report.period.since} - ${report.period.until} )`"-->
          <!--          ></span>-->
        </span>
      </div>
      <div class="flex flex-col my-6">
        <div class="flex flex-wrap md:flex-no-wrap items-end md:items-end justify-between">
          <div class="flex">
            <!--            <div class="flex flex-col align-middle mx-2 my-2 md:my-0 ">-->
            <!--              <label class="mb-2">Период</label>-->
            <!--              <date-picker-->
            <!--                id="datetime"-->
            <!--                v-model="dates"-->
            <!--                class="w-full px-1 py-2 border rounded text-gray-600"-->
            <!--                :config="pickerConfig"-->
            <!--                placeholder="Выберите даты"-->
            <!--              ></date-picker>-->
            <!--            </div>-->
            <div class="flex flex-col align-middle mx-2 my-2 md:my-0 ">
              <label class="mb-2">Заказ</label>
              <mutiselect
                v-model="orders"
                :show-labels="false"
                :multiple="true"
                :options="ordersList"
                :preserve-search="true"
                :custom-label="customLabel"
                placeholder="Выберите заказ"
                track-by="id"
                label="id"
                @search-change="getOrders"
              >
                <template
                  slot="singleLabel"
                  slot-scope="props"
                >
                  <span class="option__desc">
                    <span class="option__title">#{{ props.option.id }}</span>
                  </span>
                </template>
                <template
                  slot="option"
                  slot-scope="props"
                >
                  <div>
                    <div>#{{ props.option.id }}</div>
                    <div class="mt-1 text-xs">
                      <span v-if="props.option.sp">{{ props.option.sp.name }} </span>
                      <span v-if="props.option.bp">{{ props.option.bp.name }} </span>
                      <span v-if="props.option.binom_id">{{ props.option.binom_id }} </span>
                    </div>
                  </div>
                </template>
              </mutiselect>
            </div>
          </div>
          <div class="flex flex-col align-middle mx-2 my-2 md:my-0 ">
            <button
              type="button"
              class="button btn-primary mt-4"
              :disabled="isBusy"
              @click="load"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far','spinner']"
                  class="mr-2 fill-current"
                  spin
                  fixed-width
                ></fa-icon> Загрузка
              </span>
              <span v-else>Загрузить</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="container mx-auto">
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
  name: 'reach-status',
  data: () => ({
    dates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
      'YYYY-MM-DD',
    )}`,
    pickerConfig: {
      defaultDates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
        'YYYY-MM-DD',
      )}`,
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
    isBusy:false,
    orders: [],
    ordersList: [],
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
        // since: this.period.since,
        // until: this.period.until,
        orders: !!this.orders ? this.orders.map(order => order.id) : null,
      };
    },
  },
  created() {
    this.load();
    this.getOrders();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/reach-status', { params: this.cleanFilters })
        .then(response => this.report = response.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        ).finally(() => this.isBusy = false);
    },

    getOrders(search = null) {
      axios
        .get('/api/orders', {params: {search}})
        .then(response => this.ordersList = response.data.data)
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить заказы',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
