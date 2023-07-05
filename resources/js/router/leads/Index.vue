<template>
    <div class="container mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-gray-700">
                Лиды
            </h1>
            <div class="flex">
                <search-field
                    class="mr-2"
                    @search="search"
                ></search-field>
                <label
                    v-if="$root.user.role === 'admin' || $root.user.role === 'support' && $root.user.branch_id === 19"
                    class="px-4 py-2 mr-2 rounded cursor-pointer btn-primary"
                    @click="$modal.show('delete-duplicates-modal')"
                >
                    <fa-icon
                        :icon="['far', 'times-circle']"
                        class="mr-2"
                    ></fa-icon>
                    Удалить дубли
                </label>
                <label
                    v-if="$root.user.role === 'admin'"
                    class="px-4 py-2 mr-2 rounded cursor-pointer btn-primary"
                    @click="$modal.show('export')"
                >
                    <fa-icon
                        :icon="['far', 'download']"
                        class="mr-2"
                    ></fa-icon>
                    Экспорт
                </label>
                <label
                    v-if="$root.user.role === 'admin' || $root.user.role === 'support' && $root.user.branch_id === 19"
                    class="px-4 py-2 mr-2 rounded cursor-pointer btn-primary"
                    @click="$modal.show('pack-cold-base')"
                >
                    <fa-icon
                        :icon="['far', 'exchange']"
                        class="mr-2"
                    ></fa-icon>
                    Упаковать CD
                </label>
                <label
                    v-if="$root.user.role === 'admin'"
                    class="px-4 py-2 mr-2 rounded cursor-pointer btn-primary"
                    @click="$modal.show('leads-copy-to-offer-modal')"
                >
                    <fa-icon
                        :icon="['far', 'copy']"
                        class="mr-2"
                    ></fa-icon>
                    Копировать
                </label>
                <span
                    v-if="$root.user.role !== 'subsupport'"
                    class="relative z-0 inline-flex rounded-md shadow-sm cursor-pointer btn-primary"
                >
              <span
                  class="relative inline-flex items-center px-4 py-2 font-medium leading-5 transition duration-150 ease-in-out border border-gray-300 rounded-l-md focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                  @click="$modal.show('leads-import-modal')"
              >
                <fa-icon
                    :icon="['far', 'plus']"
                    class="mr-2"
                ></fa-icon>
                Загрузить
              </span>
              <span class="relative block -ml-px">
                <button
                    type="button"
                    class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 transition duration-150 ease-in-out border border-gray-300 rounded-r-md focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue"
                    aria-label="Expand"
                    @click.prevent="isButtonOpened = !isButtonOpened"
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
                <div
                    class="absolute right-0 w-56 mt-2 -mr-1 origin-top-right rounded-md shadow-lg btn-primary"
                    :class="{hidden: !isButtonOpened}"
                >
                  <div class="rounded-md shadow-xs">
                    <div class="py-1">
                      <span
                          v-if="$root.user.role == 'admin'"
                          class="block px-4 py-2 cursor-pointer leading-5 focus:outline-none focus:bg-gray-100"
                          @click="$modal.show('leads-upload-modal')"
                      >
                        Загрузить лиды в базу
                      </span>
                    </div>
                  </div>
                </div>
              </span>
          </span>
            </div>
        </div>
        <div class="w-full">
            <div class="flex flex-col mt-3 mb-8 md:flex-row">
                <div class="flex flex-col my-2 mr-4 md:my-0 w-1/5">
                    <label class="mb-2">Имя</label>
                    <input
                        v-model="filters.name"
                        type="search"
                        class="w-full border-b px-3 py-2 bg-transparent pb-1 text-grey-700 outline-none"
                        placeholder="Выберите имя"
                        maxlength="50"
                    />
                </div>
                <div class="flex flex-col my-2 mr-4 md:my-0 w-1/5">
                    <label class="mb-2">Оффер</label>
                    <mutiselect
                        v-model="filters.offer_id"
                        :show-labels="false"
                        :multiple="false"
                        :options="offers"
                        placeholder="Выберите офис"
                        track-by="id"
                        label="name"
                    ></mutiselect>
                </div>
                <div
                    v-if="isAdmin || isSupport || isDeveloper || isSubSupport"
                    class="flex flex-col my-2 mr-4 md:my-0 w-1/5"
                >
                    <label class="mb-2">Баер</label>
                    <mutiselect
                        v-model="filters.user_id"
                        :show-labels="false"
                        :multiple="false"
                        :options="users"
                        placeholder="Выберите баера"
                        track-by="id"
                        label="name"
                    ></mutiselect>
                </div>
                <div
                    v-if="!isSubSupport"
                    class="flex flex-col my-2 mr-4 md:my-0 w-1/5"
                >
                    <label class="mb-2">Домен</label>
                    <mutiselect
                        v-model="filters.domain_id"
                        :show-labels="false"
                        :multiple="false"
                        :options="domains"
                        placeholder="Выберите домен"
                        track-by="id"
                        label="url"
                    ></mutiselect>
                </div>
                <div class="flex flex-col my-2 mr-4 md:my-0 w-1/5">
                    <label class="mb-2">Гео</label>
                    <mutiselect
                        v-model="filters.geo_country"
                        :show-labels="false"
                        :multiple="false"
                        :options="geo_countries"
                        placeholder="Выберите гео"
                    ></mutiselect>
                </div>
                <div class="flex flex-col my-2 mr-4 md:my-0 w-1/5">
                    <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Только последние удалённые</label>
                    <div class="max-w-lg">
                        <toggle v-model="filters.lasttrashed"></toggle>
                    </div>
                </div>
                <div class="flex flex-col my-2 mr-4 md:my-0">
                    <button
                        type="button"
                        class="mt-6 button btn-secondary"
                        @click="load(1)"
                    >
                        <fa-icon
                            :icon="['far', 'filter']"
                            class="mr-2 fill-current"
                            fixed-width
                        ></fa-icon>
                        Фильтровать
                    </button>
                </div>
            </div>
            <div
                v-if="hasLeads"
                class="flex w-full overflow-x-auto overflow-y-hidden"
            >
                <table class="relative table w-full table-auto">
                    <thead
                        class="sticky w-full font-semibold text-gray-700 uppercase bg-gray-300"
                    >
                    <tr class="px-3">
                        <th class="px-2 py-3 pl-5">
                            #
                        </th>
                        <th>Дата</th>
                        <th>Гео</th>
                        <th>Имя</th>
                        <th>Номер</th>
                        <th>Домен</th>
                        <th>IP</th>
                        <th>Клик</th>
                        <th>ID приложения</th>
                        <th>Оффер</th>
                        <th>Баер</th>
                        <th v-if="isAdmin || isSupport"></th>
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
                @load="load"
            ></pagination>
        </div>
        <leads-import-modal></leads-import-modal>
        <leads-upload-modal></leads-upload-modal>
        <mark-lead-leftover-modal></mark-lead-leftover-modal>
        <edit-lead-modal></edit-lead-modal>
        <leads-copy-to-offer-modal></leads-copy-to-offer-modal>
        <pack-cold-base></pack-cold-base>
        <delete-duplicates-modal></delete-duplicates-modal>
        <export></export>
    </div>
</template>

<script>
import LeadsImportModal from '../../components/leads/leads-import-modal';
import LeadsUploadModal from '../../components/leads/leads-upload-modal';
import MarkLeadLeftoverModal from '../../components/leads/mark-lead-leftover-modal';
import EditLeadModal from '../../components/leads/edit-lead-modal';
import LeadsCopyToOfferModal from '../../components/leads/leads-copy-to-offer-modal';
import PackColdBase from '../../components/leads/pack-cold-base';
import Export from '../../components/leads/export';
import DeleteDuplicatesModal from '../../components/leads/delete-duplicates-modal';

export default {
    name: 'index',
    components: {DeleteDuplicatesModal, PackColdBase, LeadsCopyToOfferModal, EditLeadModal, MarkLeadLeftoverModal, LeadsImportModal, LeadsUploadModal, Export },
    data: () => ({
        leads: [],
        response: {},
        needle: null,
        offer_id: null,
        filters: {
            offer_id: null,
            user_id: null,
            domain_id: null,
            geo_country: null,
            lasttrashed: true,
        },
        offers: [],
        users: [],
        domains: [],
        geo_countries: [],
        isButtonOpened: false,
    }),
    computed: {
        hasLeads() {
            return this.leads.length > 0;
        },
        cleanFilters() {
            return {
                offer_id: this.filters.offer_id?.id,
                user_id: this.filters.user_id?.id,
                domain_id: this.filters.domain_id?.id,
                geo_country: this.filters.geo_country,
                lasttrashed: this.filters.lasttrashed === true ? true : null,
            };
        },
        isAdmin() {
            return this.$root.user.role === 'admin';
        },
        isSupport() {
            return this.$root.user.role === 'support';
        },
        isDeveloper() {
            return this.$root.user.role === 'developer';
        },
        isSubSupport() {
            return this.$root.user.role === 'subsupport';
        },
    },
    watch: {
        needle() {
            this.load();
        },
    },
    created() {
        this.filters.offer_id = !!this.$route.params.offer_id ? {id: this.$route.params.offer_id} : null;
        this.date = this.$route.params.date || null;
        this.load();
        this.getOffers();
        if (this.isAdmin || this.isSupport || this.isDeveloper) {
            this.getUsers();
        }
        this.getDomains();
        this.getGeoCountries();
        this.$eventHub.$on('lead-updated', (event) => {
            const index = this.leads.findIndex(
                lead => lead.id === event.lead.id,
            );
            if (index !== -1) {
                this.$set(this.leads, index, event.lead);
            }
        });
    },
    methods: {
        load(page = 1) {
            axios
                .get('/api/leads', {
                    params: {
                        search: this.needle,
                        received: this.$route.params.name || false,
                        received: this.$route.params.received || false,
                        accepted: this.$route.params.accepted || false,
                        leftovers: this.$route.params.leftovers || false,
                        resell_received: this.$route.params.resell_received || false,
                        date: this.date,
                        page: page,
                        ...this.cleanFilters,
                    },
                })
                .then(response => {
                    this.response = response.data;
                    this.leads = response.data.data;
                })
                .catch(err => {
                    this.$toast.error({
                        title: 'Не удалось загрузить лиды.',
                        message: err.response.data.message,
                    });
                });
        },
        search(needle) {
            this.needle = needle;
        },
        getOffers() {
            axios.get('/api/offers').then(r => {
                this.offers = r.data;
                this.offers.unshift({id: null, name: 'Все'});

                if (!!this.$route.params.offer_id) {
                    this.filters.offer_id = this.offers.find(offer => offer.id === this.$route.params.offer_id);
                }
            });
        },
        getUsers() {
            axios
                .get('/api/users', { params: { all: true } })
                .then(r => {
                    this.users = r.data;
                    this.users.unshift({id: '', name: 'Без байера'});
                    this.users.unshift({id: null, name: 'Все'});
                });
        },
        getDomains() {
            axios
                .get('/api/domains', {
                    params: { linkType: 'landing', perPage: 100 },
                })
                .then(({ data }) => {
                    this.domains = data.data;
                    this.domains.unshift({id: null, url: 'Все'});
                })
                .catch(error =>
                    this.$toast.error({
                        title: 'Could not get domain list.',
                        message: error.response.data.message,
                    }),
                );
        },
        getGeoCountries() {
            axios.get('/api/geo/countries')
                .then(({ data }) => (this.geo_countries = data.map(country => country.country_name)))
                .catch(error =>
                    this.$toast.error({
                        title: 'Could not get geo countries list.',
                        message: error.response.data.message,
                    }),
                );
        },
    },
};
</script>
