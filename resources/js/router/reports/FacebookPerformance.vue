<template>
    <div class="flex flex-col">
        <div class="container flex flex-col mx-auto">
            <div
                class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
            >
                <h1 class="flex text-gray-700">
                    FB Performance report
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
                    <div
                        class="flex flex-col w-full my-2 align-middlemx-2 md:my-0 "
                    >
                        <label class="mb-2">Период</label>
                        <date-picker
                            id="datetime"
                            v-model="dates"
                            class="w-full px-1 py-2 text-gray-600 border rounded"
                            :config="pickerConfig"
                            placeholder="Выберите даты"
                        ></date-picker>
                    </div>
                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <label class="mb-2">Уровень</label>
                        <select v-model="level">
                            <option
                                v-for="level in filterSets.levels"
                                :key="level.id"
                                :value="level.id"
                                v-text="level.name"
                            ></option>
                        </select>
                    </div>
                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <label class="mb-2">Баеры</label>
                        <mutiselect
                            v-model="users"
                            :show-labels="false"
                            :multiple="true"
                            :options="filterSets.users"
                            placeholder="Выберите баера"
                            track-by="id"
                            label="name"
                        ></mutiselect>
                    </div>
                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <label class="mb-2">Аккаунты</label>
                        <mutiselect
                            v-model="accounts"
                            :show-labels="false"
                            :multiple="true"
                            :options="filterSets.accounts"
                            :loading="isLoadingAccounts"
                            :internal-search="false"
                            placeholder="Выберите аккаунты"
                            track-by="id"
                            label="name"
                            @search-change="getAccounts"
                        ></mutiselect>
                    </div>
                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <label class="mb-2">Группы</label>
                        <mutiselect
                            v-model="groups"
                            :show-labels="false"
                            :multiple="true"
                            :options="filterSets.groups"
                            :loading="isLoadingGroups"
                            :options-limit="50"
                            placeholder="Поиск группы"
                            track-by="id"
                            label="name"
                            @search-change="getGroups"
                        ></mutiselect>
                    </div>

                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <label class="mb-2">Подходы</label>
                        <mutiselect
                            v-model="tags"
                            :show-labels="false"
                            :multiple="true"
                            :options="filterSets.tags"
                            placeholder="Выберите подход"
                            track-by="id"
                            label="name"
                        ></mutiselect>
                    </div>

                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
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

                    <div
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <button
                            type="button"
                            class="mt-4 button btn-primary"
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

                <div class="flex items-start md:items-center">
                    <div class="flex flex-col w-1/4 my-2 align-middle">
                        <label class="mb-2">UTM Campaign</label>
                        <mutiselect
                            v-model="utmCampaignValue"
                            :show-labels="false"
                            :options="filterSets.utmCampaigns"
                            :loading="isLoadingUtmCampaigns"
                            :internal-search="false"
                            placeholder="Выберите кампанию"
                            @search-change="getUtm"
                        ></mutiselect>
                    </div>

                    <div class="flex flex-col w-1/4 my-2 ml-2 align-middle">
                        <label class="mb-2">UTM Content</label>
                        <mutiselect
                            v-model="utmContentValue"
                            :show-labels="false"
                            :options="filterSets.utmContent"
                            :loading="isLoadingUtmContents"
                            placeholder="Выберите контент"
                            @search-change="getUtmContent"
                        ></mutiselect>
                    </div>
                </div>
            </div>
        </div>
        <report-table auto-height :report="report"></report-table>
    </div>
</template>

<script>
import moment from "moment";
import { uniqBy } from "lodash-es";
import DatePicker from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import downloadLink from "../../utilities/helpers/downloadLink";
import { debounce } from "lodash-es";
export default {
    name: "performance",
    components: {
        DatePicker
    },
    data: () => ({
        dates: `${moment().format("YYYY-MM-DD")} to ${moment().format(
            "YYYY-MM-DD"
        )}`,
        pickerConfig: {
            defaultDates: `${moment().format(
                "YYYY-MM-DD"
            )} to ${moment().format("YYYY-MM-DD")}`,
            minDate: "2019-12-02",
            maxDate: moment().format("YYYY-MM-DD"),
            mode: "range"
        },
        report: {
            headers: [],
            rows: [],
            summary: [],
            period: null
        },
        filterSets: {
            users: [],
            accounts: [],
            levels: [
                { id: "account", name: "Аккаунт" },
                { id: "campaign", name: "Кампания" },
                { id: "adset", name: "Адсет" },
                { id: "ad", name: "Объявление" }
            ],
            utmCampaigns: [],
            utmContent: [],
            groups: [],
            tags: [],
        },
        level: "campaign",
        isBusy: false,
        users: [],
        accounts: [],
        utmCampaign: null,
        utmContent: null,
        hideFacebook: false,
        groups: [],
        tags: [],
        isLoadingGroups: false,
        isLoadingAccounts: false,
        isLoadingUtmCampaigns: false,
        isLoadingUtmContents: false,
    }),
    computed: {
        period() {
            const dates = this.dates.split(" ");
            return {
                since: dates[0],
                until: dates[2] || dates[0]
            };
        },
        utmCampaignValue: {
            get() {
                return !!this.utmCampaign && this.utmCampaign !== ""
                    ? this.utmCampaign
                    : "Все";
            },
            set(value) {
                this.utmCampaign =
                    !!value && value !== "" && value !== "Все" ? value : null;
            }
        },
        utmContentValue: {
            get() {
                return !!this.utmContent && this.utmContent !== ""
                    ? this.utmContent
                    : "Все";
            },
            set(value) {
                this.utmContent =
                    !!value && value !== "" && value !== "Все" ? value : null;
            }
        },
        cleanFilters() {
            return {
                level: this.level,
                since: this.period.since,
                until: this.period.until,
                users:
                    this.users === null
                        ? null
                        : this.users.map(user => user.id),
                accounts:
                    this.accounts === null
                        ? null
                        : this.accounts.map(account => account.id),
                campaign: this.utmCampaign,
                content: this.utmContent,
                hideFacebook: this.hideFacebook,
                groups: this.groups.map(group => group.id),
                tags: this.tags.map(tag => tag.id),
            };
        }
    },
    watch: {
        users() {
            this.getAccounts();
        }
    },
    created() {
        this.getUsers();
        this.getAccounts();
        this.load();
        this.getUtm();
        this.getUtmContent();
        this.getTags();
    },
    methods: {
        load() {
            this.isBusy = true;
            axios
                .get("/api/reports/facebook-performance", { params: this.cleanFilters })
                .then(response => {
                    this.report = response.data
                })
                .catch(error =>
                    this.$toast.error({
                        title: "Не удалось загрузить отчет",
                        message: error.response.data.message
                    })
                )
                .finally(() => (this.isBusy = false));
        },
        download() {
            this.isBusy = true;
            axios
                .get("/api/reports/exports/facebook-performance", {
                    params: this.cleanFilters,
                    responseType: "blob"
                })
                .then(({ data }) => downloadLink(data, "performance.xlsx"))
                .catch(error =>
                    this.$toast.error({
                        title: "Не удалось скачать отчет",
                        message: error.response.data.message
                    })
                )
                .finally(() => (this.isBusy = false));
        },
        getUsers() {
            axios
                .get("/api/report-buyers")
                .then(response => (this.filterSets.users = response.data))
                .catch(error =>
                    this.$toast.error({
                        title: "Не удалось загрузить пользователей",
                        message: error.response.data.message
                    })
                );
        },
        getAccounts: debounce(function(search = null) {
            if (search === null || search.length > 1) {
                this.isLoadingAccounts = true;
                axios
                    .get("/api/report-accounts", {
                        params: { users: this.cleanFilters.users, search }
                    })
                    .then(
                        response =>
                            (this.filterSets.accounts = uniqBy(
                                response.data,
                                account => account.id
                            ))
                    )
                    .catch(error =>
                        this.$toast.error({
                            title: "Не удалось загрузить аккаунты",
                            message: error.response.data.message
                        })
                    )
                    .finally(() => (this.isLoadingAccounts = false));
            }
        }, 300),
        getUtm: debounce(function(search = null) {
            if (search === null || search.length > 1) {
                this.isLoadingUtmCampaigns = true;
                axios
                    .get("/api/utm-campaigns", {
                        params: { search, paginate: search === null }
                    })
                    .then(
                        r => (this.filterSets.utmCampaigns = ["Все", ...r.data])
                    )
                    .catch(err =>
                        this.$toast.error({
                            title: "Ошибка",
                            message: err.response.data.message
                        })
                    )
                    .finally(() => (this.isLoadingUtmCampaigns = false));
            }
        }, 300),
        getUtmContent: debounce(function(search = null) {
            if (search === null || search.length > 1) {
                this.isLoadingUtmContents = true;
                axios
                    .get("/api/utm-content", {
                        params: { search, paginate: search === null }
                    })
                    .then(
                        r => (this.filterSets.utmContent = ["Все", ...r.data])
                    )
                    .catch(err =>
                        this.$toast.error({
                            title: "Ошибка",
                            message: err.response.data.message
                        })
                    )
                    .finally(() => (this.isLoadingUtmContents = false));
            }
        }, 300),
        getGroups: debounce(function(search = null) {
            if (!!search) {
                this.isLoadingGroups = true;
                axios
                    .get("/api/groups", { params: { all: true, search } })
                    .then(r => (this.filterSets.groups = r.data))
                    .catch(err =>
                        this.$toast.error({
                            title: "Ошибка",
                            message: err.response.data.message
                        })
                    )
                    .finally(() => (this.isLoadingGroups = false));
            }
        }, 300),
        getTags() {
            axios
                .get("/api/tags/", { params: { all: true } })
                .then(r => (this.filterSets.tags = r.data))
                .catch(err =>
                    this.$toast.error({
                        title: "Ошибка",
                        message: err.response.data.message
                    })
                );
        },
    }
};
</script>
