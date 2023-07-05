<template>
  <div class="w-full">
    <div class="shadow">
      <div
        class="flex justify-end w-full p-3 space-x-2 bg-white border-b"
      >
        <div class="w-1/4">
          <mutiselect
            v-model="form.branch"
            :show-labels="false"
            :options="allBranches"
            placeholder="Выберите филиал"
            track-by="id"
            label="name"
            @input="errors.clear('branch_id')"
          ></mutiselect>
          <span
            v-if="errors.has('branch_id')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('branch_id')"
          ></span>
        </div>
        <div class="lg:w-1/6 w-1/4">
          <div class="max-w-lg rounded-md shadow-sm">
            <input
              v-model="form.time"
              class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              type="time"
              @input="errors.clear('time')"
            />
          </div>
          <span
            v-if="errors.has('time')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('time')"
          ></span>
        </div>
        <div class="lg:w-1/6 w-1/4">
          <div class="max-w-lg rounded-md shadow-sm">
            <input
              v-model="form.duration"
              class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              type="number"
              @input="errors.clear('duration')"
            />
          </div>
          <span
            v-if="errors.has('duration')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('duration')"
          ></span>
        </div>
        <div class="flex items-center w-auto">
          <button
            class="flex items-center button btn-primary"
            @click="add"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="mr-2 fill-current"
            ></fa-icon>
            Добавить
          </button>
        </div>
      </div>
    </div>
    <div v-if="hasBranches">
      <div
        class="flex w-full overflow-x-auto overflow-y-hidden bg-white shadow no-last-border"
      >
        <table class="relative table w-full table-auto">
          <thead
            class="sticky w-full font-semibold text-gray-700 uppercase bg-gray-300"
          >
            <tr>
              <th class="w-4/12 px-2 px-6 py-3">
                Филиал
              </th>
              <th class="w-4/12 px-6 py-3">
                Время
              </th>
              <th class="w-4/12 px-6 py-3">
                В течении(мин.)
              </th>
              <th class="w-1/12 px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="w-full">
            <morning-branch-list-item
              v-for="(branch, index) in branches"
              :key="index"
              :branch="branch"
              @deleted="remove"
            ></morning-branch-list-item>
          </tbody>
        </table>
      </div>
    </div>
    <div
      v-else
      class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6"
    >
      <p>Пусто</p>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';
import MorningBranchListItem from '../../../components/settings/morning-branch-list-item';
export default {
  name: 'offices-morning-branches',
  components: {MorningBranchListItem },
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    branches: [],
    allBranches: [],
    form: {
      branch: null,
      time: null,
      duration: 60,
    },
    errors: new ErrorBag(),
  }),
  computed: {
    hasBranches() {
      return this.branches.length > 0;
    },
  },
  created() {
    this.load();
    this.loadAllBranches();
  },
  methods: {
    load() {
      axios
        .get(`/api/offices/${this.id}/morning-branches`)
        .then(r => (this.branches = r.data))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить филиалы утренней выдачи .',
            message: e.response.data.message,
          }),
        );
    },
    loadAllBranches() {
      axios
        .get('/api/branches')
        .then(({ data }) => (this.allBranches = data))
        .catch(err =>
          this.$toast.error({
            title: 'Не удалось загрузить филиалы.',
            message: err.response.data.message,
          }),
        );
    },
    add() {
      if (this.country !== null) {
        axios
          .post(`/api/offices/${this.id}/morning-branches`, {
            branch_id: !this.form.branch ? null : this.form.branch.id,
            time: this.form.time,
            duration: this.form.duration,
          })
          .then(r => {
            if (this.branches.findIndex(branch => branch.id === r.data.id) === -1) {
              this.branches.push(r.data);
            }
            this.form.branch = null;
            this.form.time = null;
            this.form.duration = 60;
          })
          .catch(err => {
            if (err.response.status === 422) {
              return this.errors.fromResponse(err);
            }
            this.$toast.error({
              title: 'Не удалось добавить филиал в утреннюю выдачу.',
              message: err.response.data.message,
            });
          });
      }
    },
    remove(event) {
      const index = this.branches.findIndex(
        branch => branch.id === event.branch.id,
      );
      if (index !== -1) {
        this.branches.splice(index, 1);
      }
    },
  },
};
</script>
