<template>
  <div class="flex flex-col">
    <div class="container flex flex-col mx-auto">
      <div
        class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
      >
        <h1 class="flex text-gray-700">
          Выдача лидов менеджерам
        </h1>
        <span>
          <span
            v-if="report.period"
            class="mt-2 font-medium text-gray-600 md:mt-0"
            v-text="
              `( ${report.period.since} - ${report.period.until} )`
            "
          ></span>
        </span>
      </div>
      <div class="grid w-full grid-flow-row-dense grid-cols-1 gap-4 pb-6 my-6 sm:grid-cols-2 md:grid-cols-4">
        <div class="col-span-1 space-y-2 align-self-start">
          <label class="mb-2">Период</label>
          <date-picker
            id="datetime"
            v-model="dates"
            class="w-full px-1 py-2 text-gray-600 border rounded"
            :config="pickerConfig"
            placeholder="Выберите даты"
          ></date-picker>
        </div>

        <div class="col-span-1 space-y-2 align-self-start">
          <label class="mb-2">Офисы</label>
          <mutiselect
            v-model="filterSets.offices"
            :show-labels="false"
            :multiple="true"
            :options="offices"
            placeholder="Выберите офисы"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>

        <div class="col-span-1 space-y-2 align-self-start">
          <label class="mb-2">Группы офисов</label>
          <mutiselect
            v-model="filterSets.officeGroups"
            :show-labels="false"
            :multiple="true"
            :options="officeGroups"
            placeholder="Выберите группы"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>

        <div class="col-span-1 space-y-2 align-self-start">
          <label class="mb-2">Оферы</label>
          <mutiselect
            v-model="filterSets.offers"
            :show-labels="false"
            :multiple="true"
            :options="offers"
            placeholder="Выберите оферы"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>

        <div class="col-span-1 space-y-2 align-self-start">
          <label class="mb-2">Менеджеры</label>
          <mutiselect
            v-model="filterSets.managers"
            :show-labels="false"
            :multiple="true"
            :options="managers"
            placeholder="Выберите менеджеров"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>

        <div
          v-if="isAdmin || isSupport"
          class="col-span-1 space-y-2 align-self-start"
        >
          <label
            for="brachFilter"
            class="mb-2"
          >Филиал</label>
          <mutiselect
            id="brachFilter"
            v-model="filterSets.branches"
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
          v-if="isAdmin || isSupport || isTeamLead"
          class="col-span-1 space-y-2 align-self-start"
        >
          <label
            for="teamsFilter"
            class="mb-2"
          >Команда</label>
          <mutiselect
            id="teamsFilter"
            v-model="filterSets.teams"
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
          v-if="isAdmin || isSupport || isTeamLead"
          class="col-span-1 space-y-2 align-self-start"
        >
          <label
            for="usersFilter"
            class="mb-2"
          >Баер</label>
          <mutiselect
            id="usersFilter"
            v-model="filterSets.users"
            :show-labels="false"
            :multiple="false"
            :options="users"
            placeholder="Выберите байера"
            track-by="id"
            label="name"
          ></mutiselect>
        </div>
        <div class="flex items-center col-span-1 pt-6 mx-2 align-middle align-self-start">
          <button
            type="button"
            class="mr-2 button btn-primary"
            :disabled="isBusy"
            @click="load"
          >
            <span v-if="isBusy">
              <fa-icon
                :icon="['far', 'spinner']"
                class="mr-2 fill-current"
                spin
                fixed-width
              ></fa-icon>
              Загрузка
            </span>
            <span v-else>Загрузить</span>
          </button>

          <button
            type="button"
            class="button btn-primary"
            :disabled="isBusy"
            @click="download"
          >
            <span v-if="isBusy">
              <fa-icon
                :icon="['far', 'spinner']"
                class="mr-2 fill-current"
                spin
                fixed-width
              ></fa-icon>
              Загрузка
            </span>
            <span v-else>Скачать</span>
          </button>
        </div>
      </div>
    </div>
    <div class="container mx-auto bg-white rounded shadow">
      <report-table
        auto-height
        :report="report"
      ></report-table>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import { uniqBy } from 'lodash-es';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import downloadLink from '../../utilities/helpers/downloadLink';
export default {
  name: 'lead-manager-assignments',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment()
      .startOf('month')
      .subtract(1, 'days')
      .format('YYYY-MM-DD')} to ${moment().format('YYYY-MM-DD')}`,
    pickerConfig: {
      defaultDates: `${moment().format(
        'YYYY-MM-DD',
      )} to ${moment().format('YYYY-MM-DD')}`,
      minDate: '2019-12-02',
      maxDate: moment().format('YYYY-MM-DD'),
      mode: 'range',
    },
    report: {
      headers: [],
      rows: [],
      summary: [],
      period: null,
    },
    filterSets: {
      offices: [],
      offers: [],
      managers: [],
      branches: [],
      teams: [],
      users: [],
      officeGroups: [],
    },
    isBusy: false,
    offices: [],
    offers: [],
    managers: [],
    branches: [],
    teams: [],
    users: [],
    officeGroups: [],
  }),
  computed: {
    period() {
      const dates = this.dates.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
    cleanFilters() {
      return {
        since: this.period.since,
        until: this.period.until,
        offices:
          this.filterSets.offices === null
            ? null
            : this.filterSets.offices.map(office => office.id),
        offers:
          this.filterSets.offers === null
            ? null
            : this.filterSets.offers.map(offer => offer.id),
        managers:
          this.filterSets.managers === null
            ? null
            : this.filterSets.managers.map(manager => manager.id),
        branches:
          this.filterSets.branches === null
            ? null
            : this.filterSets.branches.id,
        teams:
          this.filterSets.teams === null
            ? null
            : this.filterSets.teams.id,
        users:
          this.filterSets.users === null
            ? null
            : this.filterSets.users.id,
        officeGroups:
          this.filterSets.officeGroups === null
            ? null
            : this.filterSets.officeGroups.map(group => group.id),
      };
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
  },
  created() {
    this.load();
    this.getOffices();
    this.getOfficeGroups();
    this.getOffers();
    this.getManagers();
    if (this.isAdmin || this.isTeamLead || this.isSupport) {
      this.getBranches();
      this.getTeams();
      this.getUsers();
    }
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/lead-manager-assignments', {
          params: this.cleanFilters,
        })
        .then(response => (this.report = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить отчет',
            message: error.response.data.message,
          }),
        )
        .finally(() => (this.isBusy = false));
    },

    download() {
      this.isBusy = true;
      axios
        .get('/api/reports/exports/lead-manager-assignments', {
          params: this.cleanFilters,
          responseType: 'blob',
        })
        .then(({ data }) =>
          downloadLink(data, 'lead-manager-assignments.xlsx'),
        )
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось скачать отчет',
            message: error.response.data.message,
          }),
        )
        .finally(() => (this.isBusy = false));
    },

    getOffices() {
      axios
        .get('/api/offices')
        .then(response => (this.offices = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить офисы',
            message: error.response.data.message,
          }),
        );
    },

    getOffers() {
      axios
        .get('/api/offers')
        .then(response => (this.offers = response.data))
        .catch(error => {
          this.$toast.error({
            title: 'Не удалось загрузить оферы.',
            message: error.response.data.message,
          });
        });
    },

    getManagers() {
      axios
        .get('/api/managers')
        .then(response => (this.managers = response.data))
        .catch(error => {
          this.$toast.error({
            title: 'Не удалось загрузить менеджеров.',
            message: error.response.data.message,
          });
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
          params: {
            all: true,
            branch: branch === null ? null : branch.id,
          },
        })
        .then(r => {
          this.teams = r.data;
          this.teams.unshift({ id: null, name: 'Все команды' });
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
          this.users.unshift({ id: null, name: 'Без байера' });
        });
    },
    getOfficeGroups() {
      axios.get('/api/office-groups')
        .then(r => this.officeGroups = r.data)
        .catch(error => this.$toast.error({title: 'Не удалось загрузить группы офисов', message: error.response.data.message}));
    },
  },
};
</script>
