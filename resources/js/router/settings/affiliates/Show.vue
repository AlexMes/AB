<template>
    <div class="w-full h-auto mb-8">
        <div v-if="affiliate">
            <div class="bg-white rounded shadow">
                <div
                    v-if="affiliate"
                    class="flex items-center justify-between px-4 py-5 border-b border-gray-200 sm:px-6"
                >
                    <h3
                        class="text-lg font-medium leading-6 text-gray-900"
                        v-text="affiliate.name"
                    ></h3>
                    <div class="flex">
                        <span
                            class="relative z-0 inline-flex rounded-md shadow-sm"
                        >
                            <router-link
                                :to="{
                                    name: 'affiliates.update',
                                    params: { id: id }
                                }"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-l-md hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700"
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
                            <span class="relative block -ml-px">
                                <button
                                    type="button"
                                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-r-md hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500"
                                    aria-label="Expand"
                                    @click.prevent="
                                        isEditMenuOpen = !isEditMenuOpen
                                    "
                                >
                                    <svg
                                        class="w-5 h-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                                <!--
              Dropdown panel, show/hide based on dropdown state.

              Entering: "transition ease-out duration-100"
                From: "transform opacity-0 scale-95"
                To: "transform opacity-100 scale-100"
              Leaving: "transition ease-in duration-75"
                From: "transform opacity-100 scale-100"
                To: "transform opacity-0 scale-95"
            -->
                                <div
                                    class="absolute right-0 w-56 mt-2 -mr-1 origin-top-right rounded-md shadow-lg"
                                    :class="{ hidden: !isEditMenuOpen }"
                                >
                                    <div class="bg-white rounded-md shadow-xs">
                                        <div class="py-1">
                                            <a
                                                href="#"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                                                @click.prevent="
                                                    $modal.show(
                                                        'import-affiliate-leads'
                                                    )
                                                "
                                            >
                                                <fa-icon
                                                    class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                                    :icon="['far', 'upload']"
                                                    fixed-width
                                                ></fa-icon>
                                                Загрузить лиды
                                            </a>
                                            <a
                                                href="#"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                                                @click.prevent="
                                                    $modal.show(
                                                        'export-affiliate-leads-modal',
                                                        { affiliate: affiliate }
                                                    )
                                                "
                                            >
                                                <fa-icon
                                                    class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                                    :icon="['far', 'download']"
                                                    fixed-width
                                                ></fa-icon>
                                                Скачать лиды
                                            </a>
                                            <a
                                                href="#"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                                                @click.prevent="
                                                    $modal.show(
                                                        'export-affiliate-leads-hide-deps-modal',
                                                        { affiliate: affiliate }
                                                    )
                                                "
                                            >
                                                <fa-icon
                                                    class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                                    :icon="['far', 'download']"
                                                    fixed-width
                                                ></fa-icon>
                                                Скачать лиды (скрытые депы)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </span>
                        </span>
                    </div>
                </div>
                <div v-if="affiliate" class="px-4 py-5 sm:p-0">
                    <dl>
                        <!--            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
              <dt class="text-sm font-medium leading-5 text-gray-500">
                CPL
              </dt>
              <dd
                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                v-text="`${affiliate.cpl}$`"
              >
              </dd>
            </div>
            <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
              <dt class="text-sm font-medium leading-5 text-gray-500">
                CPA
              </dt>
              <dd
                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                v-text="`${affiliate.cpa}$`"
              >
              </dd>
            </div>-->
                        <div
                            class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
                        >
                            <dt
                                class="text-sm font-medium leading-5 text-gray-500"
                            >
                                Оффер
                            </dt>
                            <dd
                                v-if="affiliate.offer"
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                                v-text="affiliate.offer.name"
                            ></dd>
                            <dd
                                v-else
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                -
                            </dd>
                        </div>
                        <div
                            class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
                        >
                            <dt
                                class="text-sm font-medium leading-5 text-gray-500"
                            >
                                Источник траффика
                            </dt>
                            <dd
                                v-if="affiliate.traffic_source"
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                                v-text="affiliate.traffic_source.name"
                            ></dd>
                            <dd
                                v-else
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                -
                            </dd>
                        </div>
                        <div
                            class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
                        >
                            <dt
                                class="text-sm font-medium leading-5 text-gray-500"
                            >
                                Филиал
                            </dt>
                            <dd
                                v-if="affiliate.branch"
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                                v-text="affiliate.branch.name"
                            ></dd>
                            <dd
                                v-else
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                -
                            </dd>
                        </div>
                        <div
                            class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
                        >
                            <dt
                                class="text-sm font-medium leading-5 text-gray-500"
                            >
                                API Key
                            </dt>
                            <dd
                                class="mt-1 text-sm leading-5 text-gray-900 break-all sm:mt-0 sm:col-span-2"
                                v-text="affiliate.api_key"
                            ></dd>
                        </div>
                        <div
                            class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
                        >
                            <dt
                                class="text-sm font-medium leading-5 text-gray-500"
                            >
                                Постбек
                            </dt>
                            <dd
                                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                <div
                                    class="w-4 h-4 ml-1 border-0 rounded-full"
                                    :class="[
                                        affiliate.postback
                                            ? 'bg-green-500'
                                            : 'bg-red-500'
                                    ]"
                                ></div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            <div class="mt-8">
                <h2 class="mb-3 text-gray-700">
                    Лиды
                </h2>

                <div v-if="hasLeads">
                    <div class="flex flex-col bg-white rounded shadow">
                        <table class="relative table w-full table-auto">
                            <thead
                                class="sticky w-full font-semibold text-gray-700 uppercase bg-gray-300"
                            >
                                <tr class="px-3">
                                    <th class="px-2 py-3 pl-5">
                                        #
                                    </th>
                                    <th>Дата</th>
                                    <th>Имя</th>
                                    <th>Номер</th>
                                    <th>Домен</th>
                                    <th>IP</th>
                                    <th>Клик</th>
                                    <th colspan="2">
                                        Оффер
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="w-full">
                                <lead-list-item
                                    v-for="lead in leads"
                                    :key="lead.id"
                                    :lead="lead"
                                >
                                </lead-list-item>
                            </tbody>
                        </table>
                    </div>
                    <pagination
                        :response="response"
                        @load="getLeads"
                    ></pagination>
                </div>
                <div v-else class="flex flex-col p-4 bg-white rounded shadow">
                    <p>Лидов не найдено</p>
                </div>
            </div>
        </div>
        <import-affiliate-leads :affiliate="affiliate"></import-affiliate-leads>
        <export-affiliate-leads-modal></export-affiliate-leads-modal>
        <export-affiliate-leads-hide-deps-modal></export-affiliate-leads-hide-deps-modal>
    </div>
</template>

<script>
import ExportAffiliateLeadsModal from "../../../components/settings/export-affiliate-leads-modal";
import ExportAffiliateLeadsHideDepsModal from "../../../components/settings/export-affiliate-leads-hide-deps-modal";
export default {
    name: "affiliates-show",
    components: { ExportAffiliateLeadsModal, ExportAffiliateLeadsHideDepsModal },
    props: {
        id: {
            type: [String, Number],
            default: null
        }
    },
    data() {
        return {
            response: {},
            isLoading: false,
            isEditMenuOpen: false,
            affiliate: {},
            leads: []
        };
    },
    computed: {
        hasLeads() {
            return this.leads.length > 0;
        }
    },
    created() {
        this.load();
        this.getLeads();
    },
    methods: {
        load() {
            axios
                .get(`/api/affiliates/${this.id}`)
                .then(r => (this.affiliate = r.data))
                .catch(e => {
                    this.$toast.error({
                        title: "Не удалось загрузить affiliate.",
                        message: e.response.data.message
                    });
                });
        },

        getLeads(page = 1) {
            axios
                .get(`/api/affiliates/${this.id}/leads`, { params: { page } })
                .then(r => {
                    this.response = r.data;
                    this.leads = r.data.data;
                })
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить лиды.",
                        message: e.response.data.message
                    })
                );
        }
    }
};
</script>
