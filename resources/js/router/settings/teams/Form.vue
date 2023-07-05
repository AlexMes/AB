<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="team.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новая команда
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
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
                v-model="team.name"
                type="text"
                placeholder="Dream team"
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
            for="branch_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Филиал
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="branch_id"
                v-model="team.branch_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('branch_id')"
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
  name: 'teams-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    team: {
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
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      if (this.isUpdating) {
        this.load();
      }
      this.loadBranches();
      this.team.branch_id = this.$route.params.branchId || null;
    },
    load() {
      axios.get(`/api/teams/${this.id}`)
        .then(r => this.team = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить команду.', message: err.response.data.message});
        });
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/teams/',this.team)
        .then(r => {
          if (!!this.$route.params.branchId) {
            this.$router.back();
            return;
          }

          this.$router.push({name:'teams.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить команду.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/teams/${this.team.id}`,this.team)
        .then(r => this.$router.push({name:'teams.users',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить команду.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    cancel() {
      if (!!this.$route.params.branchId) {
        this.$router.back();
        return;
      }

      if (this.isUpdating) {
        this.$router.push({name:'teams.users', params:{id: this.id}});
      } else {
        this.$router.push({name:'teams.index'});
      }
    },
    loadBranches() {
      axios.get('/api/branches')
        .then(r => this.branches = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить филиалы.', message: err.response.data.message}));
    },
  },
};
</script>
