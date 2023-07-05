<template>
  <modal
    name="transfer-assignment-modal"
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
        <label class="flex w-full mb-2 font-semibold">Менеджер</label>
        <mutiselect
          v-model="targetManager"
          :show-labels="false"
          :multiple="false"
          :options="managers"
          placeholder="Выберите менеджера"
          track-by="id"
          label="name"
          @input="errors.clear('manager_id')"
        ></mutiselect>
        <span
          v-if="errors.has('manager_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('manager_id')"
        ></span>
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
          @click="transfer"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('transfer-assignment-modal')"
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
  name: 'transfer-assignment-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    assignment: {},
    targetManager: null,
    managers: [],
    isLoading: false,
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
      this.assignment = event.params.assignment;
      if (!this.hasManagers) {
        this.load();
      }
    },
    load() {
      this.isLoading = true;
      axios.get(`/api/offices/${this.order.office_id}/managers`)
        .then(response => {
          this.managers = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить менеджеров оффиса.', message: e.data.message}))
        .finally(() => this.isLoading = false);
    },
    transfer() {
      this.isBusy = true;
      axios.post(`/api/assignments/${this.assignment.id}/transfer`, {
        manager_id: this.targetManager === null ? null : this.targetManager.id,
      })
        .then(response => {
          this.$modal.hide('transfer-assignment-modal');
          this.$toast.success({title: 'OK', message: 'Лид переназначен.'});
          this.errors = new ErrorBag();
          this.$eventHub.$emit('assignment-transferred', this.assignment, response.data);
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось передать лида'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

