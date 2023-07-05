<template>
    <div class="flex flex-col">
        <div class="container flex flex-col mx-auto">
            <div
                class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
            >
                <h1 class="flex text-gray-700">
                    Leftovers by buyers
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
                    class="flex flex-wrap items-start md:flex-no-wrap md:items-center"
                >
                    <div class="flex flex-col my-2 align-middlemx-2 md:my-0">
                        <label class="mb-2">Период</label>
                        <date-picker
                            id="datetime"
                            v-model="dates"
                            class="w-full px-1 py-2 text-gray-600 border rounded"
                            :config="pickerConfig"
                            placeholder="Выберите даты"
                        ></date-picker>
                    </div>

                    <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
                        <label class="mb-2">Разбивка</label>
                        <select v-model="filterSets.groupBy">
                            <option
                                v-for="group in groupByTypes"
                                :key="group.key"
                                :value="group.key"
                                v-text="group.name"
                            ></option>
                        </select>
                    </div>

                    <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
                        <label class="mb-2">Баеры</label>
                        <mutiselect
                            v-model="filterSets.users"
                            :show-labels="false"
                            :multiple="true"
                            :options="users"
                            placeholder="Выберите баера"
                            track-by="id"
                            label="name"
                        ></mutiselect>
                    </div>

                    <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
                        <label class="mb-2">Филиалы</label>
                        <mutiselect
                            v-model="filterSets.branches"
                            :show-labels="false"
                            :multiple="true"
                            :options="branches"
                            placeholder="Выберите филиалы"
                            track-by="id"
                            label="name"
                        ></mutiselect>
                    </div>

                    <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
                        <label class="mb-2">Оффер</label>
                        <mutiselect
                            v-model="offers"
                            :show-labels="false"
                            :multiple="true"
                            :options="filterSets.offers"
                            placeholder="Выберите оффер"
                            track-by="id"
                            label="name"
                        ></mutiselect>
                    </div>

                    <div
                        class="flex items-end mx-2 my-2 space-x-2 align-middle md:my-0"
                    >
                        <button
                            type="button"
                            class="mt-4 button btn-primary"
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
    name: 'leftovers-by-buyers',
    components: {
        DatePicker,
    },
    data: () => ({
        dates: `${moment().format('YYYY-MM-DD')} to ${moment().format(
            'YYYY-MM-DD',
        )}`,
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
            groupBy: 'offer',
            users: [],
            offers: [],
            branches: [],
        },
        isBusy: false,
        groupByTypes: [
            { key: 'branch', name: 'Филиал' },
            { key: 'user', name: 'Байер' },
            { key: 'offer', name: 'Офер' },
            { key: 'date', name: 'Дата' },
        ],
        offers: [],
        users: [],
        branches: [],
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
                groupBy: this.filterSets.groupBy,
                offers:
                    this.offers === null
                        ? null
                        : this.offers.map(offer => offer.id),
                users:
                    this.filterSets.users === null
                        ? null
                        : this.filterSets.users.map(user => user.id),
                branches:
                    this.filterSets.branches === null
                        ? null
                        : this.filterSets.branches.map(branch => branch.id),
            };
        },
    },
    created() {
        this.load();
        this.getOffers();
        this.getUsers();
        this.getBranches();
    },
    methods: {
        load() {
            this.isBusy = true;
            axios
                .get('/api/reports/leftovers-by-buyers', { params: this.cleanFilters })
                .then(response => (this.report = response.data))
                .catch(error =>
                    this.$toast.error({
                        title: 'Не удалось загрузить отчет',
                        message: error.response.data.message,
                    }),
                )
                .finally(() => (this.isBusy = false));
        },
        getOffers() {
            axios
                .get('/api/offers')
                .then(r => (this.filterSets.offers = r.data))
                .catch(err =>
                    this.$toast.error({
                        title: 'Ошибка',
                        message: err.response.data.message,
                    }),
                );
        },
        getUsers() {
            axios
                .get('/api/report-buyers')
                .then(response => (this.users = response.data))
                .catch(error =>
                    this.$toast.error({
                        title: 'Не удалось загрузить пользователей',
                        message: error.response.data.message,
                    }),
                );
        },
        getBranches() {
            axios
                .get('/api/branches')
                .then(response => (this.branches = response.data))
                .catch(error =>
                    this.$toast.error({
                        title: 'Не удалось загрузить филиалы',
                        message: error.response.data.message,
                    }),
                );
        },
    },
};
</script>
