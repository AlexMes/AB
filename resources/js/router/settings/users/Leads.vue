<template>
  <div class="w-full">
    <div v-if="hasLeads">
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative">
          <tbody class="w-full">
            <lead-list-item
              v-for="lead in leads"
              :key="lead.id"
              :lead="lead"
            >
            </lead-list-item>
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
      <p>Лидов не найдено</p>
    </div>
  </div>
</template>


<script>
export default {
  name: 'users-leads',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    leads: [],
    response: null,
  }),
  computed:{
    hasLeads() {
      return this.leads.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.id}/leads`, {params: {page}})
        .then(({data}) => {this.response = data; this.leads = data.data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить лиды .',
            message: e.response.data.message,
          }),
        );
    },
  },
};
</script>
