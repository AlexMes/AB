<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="group.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новая группа
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label
            for="name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="name"
                v-model="group.name"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('name')"
              />
            </div>
            <span
              v-if="errors.has('name')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('name')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="branch"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Филиал
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="branch"
                v-model="group.branch_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
              >
                <option :value="null">
                  Не выбрано
                </option>
                <option
                  v-for="branch in branches"
                  :key="branch.id"
                  :value="branch.id"
                  v-text="branch.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('branch_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('branch_id')"
            ></span>
          </div>
        </div>

        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="save"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';
export default {
  name: 'office-groups-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    group: {
      name: null,
      branch_id: null,
    },
    branches: [],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
    cleanForm() {
      return {
        name: this.group.name,
        branch_id: this.group.branch_id,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      this.loadBranches();
      if (this.isUpdating) {
        this.load();
      }
    },

    load() {
      axios.get(`/api/office-groups/${this.id}`)
        .then(r => {
          this.group = r.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить группу.', message: err.response.data.message});
        });
    },

    loadBranches() {
      axios
        .get('/api/branches')
        .then(({ data }) => (this.branches = data))
        .catch(err => this.$toast.error({title: 'Unable to load branches.', message: err.response.statusText}));
    },

    save() {
      this.isUpdating ? this.update() : this.create();
    },

    create() {
      this.isBusy = true;
      axios.post('/api/office-groups/', this.cleanForm)
        .then(r => {
          this.$router.push({name:'office-groups.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить группу.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    update() {
      this.isBusy = true;
      axios.put(`/api/office-groups/${this.id}`, this.cleanForm)
        .then(r => this.$router.push({name:'office-groups.offices',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить группу.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    cancel() {
      if (this.isUpdating) {
        this.$router.push({name:'office-groups.offices', params:{id: this.id}});
      } else {
        this.$router.push({name:'office-groups.index'});
      }
    },
  },
};
</script>
