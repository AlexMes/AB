<template>
  <modal
    name="collect-lead-destination-results-modal"
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
          v-model="since"
          class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          :config="pickerConfig"
          placeholder="Выберите стартовую дату"
          @input="errors.clear('since')"
        ></date-picker>
        <span
          v-if="errors.has('since')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('since')"
        ></span>
        <span
          v-if="errors.has('destination')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('destination')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="mr-2 button btn-primary"
          :disabled="isBusy"
          @click="collectResults"
        >
          Собрать депы
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('collect-lead-destination-results-modal')"
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

export default {
  name: 'collect-lead-destination-results-modal',
  components: {
    DatePicker,
  },
  data: () => ({
    destination: {},
    since: moment().subtract(1, 'd').format('YYYY-MM-DD'),
    isBusy: false,
    pickerConfig: {
      enableTime: false,
      dateFormat: 'Y-m-d',
      maxDate: moment().format('YYYY-MM-DD'),
      defaultDate: moment().subtract(1, 'd').format('YYYY-MM-DD'),
    },
    errors: new ErrorBag(),
  }),
  computed: {
  },
  methods: {
    beforeOpen(event) {
      this.destination = event.params.destination;
    },
    collectResults() {
      this.isBusy = true;
      axios
        .post(`/api/leads-destinations/${this.destination.id}/collect-results`, {
          since: this.since,
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Сбор депозитов доставки запущен.',
          });
          this.$modal.hide('collect-lead-destination-results-modal');
          this.errors = new ErrorBag();
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось запустить сбор депозитов доставки.',
          });
        })
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
