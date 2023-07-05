<template>
  <div class="flex flex-col">
    <div class="container flex flex-col mx-auto">
      <div
        class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
      >
        <h1 class="flex text-gray-700">
          Выдача лидов офисам
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
      <div class="flex flex-col my-6">
        <div
          class="grid w-full grid-flow-row-dense grid-cols-1 gap-4 pb-6 sm:grid-cols-2 md:grid-cols-4"
        >
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
          <div
            v-if="['admin', 'support'].includes($root.user.role)"
            class="col-span-1 space-y-2 align-self-start"
          >
            <label
              for="brachFilter"
              class="mb-2"
            >Филиал</label>
            <mutiselect
              id="brachFilter"
              v-model="filterSets.branch_id"
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
            v-if="['admin', 'support'].includes($root.user.role)"
            class="col-span-1 space-y-2 align-self-start"
          >
            <label
              for="teamsFilter"
              class="mb-2"
            >Команда</label>
            <mutiselect
              id="teamsFilter"
              v-model="filterSets.team_id"
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
            v-if="['admin', 'support'].includes($root.user.role)"
            class="col-span-1 space-y-2 align-self-start"
          >
            <label
              for="usersFilter"
              class="mb-2"
            >Баер</label>
            <mutiselect
              id="usersFilter"
              v-model="filterSets.user_id"
              :show-labels="false"
              :multiple="false"
              :options="users"
              placeholder="Выберите байера"
              track-by="id"
              label="name"
            ></mutiselect>
          </div>
          <div class="col-span-1 space-y-2 align-self-start">
            <label class="mb-2">Трафик</label>
            <select v-model="filterSets.traffic">
              <option :value="null">
                Весь
              </option>
              <option value="affiliate">
                Партнерский
              </option>
              <option value="own">
                Свой
              </option>
            </select>
          </div>

          <div class="col-span-1 space-y-2 align-self-start">
            <button
              type="button"
              class="mt-6 mr-2 button btn-primary"
              :disabled="isBusy"
              @click="load"
            >
              <span v-if="isBusy">
                <fa-icon
                  :icon="['far', 'spinner']"
                  class="mr-2 fill-current "
                  spin
                  fixed-width
                ></fa-icon>
                Загрузка
              </span>
              <span v-else>Загрузить</span>
            </button>
          </div>
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
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'lead-office-assignments',
  components: {
    DatePicker,
  },
  data: () => ({
    dates: `${moment()
      .startOf('month')
      .format('YYYY-MM-DD')} — ${moment().format('YYYY-MM-DD')}`,
    pickerConfig: {
      defaultDates: `${moment()
        .startOf('month')
        .format('YYYY-MM-DD')} — ${moment().format('YYYY-MM-DD')}`,
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
      traffic: null,
      branch_id: null,
      team_id: null,
      user_id: null,
      officeGroups: [],
    },
    isBusy: false,
    offices: [],
    offers: [],
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
        traffic: this.filterSets.traffic,
        branch_id:
          this.filterSets.branch_id === null
            ? null
            : this.filterSets.branch_id.id,
        team_id:
          this.filterSets.team_id === null
            ? null
            : this.filterSets.team_id.id,
        user_id:
          this.filterSets.user_id === null
            ? null
            : this.filterSets.user_id.id,
        office_groups:
          this.filterSets.officeGroups === null
            ? null
            : this.filterSets.officeGroups.map(group => group.id),
      };
    },
  },
  created() {
    this.load();
    this.getOffices();
    this.getOffers();
    this.getBranches();
    this.getTeams();
    this.getUsers();
    this.getOfficeGroups();
  },
  methods: {
    load() {
      this.isBusy = true;
      axios
        .get('/api/reports/lead-office-assignments', {
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
          this.users.unshift({ id: 'null', name: 'Без байера' });
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
