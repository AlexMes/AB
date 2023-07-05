<template>
  <modal
    name="transfer-route-modal"
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
        <label class="flex w-full mb-2 font-semibold">Оффис</label>
        <mutiselect
          v-model="targetOffice"
          :show-labels="false"
          :multiple="false"
          :options="offices"
          placeholder="Выберите оффис"
          track-by="id"
          label="name"
          @input="errors.clear('office_id')"
          @select="load"
          @remove="load(null)"
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
          @click="$modal.hide('transfer-route-modal')"
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
  name: 'transfer-route-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    route: {},
    targetManager: null,
    targetOffice: null,
    managers: [],
    offices: [],
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
      this.route = event.params.route;
      if (!this.hasManagers) {
        this.load();
      }
      this.loadOffices();
    },
    load(office) {
      this.isLoading = true;
      axios.get(`/api/offices/${office ? office.id : this.order.office_id}/managers`)
        .then(response => {
          this.managers = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить менеджеров оффиса.', message: e.data.message}))
        .finally(() => this.isLoading = false);
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(response => {
          this.offices = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить оффисы.', message: e.data.message}));
    },
    transfer() {
      this.isBusy = true;
      axios.post(`/api/leads-order-routes/${this.route.id}/transfer`, {
        manager_id: this.targetManager === null ? null : this.targetManager.id,
      })
        .then(response => {
          this.$modal.hide('transfer-route-modal');
          this.$toast.success({title: 'OK', message: 'Лиды переназначены.'});
          this.errors = new ErrorBag();
          this.$eventHub.$emit('route-transferred', this.route, response.data);
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось передать лиды.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

