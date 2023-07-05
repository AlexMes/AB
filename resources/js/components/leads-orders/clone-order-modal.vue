<template>
  <modal
    name="clone-order-modal"
    height="auto"
    :adaptive="true"
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
        <label class="flex w-full mb-2 font-semibold">Дата</label>
        <date-picker
          id="clonedate"
          v-model="date"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите дату на которую копировать"
        ></date-picker>
        <span
          v-if="errors.has('date')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('date')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="clone"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('clone-order-modal')"
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
  name: 'clone-order-modal',
  components: {DatePicker},
  data: () => ({
    order: {},
    date: `${moment().format('YYYY-MM-DD')}`,
    pickerConfig:{
      defaultDates: `${moment().format('YYYY-MM-DD')}`,
      minDate: moment().format('YYYY-MM-DD'),
    },
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasManagers() {
      return this.managers.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.order = event.params.order;
    },
    clone() {
      this.isBusy = true;
      axios.post(`/api/leads-order/${this.order.id}/clone`, {
        date: this.date,
      })
        .then(response => {
          this.$modal.hide('clone-order-modal');
          this.$toast.success({title: 'OK', message: 'Заказ скопирован.'});
          this.errors = new ErrorBag();
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Не удалось скопировать заказ.', message: e.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

