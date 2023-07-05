<template>
  <div class="bg-white shadow">
    <div
      v-for="user in users"
      class="flex px-4 py-3 justify-between border-b"
    >
      <div class="flex items-center w-1/3">
        <img
          :src="user.image"
          class="w-10 h-10 rounded-full mr-3"
          alt="FB profile image"
        />
        <span v-text="user.id"></span>
      </div>
      <div class="flex items-center text-left w-1/3">
        <span v-text="user.role"></span>
      </div>
      <div></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'app-users',
  data:() => ({
    users:[],
  }),
  created() {
    axios.get('/diagnostics/application/roles')
      .then(response => this.users = response.data)
      .catch(error => this.$toast.error({
        title: 'Users cant be loaded',
        message: error.response.message,
      }));
  },
};
</script>
