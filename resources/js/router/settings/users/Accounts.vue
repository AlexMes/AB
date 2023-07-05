<template>
  <div class="w-full">
    <div v-if="hasAccounts">
      <accounts-list-item
        v-for="account in accounts"
        :key="account.id"
        :account="account"
        @update="swapAccount"
      >
      </accounts-list-item>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
      <account-comment-modal></account-comment-modal>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Кабинетов не найдено</p>
    </div>
  </div>
</template>


<script>
import {set} from 'vue';

export default {
  name: 'users-accounts',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    accounts: [],
    response: null,
  }),
  computed:{
    hasAccounts() {
      return this.accounts.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.id}/accounts`, {params: {page}})
        .then(({data}) => {this.response = data; this.accounts = data.data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить кабинеты.',
            message: e.response.data.message,
          }),
        );
    },
    swapAccount(event) {
      const index = this.accounts.findIndex(
        account => account.id === event.account.id,
      );

      if (index !== -1) {
        set(this.accounts, index, event.account);
      }
    },
  },
};
</script>
