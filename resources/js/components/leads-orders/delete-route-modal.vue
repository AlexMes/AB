<template>
  <modal
    name="delete-route-modal"
    height="auto"
    :adaptive="true"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div class="mb-4">
        <h3>Удаление роута!</h3>
      </div>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Что сделать с назначениями?</label>
        <select
          v-model="action"
          class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          @change="errors.clear('action')"
        >
          <option
            v-for="action in actions"
            :key="action.id"
            :value="action.id"
            v-text="action.name"
          ></option>
        </select>
        <span
          v-if="errors.has('action')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('action')"
        ></span>
        <span
          v-if="errors.has('date')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('date')"
        ></span>
      </div>
      <div
        v-if="action === 'transfer'"
        class="flex flex-col w-full mb-4"
      >
        <label class="flex w-full mb-2 font-semibold">Менеджер</label>
        <select
          v-model="manager_id"
          class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          @change="errors.clear('manager_id')"
        >
          <option
            v-for="manager in managers"
            :key="manager.id"
            :value="manager.id"
            v-text="manager.name"
          ></option>
        </select>
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
          @click="remove"
        >
          Удалить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('delete-route-modal')"
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
  name: 'delete-route-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    route: {},
    manager_id: null,
    action: null,
    managers: [],
    actions: [
      {id: null, name: 'Обычное удаление'},
      {id: 'leftovers', name: 'Лиды вернуть в остаток'},
      {id: 'delete_leads', name: 'Лиды удалить'},
      {id: 'transfer', name: 'Передать на менеджера'},
      {id: 'distribute', name: 'Распределить между менеджерами в заказе'},
    ],
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
        this.loadManagers();
      }
    },
    loadManagers() {
      this.isLoading = true;
      axios.get(`/api/offices/${this.order.office_id}/managers`)
        .then(response => {
          this.managers = response.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить менеджеров оффиса.', message: e.data.message}))
        .finally(() => this.isLoading = false);
    },
    remove() {
      this.isBusy = true;
      axios
        .delete(`/api/leads-order-routes/${this.route.id}`, {
          data: {
            manager_id: this.manager_id,
            action: this.action,
          },
        })
        .then(r => {
          this.$modal.hide('delete-route-modal');
          this.$toast.success({ title: 'OK', message: 'Gone' });
          this.errors = new ErrorBag();
          this.$eventHub.$emit('lead-order-route-deleted', {route: this.route});
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Can\'t delete route.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

