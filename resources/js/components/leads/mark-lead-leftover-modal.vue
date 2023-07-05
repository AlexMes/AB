<template>
  <modal
    name="mark-lead-leftover-modal"
    :height="150"
    :adaptive="true"
    @before-open="beforeOpen"
    @before-close="reset"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Удалить назначения</label>
        <toggle v-model="delete_assignments"></toggle>
        <span
          v-if="errors.has('delete_assignments')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('delete_assignments')"
        ></span>
        <span
          v-if="errors.has('lead')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('lead')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="markLeftover"
        >
          Перевести в LO
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('mark-lead-leftover-modal')"
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
  name: 'mark-lead-leftover-modal',
  data: () => ({
    lead: {},
    isBusy: false,
    delete_assignments: false,
    errors: new ErrorBag(),
  }),
  methods: {
    beforeOpen(event) {
      this.lead = event.params.lead;
    },
    markLeftover() {
      this.isBusy = true;
      axios.post(`/api/leads/${this.lead.id}/mark-leftover`, {
        delete_assignments: this.delete_assignments,
      })
        .then(response => {
          this.$eventHub.$emit('lead-updated', {lead: response.data});
          this.$modal.hide('mark-lead-leftover-modal');
          this.$toast.success({title: 'OK', message: 'Лид переведён в LO.'});
          this.errors = new ErrorBag();
        })
        .catch(err => this.$toast.error({
          title: 'Не удалось перевести лид в LO.',
          message: err.response.data.message + ' ' + err.response.data.errors.lead,
        }))
        .finally(() => this.isBusy = false);
    },
    reset() {
      this.delete_assignments = false;
    },
  },
};
</script>

