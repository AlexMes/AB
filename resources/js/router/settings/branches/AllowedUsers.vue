<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <mutiselect
            v-model="user"
            :show-labels="false"
            :options="allUsers"
            placeholder="Выберите пользователя"
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
        <allowed-user-list-item
          v-for="allowed in allowedBranches"
          :key="allowed.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :user="allowed.user"
          @deleted="remove(allowed)"
        ></allowed-user-list-item>
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
import AllowedUserListItem from '../../../components/settings/allowed-user-list-item';
export default {
  name: 'branches-allowed-users',
  components: {AllowedUserListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    user: null,
    allowedBranches: [],
    allUsers: [],
  }),
  computed:{
    hasAllowedBranches() {
      return this.allowedBranches.length > 0;
    },
  },
  created() {
    this.load();
    this.getAllUsers();
  },
  methods: {
    load() {
      axios
        .get('/api/allowed-branches', {params: {branch_id: this.id}})
        .then(response => this.allowedBranches = response.data)
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load allowed users.',
          }),
        );
    },
    add() {
      if (this.user !== null) {
        axios
          .post('/api/allowed-branches', {user_id: this.user.id, branch_id: this.id})
          .then(response => {
            if (this.allowedBranches.findIndex(item => item.id === response.data.id) === -1) {
              this.allowedBranches.push(response.data);
            }
            this.user = null;
            this.$toast.success({title: 'Ok', message: 'User was added successfully.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to add user.', message: err.response.data.message}));
      }
    },
    remove(allowed) {
      axios.delete(`/api/allowed-branches/${allowed.id}`)
        .then(response => {
          const index = this.allowedBranches.findIndex(item => item.id === allowed.id);
          if (index !== -1) {
            this.allowedBranches.splice(index, 1);
          }

          this.$toast.success({title: 'Ok', message: 'User was detached successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to detach user.', message: err.response.data.message}));
    },
    getAllUsers() {
      axios
        .get('/api/users', {params: {all: true}})
        .then(response => this.allUsers = response.data)
        .catch(err => this.$toast.error({title: 'Unable to load all users.', message: err.response.data.message}));
    },
  },
};
</script>
