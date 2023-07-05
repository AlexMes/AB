<template>
  <modal
    name="delete-deposit-modal"
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
      <div
        v-if="(isSupport && $root.user.branch_id === 16) || isAdmin"
        class="flex flex-col w-full mb-4"
      >
        <label
          for="sum"
          class="flex w-full mb-2 font-semibold"
        >Разрешить повторное стягивание депозита</label>
        <div class="mt-1 sm:mt-0 sm:col-span-2">
          <div class="max-w-lg">
            <toggle v-model="recreate_deposit"></toggle>
          </div>
          <span
            v-if="errors.has('recreate_deposit')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('recreate_deposit')"
          ></span>
        </div>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="remove"
        >
          Удалить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('delete-deposit-modal')"
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
  name: 'delete-deposit-modal',

  data: () => ({
    deposit: null,
    recreate_deposit: true,
    loading: {
    },
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed:{
    isAdmin() {
      return this.$root.user.role === 'admin';
    },
    isSupport() {
      return this.$root.user.role === 'support';
    },
  },
  methods: {
    beforeOpen(event) {
      this.deposit = event.params.deposit;
      this.recreate_deposit = event.params.deposit.lead.recreate_deposit;
      this.errors.clear();
    },
    remove() {
      this.isBusy = true;
      axios.delete(`/api/deposits/${this.deposit.id}`, {
        params: {
          recreate_deposit: this.recreate_deposit,
        },
      })
        .then(response => {
          this.$modal.hide('delete-deposit-modal');
          this.$toast.success({title: 'OK', message: 'Депозит удалён.'});
          this.errors = new ErrorBag();
          this.$eventHub.$emit('updated', {deposit: response.data});
        })
        .catch(err => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось удалить депозит.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

