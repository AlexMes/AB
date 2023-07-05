<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <mutiselect
            v-model="user"
            :show-labels="false"
            :options="allUsers"
            placeholder="Выберите баера"
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
      v-if="hasUsers"
    >
      <div class="shadow">
        <shared-user-list-item
          v-for="user in users"
          :key="user.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :user="user"
          @deleted="remove(user)"
        ></shared-user-list-item>
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
import SharedUserListItem from '../../../components/users/shared-user-list-item';
export default {
  name: 'users-shared-users',
  components: {SharedUserListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    user: null,
    users: [],
    allUsers: [],
  }),
  computed:{
    hasUsers() {
      return this.users.length > 0;
    },
  },
  created() {
    this.load();
    this.getAllUsers();
  },
  methods: {
    load() {
      axios
        .get(`/api/users/${this.id}/shared-users`)
        .then(response => this.users = response.data)
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load shared users.',
          }),
        );
    },
    add() {
      if (this.user !== null) {
        axios
          .post(`/api/users/${this.id}/shared-users`, {shared_id: this.user.id})
          .then(response => {
            if (this.users.findIndex(user => user.id === response.data.id) === -1) {
              this.users.push(response.data);
            }
            this.user = null;
            this.$toast.success({title: 'Ok', message: 'User was added successfully.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to add user.', message: err.response.data.message}));
      }
    },
    remove(user) {
      axios.delete(`/api/users/${this.id}/shared-users/${user.id}`)
        .then(response => {
          const index = this.users.findIndex(user => user.id === response.data.id);
          if (index !== -1) {
            this.users.splice(index, 1);
          }

          this.$toast.success({title: 'Ok', message: 'User was detached successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to detach user.', message: err.response.data.message}));
    },
    getAllUsers() {
      axios
        .get('/api/users', {
          params: {
            all: true,
            userRole: 'buyer',
          },
        })
        .then(response => this.allUsers = response.data)
        .catch(err => this.$toast.error({title: 'Unable to load all users.', message: err.response.data.message}));
    },
  },
};
</script>
