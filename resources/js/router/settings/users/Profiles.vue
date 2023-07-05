<template>
  <div class="w-full">
    <div
      v-if="hasProfiles"
    >
      <div class="shadow">
        <profiles-list-item
          v-for="profile in profiles"
          :key="profile.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :profile="profile"
        ></profiles-list-item>
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
      <p>Нет прикрепленных профилей</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'users-profiles',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    profiles: [],
    response: null,
  }),
  computed:{
    hasProfiles() {
      return this.profiles.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.id}/profiles`, {params:{page:page}})
        .then(({data}) => {this.profiles = data.data; this.response = data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить профили.',
            message: e.response.data.message,
          }),
        );
    },
  },
};
</script>
