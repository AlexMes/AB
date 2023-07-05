<template>
    <div class="container mx-auto">
        <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
            <div
                class="flex flex-wrap items-center justify-between -mt-4 -ml-4 sm:flex-no-wrap"
            >
                <div class="mt-4 ml-4">
                    <div class="flex flex-col justify-center">
                        <h3
                            class="text-lg font-medium leading-6 text-gray-900"
                            v-text="office.name"
                        ></h3>
                        <!--            <div class="text-xs font-medium leading-6 text-gray-400">
              CPL: {{ office.cpl ? office.cpl : '-' }} / CPA: {{ office.cpa ? office.cpa : '-' }}
            </div>-->
                    </div>
                </div>
                <div class="flex flex-shrink-0 mt-4 ml-4">
                    <span class="inline-flex rounded-md shadow-sm">
                        <router-link
                            v-if="$root.user.role !== 'subsupport'"
                            :to="{ name: 'offices.update', params: { id: id } }"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
                        >
                            <fa-icon
                                :icon="['far', 'pencil-alt']"
                                class="w-5 h-5 mr-2 -ml-1 text-gray-400"
                                fixed-width
                            ></fa-icon>
                            <span>
                                Редактировать
                            </span>
                        </router-link>
                    </span>
                </div>
            </div>
        </div>
        <div class="px-4 overflow-x-scroll bg-white border-b border-gray-200 shadow sm:px-6">
            <div>
                <div>
                    <nav class="flex -mb-px">
                        <router-link
                            v-if="
                                $root.user.role !== 'support' &&
                                    $root.user.role !== 'subsupport'
                            "
                            :to="{
                                name: 'offices.users',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Пользователи
                        </router-link>
                        <router-link
                            :to="{
                                name: 'offices.managers',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Менеджеры
                        </router-link>
                        <router-link
                            v-if="$root.user.role !== 'subsupport'"
                            :to="{
                                name: 'offices.orders',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Заказы
                        </router-link>
                        <router-link
                            v-if="$root.user.role !== 'subsupport'"
                            :to="{
                                name: 'offices.deposits',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Депозиты
                        </router-link>
                        <router-link
                            v-if="$root.user.role !== 'subsupport'"
                            :to="{
                                name: 'offices.statuses',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Разрешенные статусы
                        </router-link>
                        <router-link
                            v-if="$root.user.role !== 'subsupport'"
                            :to="{
                                name: 'offices.status-configs',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Конфигурации статусов
                        </router-link>
                        <router-link
                            v-if="$root.user.role !== 'subsupport'"
                            :to="{
                                name: 'offices.office-payments',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Оплаты
                        </router-link>
                        <router-link
                            v-if="['admin', 'support', 'developer'].includes($root.user.role)"
                            :to="{
                                name: 'offices.distribution-rules',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Гео раздачи
                        </router-link>
                        <router-link
                            v-if="['admin', 'support'].includes($root.user.role)"
                            :to="{
                                name: 'offices.morning-branches',
                                params: { id: id }
                            }"
                            active-class="text-teal-600 border-teal-500 focus:text-teal-800 focus:border-teal-700"
                            class="px-1 py-4 mr-8 text-sm font-medium leading-5 text-gray-500 whitespace-no-wrap border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
                        >
                            Утренняя выдача
                        </router-link>
                    </nav>
                </div>
            </div>
        </div>
        <div class="">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
export default {
    name: "offices-show",
    props: {
        id: {
            type: [String, Number],
            default: null
        }
    },
    data() {
        return {
            isLoading: false,
            office: {}
        };
    },
    created() {
        this.load();
    },
    methods: {
        load() {
            axios
                .get(`/api/offices/${this.id}`)
                .then(r => (this.office = r.data))
                .catch(e => {
                    this.$toast.error({
                        title: "Не удалось загрузить офис.",
                        message: e.response.data.message
                    });
                });
        }
    }
};
</script>
