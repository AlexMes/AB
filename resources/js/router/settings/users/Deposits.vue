<template>
  <div class="w-full">
    <div v-if="hasDeposits">
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative">
          <tbody class="w-full">
            <deposit-list-item
              v-for="deposit in deposits"
              :key="deposit.id"
              class="bg-white hover:bg-gray-100 text-black font-normal normal-case"
              :deposit="deposit"
            >
            </deposit-list-item>
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
      <p>Депозитов не найдено</p>
    </div>
  </div>
</template>


<script>
export default {
  name: 'users-deposits',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    deposits: [],
    response: null,
  }),
  computed:{
    hasDeposits() {
      return this.deposits.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.id}/deposits`, {params: {page}})
        .then(({data}) => {this.response = data; this.deposits = data.data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить депозиты.',
            message: e.response.data.message,
          }),
        );
    },
  },
};
</script>
