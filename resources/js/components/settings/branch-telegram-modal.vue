<template>
  <modal
    name="branch-telegram-modal"
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
        <label class="flex w-full mb-2 font-semibold">Сообщение</label>
        <textarea
          v-model="message"
          placeholder=""
          class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          rows="5"
          @input="errors.clear('message')"
        ></textarea>
        <span
          v-if="errors.has('message')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('message')"
        ></span>
        <span
          v-if="errors.has('branch')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('branch')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="mr-2 button btn-primary"
          :disabled="isBusy"
          @click="sendMessage"
        >
          Отправить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('branch-telegram-modal')"
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
  name: 'branch-telegram-modal',
  components: {
    DatePicker,
  },
  data: () => ({
    branch: {},
    message: null,
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
  },
  methods: {
    beforeOpen(event) {
      this.branch = event.params.branch;
    },
    sendMessage() {
      this.isBusy = true;
      axios
        .post(`/api/branches/${this.branch.id}/send-tg-message`, {
          message: this.message,
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Сообщение отправлено.',
          });
          this.$modal.hide('branch-telegram-modal');
          this.message = null;
          this.errors = new ErrorBag();
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось отправить сообщение.',
          });
        })
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
