<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
          <mutiselect
            v-model="user"
            :show-labels="false"
            :options="allUsers"
            placeholder="Выберите пользователя"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>
        <button
          class="ml-4 w-auto cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
          @click="add"
        >
          <fa-icon
            :icon="['far', 'plus']"
            class="-ml-1 mr-2 h-5 w-5 text-gray-400"
            fixed-width
          ></fa-icon>
          <span>
            Добавить
          </span>
        </button>
      </div>
    </div>
    <div
      v-if="hasUsers"
    >
      <div class="shadow">
        <user-list-item
          v-for="user in users"
          :key="user.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :user="user"
          @deleted="remove(user)"
        ></user-list-item>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Пользователей не найдено</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'teams-users',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    users: [],
    response: null,
    user: null,
    allUsers: [],
  }),
  computed:{
    hasUsers() {
      return this.users.length > 0;
    },
  },
  created() {
    this.load();
    this.loadAllUsers();
  },
  methods: {
    load(page = 1) {
      axios
        .get(`/api/teams/${this.id}/users`, {params: {page, paginate: true}})
        .then(response => {
          this.users = response.data.data;
          this.response = response.data;
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load users',
          }),
        );
    },
    loadAllUsers() {
      axios
        .get('/api/users', {params: {all: true}})
        .then(response => this.allUsers = response.data)
        .catch(err => this.$toast.error({title: 'Unable to load all users.', message: err.response.data.message}));
    },
    add() {
      if (this.user !== null) {
        axios.post(`/api/teams/${this.id}/users`, {user_id: this.user.id})
          .then(r => {
            if (this.users.findIndex(user => user.id === r.data.id) === -1) {
              this.users.push(r.data);
            }

            this.user = null;
            this.$toast.success({title: 'Ok', message: 'The user was attached successfully.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to attach the user.', message: err.response.data.message}));
      }
    },
    remove(user) {
      axios.delete(`/api/teams/${this.id}/users/${user.id}`)
        .then(r => {
          const index = this.users.findIndex(u => u.id === user.id);
          if (index !== -1) {
            this.users.splice(index, 1);
          }

          this.$toast.success({title: 'Ok', message: 'The user was detached successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to detach the user.', message: err.response.data.message}));
    },
  },
};
</script>
