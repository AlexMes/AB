<template>
  <div class="w-full">
    <div
      v-if="hasDomains"
    >
      <div class="shadow">
        <table class="w-full table table-auto relative shadow">
          <tbody>
            <domain-list-item
              v-for="domain in domains"
              :key="domain.id"
              class="mt-0 border-b shadow-none hover:shadow-none"
              :domain="domain"
            ></domain-list-item>
          </tbody>
        </table>
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
      <p>Нет прикрепленных доменов</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'traffic-sources-domains',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    domains: [],
    response: null,
  }),
  computed:{
    hasDomains() {
      return this.domains.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/traffic-sources/${this.id}/domains`, {params:{page:page}})
        .then(({data}) => {this.domains = data.data; this.response = data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить домены.',
            message: e.response.data.message,
          }),
        );
    },
  },
};
</script>
