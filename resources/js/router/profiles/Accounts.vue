<template>
  <div>
    <div v-if="hasAccounts">
      <div class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold">
        <div class="w-1/3">
          Название
        </div>
        <div class="w-40">
          Возраст / Бан
        </div>
        <div class="w-1/6">
          Статус
        </div>
        <div class="w-1/5 flex justify-between">
          <div>Расходы</div>
          <div>Баланс</div>
        </div>
        <div class="w-1/12"></div>
      </div>
      <accounts-list-item
        v-for="account in accounts"
        :key="account.id"
        :account="account"
        @update="update"
      ></accounts-list-item>
    </div>
    <div
      v-else
      class="text-center"
    >
      <h1 class="text-gray-700">
        Рекламных аккаунтов не найдено
      </h1>
    </div>
    <account-comment-modal></account-comment-modal>
  </div>
</template>

<script>
export default {
  name: 'ads-accounts',
  props:{
    id:{
      type: [Number, String],
      required: true,
      default: null,
    },
  },
  data:() => ({
    accounts: [],
    response: null,
  }),
  computed:{
    hasAccounts(){
      return this.accounts.length > 0;
    },
  },
  created() {
    this.load();
    this.listen();
  },
  methods:{
    load(){
      axios.get(`/api/profiles/${this.id}/accounts`)
        .then(response => {
          this.accounts = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить аккаунты.', message: err.response.data.message});
        });
    },
    update(event) {
      const index = this.accounts.findIndex(account => account.id === event.account.id);

      if(index !== -1) {
        set(this.accounts, index, event.account);
      }
    },
    listen() {
      Echo.private(`App.Profile.${this.id}`)
        .listen('Account.Created', event => {
          this.accounts.push(event.account);
        });
    },
  },
};
</script>


