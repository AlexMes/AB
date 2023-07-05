<template>
  <div class="w-full">
    <div
      v-if="hasAccesses"
    >
      <div class="shadow">
        <accesses-list-item
          v-for="access in accesses"
          :key="access.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :access="access"
        ></accesses-list-item>
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
      <p>Нет прикрепленных доступов</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'users-accesses',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    accesses: [],
    response: null,
  }),
  computed:{
    hasAccesses() {
      return this.accesses.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.id}/accesses`, {params:{page:page}})
        .then(({ data }) => {this.accesses = data.data; this.response = data; })
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить доступы.',
            message: e.response.data.message,
          }),
        );
    },
  },
};
</script>
