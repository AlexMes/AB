<template>
  <div class="flex flex-col flex-shrink-0 bg-white rounded shadow mb-8">
    <div class="flex justify-between items-center p-3 flex-col md:flex-row">
      <div
        class="flex items-center flex-wrap justify-between md:justify-end w-full"
      >
        <date-picker
          id="datetime"
          v-model="date"
          class="w-40 px-3 py-2 pr-4 mx-2 border border-gray-400 rounded text-gray-700"
          placeholder="Дата"
          :config="pickerConfig"
        ></date-picker>
        <select
          v-if="$root.isAdmin && accounts.length > 1"
          v-model="account"
          name="qs-user"
          class="w-40 justify-end mt-4 md:mt-0 px-3 py-2 pr-4 text-gray-700"
        >
          <option :value="null">
            Все
          </option>
          <option
            v-for="account in accounts"
            :key="account.id"
            :value="account.account_id"
            v-text="account.name"
          ></option>
        </select>
      </div>
    </div>
    <div class="overflow-x-auto overflow-y-hidden flex w-full">
      <div
        v-if="hasFails"
        class="flex flex-col w-full"
      >
        <div
          class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold"
        >
          <div class="w-3/12">
            Date
          </div>
          <div class="w-4/12">
            Account
          </div>
          <div class="w-3/12">
            Buyer
          </div>
          <div class="w-2/12">
            Card
          </div>
        </div>
        <payment-fails-list-item
          v-for="fail in fails"
          :key="fail.account_id"
          :fail="fail"
        ></payment-fails-list-item>
      </div>
      <div
        v-else
        class="w-full flex border-t justify-center bg-white p-4 font-medium text-xl text-gray-700"
      >
        <span v-if="isBusy">
          <fa-icon
            :icon="['far', 'spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon>Загрузка
        </span>
        <span
          v-else
          class="flex text-center"
        >
          Нет логов
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';

export default {
  name: 'logs-payment-fails',
  components: {
    DatePicker,
  },
  data: () => ({
    fails: [],
    isBusy: false,
    accounts: [],
    account: null,
    pickerConfig: {},
    date: moment().format('YYYY-MM-DD'),
  }),
  computed: {
    hasFails() {
      return this.fails.length > 0;
    },
  },
  watch: {
    account() {
      this.load();
    },
    date() {
      this.load();
    },
  },
  created() {
    this.load();
    if (this.$root.isAdmin) {
      axios
        .get('/api/accounts')
        .then(response => (this.accounts = response.data.data))
        .catch(error => console.error);
    }
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/payment-fails', {
          params: {
            account: this.account,
            user: this.user,
            date: this.date,
          },
        })
        .then(response => (this.fails = response.data))
        .catch(error => console.error)
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
<style>
th {
    @apply .text-left;
    @apply px-2;
    @apply py-3;
    @apply whitespace-no-wrap;
}
td {
    @apply py-4;
    @apply px-2;
    @apply border-b;
    @apply whitespace-no-wrap;
}
</style>
