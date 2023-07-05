<template>
  <div class="container mx-auto">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-gray-700">
        Депозиты
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
        <router-link
          v-if="isAdmin || isSupport || isSubSupport"
          :to="{ name: 'deposits.create' }"
          class="flex items-center ml-3 button btn-primary"
        >
          <fa-icon
            :icon="['far', 'plus']"
            class="mr-2 fill-current"
          ></fa-icon>
          Добавить
        </router-link>
        <!--         <router-link
                    v-if="isAdmin || isSupport || isSubSupport"
                    :to="{ name: 'deposits.import' }"
                    class="flex items-center ml-3 button btn-primary"
                >
                    <fa-icon
                        :icon="['far', 'plus']"
                        class="mr-2 fill-current"
                    ></fa-icon>
                    Загрузить
                </router-link>-->
        <button
          v-if="isAdmin || isSupport"
          class="flex items-center ml-3 button btn-primary"
          @click="download"
        >
          <fa-icon
            :icon="['far', 'download']"
            class="mr-2 fill-current"
          ></fa-icon>
          Скачать
        </button>
      </div>
    </div>
    <div
      v-if="!isSubSupport"
      class="grid w-full grid-flow-row-dense grid-cols-1 gap-4 pb-6 sm:grid-cols-2 md:grid-cols-4"
    >
      <div class="col-span-1 space-y-2 align-self-start">
        <label class="mb-2">Дата лида</label>
        <date-picker
          v-model="filters.lead_return_date"
          class="w-full px-1 py-2 text-gray-600 border rounded"
          :config="pickerConfig"
          placeholder="Выберите даты"
        ></date-picker>
      </div>
      <div class="col-span-1 space-y-2 align-self-start">
        <label class="mb-2">Дата депозита</label>
        <date-picker
          v-model="filters.date"
          class="w-full px-1 py-2 text-gray-600 border rounded"
          :config="pickerConfig"
          placeholder="Выберите даты"
        ></date-picker>
      </div>
      <div class="col-span-1 space-y-2 align-self-start">
        <label
          for="offerFilter"
          class="mb-2"
        >Оффер</label>
        <mutiselect
          id="offerFilter"
          v-model="filters.offer_id"
          :show-labels="false"
          :multiple="false"
          :options="offers"
          placeholder="Выберите оффер"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="col-span-1 space-y-2 align-self-start">
        <label
          for="utmCampaignFilter"
          class="mb-2"
        >UTM Campaign</label>
        <mutiselect
          id="utmCampaignFilter"
          v-model="filters.utm_campaign"
          :show-labels="false"
          :multiple="false"
          :options="campaigns"
          placeholder="Выберите кампанию"
        ></mutiselect>
      </div>
      <div
        v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
        class="col-span-1 space-y-2 align-self-start"
      >
        <label
          for="officeFilter"
          class="mb-2"
        >Офис</label>
        <mutiselect
          id="officeFilter"
          v-model="filters.office_id"
          :show-labels="false"
          :multiple="false"
          :options="offices"
          placeholder="Выберите офис"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div
        v-if="(isAdmin || isSupport) && branches.length > 0"
        class="col-span-1 space-y-2 align-self-start"
      >
        <label
          for="brachFilter"
          class="mb-2"
        >Филиал</label>
        <mutiselect
          id="brachFilter"
          v-model="filters.branch_id"
          :show-labels="false"
          :multiple="false"
          :options="branches"
          placeholder="Выберите филиал"
          track-by="id"
          label="name"
          @select="getTeams"
        ></mutiselect>
      </div>
      <div
        v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
        class="col-span-1 space-y-2 align-self-start"
      >
        <label
          for="teamsFilter"
          class="mb-2"
        >Команда</label>
        <mutiselect
          id="teamsFilter"
          v-model="filters.team_id"
          :show-labels="false"
          :multiple="false"
          :options="teams"
          placeholder="Выберите команду"
          track-by="id"
          label="name"
          @select="getUsers"
        ></mutiselect>
      </div>
      <div
        v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
        class="col-span-1 space-y-2 align-self-start"
      >
        <label
          for="usersFilter"
          class="mb-2"
        >Баер</label>
        <mutiselect
          id="usersFilter"
          v-model="filters.user_id"
          :show-labels="false"
          :multiple="false"
          :options="users"
          placeholder="Выберите байера"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div
        v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
        class="col-span-1 space-y-2 align-self-start"
      >
        <label
          for="appsFilter"
          class="mb-2"
        >Приложение</label>
        <mutiselect
          id="appsFilter"
          v-model="filters.app"
          :show-labels="false"
          :multiple="false"
          :options="apps"
          placeholder="Выберите приложения"
        ></mutiselect>
      </div>
      <div
        v-if="isAdmin || isSupport || isBranchHead"
        class="col-span-1 space-y-2 align-self-start"
      >
        <label
          for="officeGroupFilter"
          class="mb-2"
        >Группа офисов</label>
        <mutiselect
          id="officeGroupFilter"
          v-model="filters.office_group_id"
          :show-labels="false"
          :multiple="false"
          :options="officeGroups"
          placeholder="Выберите группу"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="col-span-1 space-y-2 align-self-start">
        <button
          type="button"
          class="mt-6 button btn-secondary"
          @click="load(1)"
        >
          <fa-icon
            :icon="['far', 'filter']"
            class="mr-2 fill-current"
            fixed-width
          ></fa-icon>Фильтровать
        </button>
      </div>
    </div>
    <div v-if="hasDeposits">
      <div class="flex w-full overflow-x-auto overflow-y-hidden">
        <table class="relative table w-full table-auto">
          <thead
            class="sticky w-full font-semibold text-gray-700 uppercase bg-gray-300"
          >
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th class="px-2 py-3">
                Регистрация лида
              </th>
              <th class="px-2 py-3">
                Дата лида
              </th>
              <th class="px-2 py-3">
                Дата
              </th>
              <th class="px-2 py-3">
                Клик ID
              </th>
              <th class="px-2 py-3">
                Аккаунт
              </th>
              <th
                v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
                class="px-2 py-3"
              >
                Баер
              </th>
              <th
                v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
                class="px-2 py-3"
              >
                Офис
              </th>
              <th class="px-2 py-3">
                Оффер
              </th>
              <th
                v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
                class="px-2 py-3"
              >
                Телефон
              </th>
              <th
                v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
                class="px-2 py-3"
              >
                Сумма
              </th>
              <th>Updated at:</th>
              <th class="px-2 py-3">
                UTM
              </th>
              <th class="px-2 py-3">
                ID приложения
              </th>
              <th
                v-if="isAdmin || isSupport"
                class="px-2 py-3"
              >
              </th>
            </tr>
          </thead>
          <tbody class="w-full">
            <deposit-list-item
              v-for="deposit in response.data"
              :key="deposit.id"
              class="font-normal text-black normal-case bg-white hover:bg-gray-100"
              :deposit="deposit"
            ></deposit-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
      <div
        v-if="shouldShowQuantity"
        class="flex justify-end mt-8"
      >
        <div>
          <span
            class="font-semibold"
            v-text="`1 - ${current}`"
          ></span>
          <span class="font-weight-normal">из</span>
          <span
            class="font-semibold"
            v-text="total"
          ></span>
        </div>
      </div>
    </div>
    <div
      v-else
      class="p-4 text-center"
    >
      <h2>Депозитов не найдено</h2>
    </div>
    <edit-deposit-modal v-if="hasDeposits"></edit-deposit-modal>
    <delete-deposit-modal v-if="hasDeposits"></delete-deposit-modal>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import SearchField from '../../components/SearchField';
import downloadLink from '../../utilities/helpers/downloadLink';
import EditDepositModal from '../../components/deposits/edit-deposit-modal';
import DeleteDepositModal from '../../components/deposits/delete-deposit-modal';

export default {
  name: 'index',
  components: {
    EditDepositModal,
    DeleteDepositModal,
    SearchField,
    DatePicker,
  },
  data: () => ({
    response: {},
    filters: {
      lead_return_date: null,
      date: null,
      offer_id: null,
      office_id: null,
      branch_id: null,
      team_id: null,
      user_id: null,
      utm_campaign: null,
      app: null,
      office_group_id: null,
    },
    pickerConfig: {
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    offers: [],
    offices: [],
    branches: [],
    teams: [],
    users: [],
    campaigns: [],
    apps: [],
    officeGroups: [],
  }),
  computed: {
    hasDeposits() {
      if (this.response.data) {
        return this.response.data.length > 0;
      }
      return this.response.length > 0;
    },
    leadReturnPeriod() {
      if (this.filters.lead_return_date === null || this.filters.lead_return_date === '' || this.filters.lead_return_date === undefined) {
        return null;
      }

      const dates = this.filters.lead_return_date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    depositPeriod() {
      if (this.filters.date === null || this.filters.date === '' || this.filters.date === undefined) {
        return null;
      }
      const dates = this.filters.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        lead_return_date: this.leadReturnPeriod,
        date: this.depositPeriod,
        offer_id:
          this.filters.offer_id === null
            ? null
            : this.filters.offer_id.id,
        office_id:
          this.filters.office_id === null
            ? null
            : this.filters.office_id.id,
        branch_id:
          this.filters.branch_id === null
            ? null
            : this.filters.branch_id.id,
        team_id:
          this.filters.team_id === null
            ? null
            : this.filters.team_id.id,
        user_id:
          this.filters.user_id === null
            ? null
            : this.filters.user_id.id,
        utm_campaign:
          this.filters.utm_campaign === 'Без utm_campaign'
            ? 'null'
            : this.filters.utm_campaign,
        app: this.filters.app,
        office_group_id:
          this.filters.office_group_id === null
            ? null
            : this.filters.office_group_id.id,
      };
    },
    total() {
      return this.response.total;
    },
    current() {
      return this.response.data.length;
    },
    shouldShowQuantity() {
      return this.response.next_page_url === null;
    },
    isAdmin() {
      return this.$root.user.role === 'admin';
    },
    isTeamLead() {
      return this.$root.user.role === 'teamlead';
    },
    isSupport() {
      return this.$root.user.role === 'support';
    },
    isSubSupport() {
      return this.$root.user.role === 'subsupport';
    },
    isBranchHead(){
      return this.$root.user.role = 'head';
    },
    isSales() {
      return this.$root.user.role === 'sales';
    },
  },
  created() {
    this.load();
    if (!this.isSubSupport) {
      this.getOffers();
      this.getCampaigns();
    }
    if (this.isAdmin || this.isSupport || this.isBranchHead) {
      this.getOffices();
      this.getUsers();
      this.getBranches();
      this.getTeams();
      this.getApps();
      this.getOfficeGroups();
    }
    this.listen();
  },
  methods: {
    load(page = 1) {
      if (this.isSubSupport) {
        return;
      }
      axios
        .get('/api/deposits', {
          params: {
            page: page,
            ...this.cleanFilters,
          },
        })
        .then(response => (this.response = response.data))
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить депозиты.',
            message: err.response.data.message,
          });
        });
    },
    search(needle) {
      axios
        .get('/api/deposits', {
          params: {
            search: needle,
          },
        })
        .then(response => {
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось найти депозиты.',
            message: err.response.data.message,
          });
        });
    },
    getOffers() {
      axios.get('/api/offers').then(r => {
        this.offers = r.data;
        this.offers.unshift({ id: 'null', name: 'Без оффера' });
      });
    },
    getOffices() {
      axios.get('/api/offices').then(r => {
        this.offices = r.data;
        this.offices.unshift({ id: 'null', name: 'Без офиса' });
      });
    },
    getUsers(team = null) {
      axios
        .get('/api/users', {
          params: {
            all: true,
            branch: this.cleanFilters.branch_id,
            team: team === null ? null : team.id,
          },
        })
        .then(r => {
          this.users = r.data;
          this.users.unshift({ id: 'null', name: 'Без байера' });
        });
    },
    getCampaigns() {
      axios
        .get('/api/utm-campaigns', { params: { all: true } })
        .then(r => {
          this.campaigns = r.data;
          this.campaigns.unshift('Без utm_campaign');
        });
    },
    getBranches() {
      axios.get('/api/branches').then(r => {
        this.branches = r.data;
        this.branches.unshift({ id: null, name: 'Все филиалы' });
      });
    },
    getTeams(branch = null) {
      axios
        .get('/api/teams', {
          params: { all: true, branch: branch === null ? null : branch.id},
        })
        .then(r => {
          this.teams = r.data;
          this.teams.unshift({ id: null, name: 'Все команды' });
        });
    },
    getApps() {
      axios
        .get('/api/lead-apps')
        .then(r => this.apps = r.data);
    },
    getOfficeGroups() {
      axios.get('/api/office-groups').then(r => {
        this.officeGroups = r.data;
      });
    },
    download() {
      axios
        .get('/api/exports/deposits', {
          params: this.cleanFilters,
        })
        .then(({ data }) => downloadLink(data, 'deposits.csv'))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось скачать депозиты',
            message: error.response.data.message,
          }),
        );
    },
    listen() {
      this.$eventHub.$on('deposit-updated', event => {
        let index = this.response.data.findIndex(a => a.id === event.deposit.id);
        if (index !== -1) {
          this.$set(this.response.data, index, event.deposit);
        }
      });
    },
  },
};
</script>

<style scoped>
th,
td {
    @apply whitespace-no-wrap;
}
</style>
