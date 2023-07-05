<template>
  <modal
    name="change-manager-office-modal"
    height="auto"
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
        <label class="flex w-full mb-2 font-semibold">Новый офис</label>
        <mutiselect
          v-model="new_office"
          :show-labels="false"
          :options="offices"
          placeholder="Выберите офер"
          track-by="id"
          label="name"
          @input="errors.clear('office_id')"
        ></mutiselect>
        <span
          v-if="errors.has('office_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('office_id')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="changeOffice"
        >
          Сменить офис
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('change-manager-office-modal')"
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
  name: 'change-manager-office-modal',
  data: () => ({
    isBusy: false,
    manager: {
      id: null,
      name: null,
      office_id: null,
    },
    offices: [],
    new_office: {},
    errors: new ErrorBag(),
  }),
  computed: {
    hasOffices() {
      return this.offices.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.manager = event.params.manager;
      if (!this.hasOffices) {
        this.loadOffices();
      }
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(({data}) => {
          this.offices = data.filter(office => office.id !== this.manager.office_id);
        })
        .catch(error => this.$toast.error({title: 'Could not load offices.', message: error.response.data.message}));
    },
    changeOffice() {
      this.isBusy = true;
      axios
        .post(`/api/managers/${this.manager.id}/change-office`, {office_id: this.new_office.id})
        .then(response => {
          this.$toast.success({title: 'Success', message: 'Manager office has been changed.'});
          this.$modal.hide('change-manager-office-modal');
          this.$emit('office-changed', {manager: response.data});
        })
        .catch(error => {
          this.errors.fromResponse(error);
          this.$toast.error({
            title: 'Unable to change office',
            message: error.response.data.message,
          });
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

