<template>
  <modal
    name="export-affiliate-leads-modal"
    height="auto"
    :adaptive="true"
    :styles="{ overflow: 'visible' }"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="p-3 mb-2 text-white bg-red-700 rounded"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">С даты</label>
        <date-picker
          v-model="date"
          class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          :config="pickerConfig"
          placeholder="Выберите стартовую дату"
          @input="errors.clear('date')"
        ></date-picker>
        <span
          v-if="errors.has('since')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('since')"
        ></span>
        <span
          v-if="errors.has('until')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('until')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="mr-2 button btn-primary"
          :disabled="isBusy"
          @click="download"
        >
          Скачать
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('export-affiliate-leads-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';
import downloadLink from '../../utilities/helpers/downloadLink';

export default {
  name: 'export-affiliate-leads-modal',
  components: {
    DatePicker,
  },
  data: () => ({
    affiliate: {},
    date: null,
    isBusy: false,
    pickerConfig: {
      mode: 'range',
      maxDate: moment().format('YYYY-MM-DD'),
    },
    errors: new ErrorBag(),
  }),
  computed: {
    datePeriod() {
      if (this.date === null || this.date === '') {
        return null;
      }
      const dates = this.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  methods: {
    beforeOpen(event) {
      this.affiliate = event.params.affiliate;
    },
    download() {
      this.isBusy = true;
      axios
        .get(`/api/affiliates/${this.affiliate.id}/export-leads`, {
          params: this.datePeriod,
          responseType: 'blob',
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Скачивание лидов завершено.',
          });
          this.$modal.hide('export-affiliate-leads-modal');
          this.date = null;
          this.errors = new ErrorBag();
          downloadLink(response.data, 'affiliate-leads.xlsx');
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось скачать лиды.',
          });
        })
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
