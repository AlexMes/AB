<template>
  <div class="container mx-auto flex flex-col">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Аккаунты
      </h1>
      <div class="flex">
        <search-field
          class="w-full"
          @search="search"
        ></search-field>
      </div>
    </div>
    <div class="flex flex-shrink-0">
      <div
        class="relative flex flex-col align-middlemx-2 m-2 md:my-0 w-1/6"
      >
        <label
          for="datetime"
          class="mb-2"
        >Дата добавления:</label>
        <date-picker
          id="datetime"
          v-model="dates"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите даты"
        ></date-picker>
        <fa-icon
          v-if="dates"
          class="cursor-pointer absolute right-0 top-2"
          :icon="['far', 'times-circle']"
          @click="clearPeriod"
        ></fa-icon>
      </div>
      <div class="flex flex-col mr-4 my-2 md:my-0">
        <label
          for="offerFilter"
          class="mb-2"
        >Группа</label>
        <multiselect
          id="offerFilter"
          v-model="group"
          :options="groups"
          :show-labels="false"
          :multiple="true"
          label="name"
          placeholder="Выберите группу"
          track-by="id"
        ></multiselect>
      </div>
    </div>
    <div
      v-if="hasAccounts"
      class="flex flex-col w-full mt-6"
    >
      <div
        class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold"
      >
        <div class="w-1/4">
          Название
        </div>
        <div class="w-1/5">
          Профиль
        </div>
        <div class="w-40">
          Возраст / Бан
        </div>
        <div class="w-1/6">
          Статус
        </div>
        <div class="w-1/3 flex justify-between">
          <div>Расходы</div>
          <div>Баланс</div>
          <div>Spend</div>
          <div>CPL</div>
        </div>
        <div class="w-1/12"></div>
      </div>
      <accounts-list-item
        v-for="account in accounts"
        :key="account.id"
        :account="account"
        @update="swapAccount"
      ></accounts-list-item>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <account-comment-modal></account-comment-modal>
  </div>
</template>

<script>
import { set } from 'vue';
import Pagination from '../../components/pagination';
import SearchField from '../../components/SearchField';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';

export default {
  name: 'accounts-index',
  components: { SearchField, Pagination, DatePicker, Multiselect },
  data: () => ({
    accounts: [],
    groups: [],
    group: [],
    response: null,
    needle: null,
    dates: null,
    pickerConfig: {
      mode: 'range',
    },
  }),
  computed: {
    period() {
      if (this.dates) {
        const dates = this.dates.split(' ');
        return {
          since: dates[0],
          until: dates[2] || dates[0],
        };
      }
      return {
        since: null,
        until: null,
      };
    },
    hasAccounts() {
      return !this.accounts.isEmpty;
    },
  },
  watch: {
    period() {
      this.load();
    },
    group() {
      this.load();
    },
  },
  created() {
    this.load();
    this.loadGroups();
    this.listen();
  },
  methods: {
    load(page = 1) {
      axios
        .get('/api/accounts', {
          params: {
            search: this.needle,
            page: page,
            since: this.period.since,
            until: this.period.until,
            group:
              this.group === []
                ? []
                : this.group.map(group => group.id),
          },
        })
        .then(response => {
          this.response = response.data;
          this.accounts = response.data.data;
        })
        .catch(error => {
          this.$toast.error({
            title: 'Не удалось загрузить аккаунты.',
            message: error.response.data.message,
          });
        });
    },
    loadGroups() {
      axios
        .get('/api/groups')
        .then(response => {
          this.groups = response.data.data;
        })
        .catch(error => {
          this.$toast.error({
            title: 'Не удалось загрузить группы.',
            message: error.response.data.message,
          });
        });
    },
    search(needle) {
      this.needle = needle;
      this.load();
    },
    listen() {
      Echo.private(`App.User.${this.$root.$refs.auth.user.id}`).listen(
        '.Accounts.Created',
        event => {
          this.accounts.push(event.account);
        },
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
    clearPeriod() {
      this.dates = null;
    },
  },
};
</script>
