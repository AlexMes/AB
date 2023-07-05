<template>
  <div class="flex flex-col items-center">
    <div class="container flex flex-col mx-auto">
      <h1 class="flex text-gray-700">
        Статистика
      </h1>
      <div class="flex flex-col my-6">
        <div class="flex flex-no-wrap items-center justify-between">
          <div
            v-if="$root.user.role !=='verifier'"
            class="flex flex-col w-1/5 mx-2 align-middle"
          >
            <label class="mb-2">Баер</label>
            <mutiselect
              v-model="users"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.users"
              placeholder="Выберите баеров"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="flex flex-col w-1/5 mx-2 align-middle">
            <label class="mb-2">Профиль</label>
            <mutiselect
              v-model="profiles"
              :show-labels="false"
              :multiple="true"
              :close-on-select="false"
              :clear-on-select="false"
              :loading="isProfileFetching"
              track-by="id"
              label="name"
              :limit="1"
              :options="filterSets.profiles"
              placeholder="Выберите профили"
            ></mutiselect>
          </div>
          <div class="flex flex-col w-1/5 mr-2 align-middle">
            <label class="mb-2">Рекламный кабинет</label>
            <mutiselect
              v-model="filters.accounts"
              :show-labels="false"
              :multiple="true"
              :close-on-select="false"
              :limit="1"
              :clear-on-select="false"
              :options="filterSets.accounts"
              :loading="isAccountFetching"
              track-by="id"
              label="name"
              placeholder="Выберите РК"
            ></mutiselect>
          </div>
          <div class="flex flex-col w-1/5 mx-2 align-middle">
            <label class="mb-2">Режим выборки</label>
            <mutiselect
              v-model="filters.mode"
              :show-labels="false"
              :multiple="false"
              :allow-empty="false"
              track-by="id"
              label="label"
              :options="filterSets.modes"
              placeholder="Выберите режим"
              @select="resetStatuses"
            ></mutiselect>
          </div>
          <div class="flex flex-col w-1/5 mx-2 align-middle">
            <label class="mb-2">Период</label>
            <date-picker
              id="datetime"
              v-model="filters.dates"
              class="w-full px-1 py-2 text-gray-600 border rounded"
              :config="pickerConfig"
              placeholder="Выберите даты"
            ></date-picker>
          </div>
          <div class="flex flex-col w-1/5 ml-2">
            <button
              class="mt-2 -mb-3 text-lg button btn-primary"
              :class="{ 'bg-gray-700': isBusy }"
              :disabled="isBusy"
              @click="loadStatistic"
            >
              <span
                v-if="
                  isBusy ||
                    isAccountFetching ||
                    isProfileFetching
                "
              ><fa-icon
                :icon="['far', 'spinner']"
                class="mr-2 fill-current"
                spin
                fixed-width
              ></fa-icon>
                Загрузка</span>
              <span v-else>Применить</span>
            </button>
          </div>
        </div>
        <div class="flex flex-no-wrap mt-3">
          <div class="flex flex-col w-1/5 mx-2 align-middle">
            <label class="mb-2">Разбивка</label>
            <mutiselect
              v-model="groupBy"
              :show-labels="false"
              :mutiple="false"
              :allow-empty="true"
              :options="groupOptions"
              track-by="id"
              label="label"
              placeholder="Выберите разбивку"
            ></mutiselect>
          </div>
          <div class="flex flex-col w-1/5 mx-2 align-middle">
            <label class="mb-2">Статус</label>
            <mutiselect
              v-model="filters.statuses"
              :show-labels="false"
              :multiple="true"
              :close-on-select="false"
              :clear-on-select="false"
              :limit="1"
              :options="statuses"
              placeholder="Выберите статусы"
            ></mutiselect>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="withoutGroups"
      class="w-full mt-5"
    >
      <insights-group
        ref="resultBlock"
        :filters="cleanFilters"
      ></insights-group>
    </div>
    <div
      v-else
      class="w-full mt-5"
    >
      <insights-group
        v-for="(group, index) in groups"
        :key="index"
        ref="resultBlock"
        :group="group"
        :only="groupBy.id"
        :filters="cleanFilters"
        class="w-3/4 mb-8"
      >
      </insights-group>
    </div>
    <insight-budget-modal :mode="filters.mode.id"></insight-budget-modal>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import InsightsGroup from '../../components/insights/insights-group';
import InsightBudgetModal from '../../components/insights/insight-budget-modal';

export default {
  name: 'report-statistics',
  components: {
    InsightBudgetModal,
    InsightsGroup,
    DatePicker,
  },
  data: () => ({
    users: null,
    profiles: null,
    filters: {
      accounts: null,
      mode: { id: 'campaign', label: 'Кампании' },
      statuses: [],
      dates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
        'YYYY-MM-DD',
      )}`,
    },
    groupBy: null,
    groupable: [],
    groupOptions: [
      { id: 'buyer', label: 'Баер' },
      { id: 'profile', label: 'Профиль' },
      { id: 'account', label: 'Аккаунт' },
    ],
    filterSets: {
      profiles: [],
      users: [],
      accounts: [],
      modes: [
        { id: 'account', label: 'Аккаунты' },
        { id: 'campaign', label: 'Кампании' },
        { id: 'adset', label: 'Адсеты' },
        { id: 'ad', label: 'Обьявления' },
      ],
      statuses: {
        account: [],
        adset: [
          'ACTIVE',
          'PAUSED',
          'DELETED',
          'PENDING_REVIEW',
          'DISAPPROVED',
          'PREAPPROVED',
          'PENDING_BILLING_INFO',
          'CAMPAIGN_PAUSED',
          'ARCHIVED',
          'ADSET_PAUSED',
          'IN_PROCESS',
          'WITH_ISSUES',
        ],
        ad: [
          'ACTIVE',
          'PAUSED',
          'DELETED',
          'PENDING_REVIEW',
          'DISAPPROVED',
          'PREAPPROVED',
          'PENDING_BILLING_INFO',
          'CAMPAIGN_PAUSED',
          'ARCHIVED',
          'ADSET_PAUSED',
          'IN_PROCESS',
          'WITH_ISSUES',
        ],
        campaign: [
          'ACTIVE',
          'PAUSED',
          'DELETED',
          'PENDING_REVIEW',
          'DISAPPROVED',
          'PREAPPROVED',
          'PENDING_BILLING_INFO',
          'CAMPAIGN_PAUSED',
          'ARCHIVED',
          'ADSET_PAUSED',
          'IN_PROCESS',
          'WITH_ISSUES',
        ],
      },
    },
    pickerConfig: {
      defaultDates: `${moment().format(
        'YYYY-MM-DD',
      )} to ${moment().format('YYYY-MM-DD')}`,
      mode: 'range',
    },
    results: null,
    isBusy: false,
    isAccountFetching: false,
    isProfileFetching: false,
  }),
  computed: {
    groups() {
      if (this.groupBy && this.groupBy.id === 'buyer') {
        return this.users === null || this.users.length === 0
          ? this.filterSets.users
          : this.users;
      }
      if (this.groupBy && this.groupBy.id === 'profile') {
        return this.profiles === null || this.profiles.length === 0
          ? this.filterSets.profiles
          : this.profiles;
      }
      if (this.groupBy && this.groupBy.id === 'account') {
        return this.filters.accounts === null ||
          this.filter.accounts.length === 0
          ? this.filterSets.accounts
          : this.filters.accounts;
      }

      return null;
    },
    statuses() {
      return this.filterSets.statuses[this.filters.mode.id];
    },
    hasResults() {
      return this.results !== null && this.results !== [];
    },
    withoutGroups() {
      return this.groupBy === null;
    },
    period() {
      const dates = this.filters.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        period: this.period,
        mode: this.filters.mode.id || 'campaigns',
        statuses: this.filters.statuses,
        accounts:
          this.filters.accounts === null
            ? this.filterSets.accounts.map(account => {
              return {
                id: account.id,
                profile_id: account.profile_id,
              };
            })
            : this.filters.accounts.map(account => {
              return {
                id: account.id,
                profile_id: account.profile_id,
              };
            }),
        users:
          this.users === null
            ? null
            : Object.values(this.users).map(user => user.id),
        profiles:
          this.profiles == null
            ? this.filterSets.profiles.map(profile => profile.id)
            : this.profiles.map(profile => profile.id),
      };
    },
  },
  watch: {
    users() {
      this.fetchProfiles();
    },
    profiles() {
      this.fetchAccounts();
    },
  },
  created() {
    axios
      .get('/api/users', { params: { all: true } })
      .then(response =>
        Promise.all((this.filterSets.users = response.data)),
      )
      .then(r => this.fetchProfiles())
      .catch(err => console.error);
  },
  methods: {
    fetchProfiles() {
      this.isProfileFetching = true;
      axios
        .get('/api/profiles', {
          params: {
            all: true,
            active: true,
            users: this.cleanFilters.users,
          },
        })
        .then(response => {
          return Promise.all(
            (this.filterSets.profiles = response.data),
          );
        })
        .then(r => {
          this.fetchAccounts();
        })
        .catch(err => console.error)
        .finally(() => {
          this.isProfileFetching = false;
        });
    },
    fetchAccounts() {
      this.isAccountFetching = true;
      axios
        .get('/api/accounts', {
          params: {
            users: this.cleanFilters.users,
            profiles: this.cleanFilters.profiles,
            all: true,
            active: true,
          },
        })
        .then(response => (this.filterSets.accounts = response.data))
        .finally(() => {
          this.isAccountFetching = false;
        });
    },
    resetStatuses() {
      this.filters.statuses = [];
    },
    loadStatistic() {
      if (this.$refs.resultBlock.length > 1) {
        this.$refs.resultBlock.forEach(result => result.load());
        return;
      }
      this.$refs.resultBlock.load();
    },
  },
};
</script>

<style scoped>
label {
    @apply text-gray-700;
    @apply font-semibold;
}
</style>
