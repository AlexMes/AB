<template>
    <div class="flex flex-col">
        <div class="container flex flex-col mx-auto">
            <div
                class="flex flex-col items-start justify-between w-full md:flex-row md:items-center"
            >
                <h1 class="flex text-gray-700">
                    Monthly DTD NOA
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
                        class="flex flex-col w-full mx-2 my-2 align-middle md:my-0 "
                    >
                        <label class="mb-2">Период</label>
                        <date-picker
                            id="datetime"
                            v-model="filters.dates"
                            class="w-full px-1 py-2 text-gray-600 border rounded"
                            :config="pickerConfig"
                            placeholder="Выберите даты"
                        ></date-picker>
                    </div>
                    <div
                        class="flex flex-col w-full pr-4 my-2 align-middle md:my-0 "
                    >
                        <button
                            type="button"
                            class="mt-4 ml-3 button btn-primary"
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
                        class="flex flex-col w-full pr-4 my-2 align-middle md:my-0 "
                    >
                        <button
                            type="button"
                            class="mt-4 ml-3 button btn-primary"
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
        </div>
        <report-table auto-height :report="report"></report-table>
    </div>
</template>

<script>
import moment from "moment";
import DatePicker from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import downloadLink from "../../utilities/helpers/downloadLink";
export default {
    name: "daily",
    components: {
        DatePicker
    },
    data: () => ({
        isBusy: false,
        filters: {
            dates: `${moment()
                .startOf("month")
                .format("YYYY-MM-DD")} to ${moment().format("YYYY-MM-DD")}`,
            offers: [],
            level: "month"
        },
        pickerConfig: {
            defaultDates: `${moment()
                .startOf("month")
                .format("YYYY-MM-DD")} to ${moment().format("YYYY-MM-DD")}`,
            maxDate: moment().format("YYYY-MM-DD"),
            mode: "range"
        },
        report: {
            headers: [],
            rows: [],
            summary: [],
            period: null
        }
    }),
    computed: {
        period() {
            const dates = this.filters.dates.split(" ");
            return {
                since: dates[0],
                until: dates[2] || dates[0]
            };
        },
        cleanFilters() {
            return {
                since: this.period.since,
                until: this.period.until
            };
        }
    },
    created() {
        this.load();
    },
    methods: {
        load() {
            this.isBusy = true;
            axios
                .get("/api/reports/monthly-dtd-noa", {
                    params: this.cleanFilters
                })
                .then(response => (this.report = response.data))
                .catch(error => console.error)
                .finally(() => (this.isBusy = false));
        },
        download() {
            this.isBusy = true;
            axios
                .get("/api/reports/exports/monthly-dtd-noa", {
                    params: this.cleanFilters,
                    responseType: "blob"
                })
                .then(({ data }) => downloadLink(data, "conversion-stats.xlsx"))
                .catch(error =>
                    this.$toast.error({
                        title: "Не удалось скачать отчет",
                        message: error.response.data.message
                    })
                )
                .finally(() => (this.isBusy = false));
        }
    }
};
</script>
