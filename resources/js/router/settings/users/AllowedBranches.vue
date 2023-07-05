<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <mutiselect
            v-model="branch"
            :show-labels="false"
            :options="allBranches"
            placeholder="Выберите филиал"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>
        <div class="w-auto flex items-center">
          <button
            class="button btn-primary flex items-center ml-3"
            @click="add"
          >
            <fa-icon
              :icon="['far','plus']"
              class="fill-current mr-2"
            ></fa-icon> Добавить
          </button>
        </div>
      </div>
    </div>
    <div
      v-if="hasAllowedBranches"
    >
      <div class="shadow">
        <allowed-branch-list-item
          v-for="allowed in allowedBranches"
          :key="allowed.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :branch="allowed.branch"
          @deleted="remove(allowed)"
        ></allowed-branch-list-item>
      </div>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Пусто</p>
    </div>
  </div>
</template>

<script>
import AllowedBranchListItem from '../../../components/users/allowed-branch-list-item';
export default {
  name: 'users-allowed-branches',
  components: {AllowedBranchListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    branch: null,
    allowedBranches: [],
    allBranches: [],
  }),
  computed:{
    hasAllowedBranches() {
      return this.allowedBranches.length > 0;
    },
  },
  created() {
    this.load();
    this.getAllBranches();
  },
  methods: {
    load() {
      axios
        .get('/api/allowed-branches', {params: {user_id: this.id}})
        .then(response => this.allowedBranches = response.data)
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load allowed branches.',
          }),
        );
    },
    add() {
      if (this.branch !== null) {
        axios
          .post('/api/allowed-branches', {user_id: this.id, branch_id: this.branch.id})
          .then(response => {
            if (this.allowedBranches.findIndex(item => item.id === response.data.id) === -1) {
              this.allowedBranches.push(response.data);
            }
            this.branch = null;
            this.$toast.success({title: 'Ok', message: 'Branch was added successfully.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to add branch.', message: err.response.data.message}));
      }
    },
    remove(allowed) {
      axios.delete(`/api/allowed-branches/${allowed.id}`)
        .then(response => {
          const index = this.allowedBranches.findIndex(item => item.id === allowed.id);
          if (index !== -1) {
            this.allowedBranches.splice(index, 1);
          }

          this.$toast.success({title: 'Ok', message: 'Branch was detached successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to detach branch.', message: err.response.data.message}));
    },
    getAllBranches() {
      axios
        .get('/api/branches')
        .then(response => this.allBranches = response.data)
        .catch(err => this.$toast.error({title: 'Unable to load all branches.', message: err.response.data.message}));
    },
  },
};
</script>
