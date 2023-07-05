<template>
    <div class="container mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-gray-700">
                Batches
            </h1>
            <div class="flex">
                <search-field @search="search"></search-field>
                <router-link
                    v-if="$root.isAdmin"
                    :to="{ name: 'resell-batches.create' }"
                    class="flex items-center ml-3 button btn-primary"
                >
                    <fa-icon
                        :icon="['far', 'plus']"
                        class="mr-2 fill-current"
                    ></fa-icon>
                    Добавить
                </router-link>
            </div>
        </div>

        <div v-if="hasBatches">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div
                        class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                    >
                        <div
                            class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg"
                        >
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"
                                        >
                                            Id
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"
                                        >
                                            Название
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"
                                        >
                                            Статус
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"
                                        >
                                            Офер _R
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"
                                        >
                                            Автологин
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50"
                                        >
                                            Игнор роутов в паузе
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50"></th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <resell-batch-list-item
                                        v-for="batch in batches"
                                        :key="batch.id"
                                        :batch="batch"
                                    ></resell-batch-list-item>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <pagination :response="response" @load="load"></pagination>
        </div>
        <div v-else class="p-4 text-center">
            <h2>Пакетов не найдено</h2>
        </div>
    </div>
</template>

<script>
import ResellBatchListItem from "../../components/resell-batches/resell-batch-list-item";
import ResellBatchesStatsProgress from "../../components/widgets/resell-batches-stats-progress";
export default {
    name: "resell-batches-index",
    components: { ResellBatchesStatsProgress, ResellBatchListItem },
    data: () => ({
        batches: {},
        response: {},
        needle: null
    }),
    computed: {
        hasBatches() {
            return this.batches !== undefined && this.batches.length > 0;
        }
    },
    created() {
        this.load();
    },
    methods: {
        load(page = null) {
            axios
                .get("/api/resell-batches", {
                    params: { page, search: this.needle }
                })
                .then(response => {
                    this.batches = response.data.data;
                    this.response = response.data;
                })
                .catch(err => {
                    this.$toast.error({
                        title: "Не удалось загрузить пакеты.",
                        message: err.response.data.message
                    });
                });
        },
        search(needle) {
            this.needle = needle;
            this.load();
        }
    }
};
</script>
