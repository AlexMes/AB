<template>
  <modal
    name="delete-office-manager-modal"
    height="auto"
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
        <label class="flex w-full mb-2 font-semibold">Распределить лиды:</label>
        <select
          v-model="assignType"
          class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          @change="errors.clear('assign_type')"
        >
          <option
            v-for="type in assignTypes"
            :key="type.id"
            :value="type.id"
            v-text="type.name"
          ></option>
        </select>
        <span
          v-if="errors.has('assign_type')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('assign_type')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">
          <span v-if="assignType==='onlyManagers'">На менеджеров</span>
          <span v-else>Кроме менеджеров</span>
        </label>
        <select
          v-model="assignManagers"
          class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
          multiple
          @change="errors.clear('managers')"
        >
          <option
            v-for="manager in managers"
            :key="manager.id"
            :value="manager.id"
            v-text="manager.name"
          ></option>
        </select>
        <span
          v-if="errors.has('managers')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('managers')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="deleteManager"
        >
          Удалить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('delete-office-manager-modal')"
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
  name: 'delete-office-manager-modal',
  props: {
    officeId: {
      type: [String, Number],
      required: true,
    },
  },
  data: () => ({
    isBusy: false,
    manager: {
      id: null,
      name: null,
      email: null,
      spreadsheet_id: null,
    },
    officeManagers: [],
    assignManagers: [],
    assignType: 'onlyManagers',
    assignTypes: [
      {id: 'onlyManagers', name: 'На менеджеров'},
      {id: 'excludeManagers', name: 'По офису'},
    ],
    errors: new ErrorBag(),
  }),
  computed: {
    managers() {
      return this.officeManagers.filter(m => m.id !== this.manager.id);
    },
    hasManagers() {
      return this.managers.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.manager = event.params.manager;
      if (!this.hasManagers) {
        this.loadManagers();
      }
    },
    loadManagers() {
      axios.get(`/api/offices/${this.officeId}/managers`)
        .then(({data}) => this.officeManagers = data)
        .catch(error => this.$toast.error({title: 'Could not load managers.', message: error.response.data.message}));
    },
    deleteManager() {
      this.isBusy = true;
      axios
        .delete(`/api/offices/${this.officeId}/managers/${this.manager.id}`, {
          params: {
            assign_type: this.assignType,
            managers: this.assignManagers,
          },
        })
        .then(response => {
          this.$toast.success({title: 'Success', message: 'Manager has been deleted.'});
          this.$modal.hide('delete-office-manager-modal');
        })
        .catch(error => {
          this.errors.fromResponse(error);
          this.$toast.error({
            title: 'Error',
            message: 'Cant delete manager now',
          });
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

