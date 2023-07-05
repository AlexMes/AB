<template>
  <modal
    name="start-resell-batch-modal"
    height="auto"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Выдать до</label>
        <date-picker
          v-model="assign_until"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите дату на которую копировать"
        ></date-picker>
        <span
          v-if="errors.has('assign_until')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('assign_until')"
        ></span>
      </div>
      <div class="flex w-full mb-4">
        <span v-text="timeLeft"></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="start"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('start-resell-batch-modal')"
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
  name: 'start-resell-batch-modal',
  components: {DatePicker},
  data: () => ({
    batch: {},
    assign_until: null,
    pickerConfig: {
      enableTime: true,
      defaultDate: moment().add(1, 'h').add(5, 'm').format('YYYY-MM-DD HH:mm'),
    },
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    timeLeft() {
      const duration = moment.duration(moment(this.assign_until).diff(moment()));
      return `${parseInt(duration.asHours())} ч. и ${duration.minutes()} мин.`;
    },
  },
  methods: {
    beforeOpen(event) {
      this.batch = event.params.batch;
      if (this.batch.status === 'paused') {
        this.assign_until = moment(this.batch.assign_until).format('YYYY-MM-DD HH:mm');
      } else {
        this.assign_until = moment().add(1, 'h').add(5, 'm').format('YYYY-MM-DD HH:mm');
        this.pickerConfig.minDate = moment().add(1, 'h').add(5, 'm').format('YYYY-MM-DD HH:mm');
      }
    },

    start() {
      this.isBusy = true;
      axios.post(`/api/resell-batches/${this.batch.id}/start`, {
        assign_until: this.assign_until,
      })
        .then(response => {
          this.$modal.hide('start-resell-batch-modal');
          this.$toast.success({title: 'Ok', message: 'Перевыдача запущена.'});
          this.errors.clear();
          this.$emit('updated', {batch: response.data});
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось запустить перевыдачу.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

