<template>
  <modal
    name="edit-deposit-modal"
    height="auto"
    :adaptive="true"
    :styles="{overflow: 'visible'}"
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
        <label
          for="sum"
          class="flex w-full mb-2 font-semibold"
        >Депозит</label>
        <input
          id="sum"
          v-model="deposit.sum"
          type="number"
          placeholder="999"
          class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
        />
        <span
          v-if="errors.has('sum')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('sum')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="update"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('edit-deposit-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'edit-deposit-modal',
  props: {
    /*order: {
      type: Object,
      required: true,
    },*/
  },
  data: () => ({
    deposit: {
      sum: null,
    },
    loading: {
    },
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    //
  },
  methods: {
    beforeOpen(event) {
      this.deposit.id = event.params.deposit.id;
      this.deposit.sum = event.params.deposit.sum;

      this.errors.clear();
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/deposits/${this.deposit.id}`, {
        sum: this.deposit.sum,
      })
        .then(response => {
          this.$modal.hide('edit-deposit-modal');
          this.$toast.success({title: 'OK', message: 'Депозит обновлён.'});
          this.errors = new ErrorBag();
          this.$eventHub.$emit('deposit-updated', {deposit: response.data});
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось обновить депозит.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

