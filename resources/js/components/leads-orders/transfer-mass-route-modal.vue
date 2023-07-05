<template>
  <modal
    name="transfer-mass-route-modal"
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
        <label class="flex w-full mb-2 font-semibold">Менеджер с:</label>
        <mutiselect
          v-model="fromManager"
          :show-labels="false"
          :multiple="false"
          :options="fromManagers"
          placeholder="Выберите менеджера"
          track-by="id"
          label="name"
          @input="errors.clear('from_manager_id')"
        ></mutiselect>
        <span
          v-if="errors.has('from_manager_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('from_manager_id')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Оффис второго менеджера</label>
        <mutiselect
          v-model="toOffice"
          :show-labels="false"
          :multiple="false"
          :options="offices"
          placeholder="Выберите оффис"
          track-by="id"
          label="name"
          @input="errors.clear('office_id')"
          @select="loadToManagers"
          @remove="loadToManagers(null)"
        ></mutiselect>
        <span
          v-if="errors.has('office_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('office_id')"
        ></span>
        <span
          v-if="errors.has('date')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('date')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Менеджер на:</label>
        <mutiselect
          v-model="toManager"
          :show-labels="false"
          :multiple="false"
          :options="toManagers"
          placeholder="Выберите менеджера"
          track-by="id"
          label="name"
          @input="errors.clear('to_manager_id')"
        ></mutiselect>
        <span
          v-if="errors.has('to_manager_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('to_manager_id')"
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
          @click="$modal.hide('transfer-mass-route-modal')"
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
  name: 'transfer-mass-route-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    fromManager: null,
    toManager: null,
    toManagers: [],
    fromManagers: [],
    toOffice: null,
    offices: [],
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasToManagers() {
      return this.toManagers.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.loadFromManagers();

      if (!this.hasToManagers) {
        this.loadToManagers();
      }

      this.loadOffices();
    },
    loadFromManagers() {
      axios.get(`/api/leads-order/${this.order.id}/managers`)
        .then(response => this.fromManagers = response.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить менеджеров заказа.', message: err.response.data.message}));
    },
    loadToManagers(toOffice) {
      axios.get(`/api/offices/${toOffice ? toOffice.id : this.order.office_id}/managers`)
        .then(response => {
          this.toManagers = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить менеджеров.', message: e.response.data.message}));
    },
    loadOffices() {
      axios.get(`/api/offices`)
        .then(response => {
          this.offices = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить оффисы.', message: e.response.data.message}));
    },
    transfer() {
      this.isBusy = true;
      axios.post(`/api/leads-order/${this.order.id}/transfer`, {
        to_manager_id: this.toManager === null ? null : this.toManager.id,
        from_manager_id: this.fromManager === null ? null : this.fromManager.id,
      })
        .then(response => {
          this.$modal.hide('transfer-mass-route-modal');
          this.$toast.success({title: 'OK', message: 'Лиды переназначены.'});
          this.errors = new ErrorBag();
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Лиды переназначены.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

