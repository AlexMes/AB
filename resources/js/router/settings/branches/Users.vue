<template>
  <div class="w-full">
    <div
      v-if="hasUsers"
    >
      <div class="shadow">
        <user-list-item
          v-for="user in users"
          :key="user.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :user="user"
        ></user-list-item>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6"
    >
      <p>Команд не найдено</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'branches-users',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    users: [],
    response: null,
  }),
  computed:{
    hasUsers() {
      return this.users.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/branches/${this.id}/users`, {params: {page}})
        .then(response => {
          this.users = response.data.data;
          this.response = response.data;
        })
        .catch(err => this.$toast.error({title: 'Error', message: 'Unable to load users'}));
    },
  },
};
</script>
