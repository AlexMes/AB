<template>
    <div class="container mx-auto">
        <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
            <h3
                v-if="isUpdating"
                class="text-lg font-medium leading-6 text-gray-900"
                v-text="affiliate.name"
            ></h3>
            <h3 v-else class="text-lg font-medium leading-6 text-gray-900">
                Новый партнер
            </h3>
            <div
                v-if="errors.hasMessage()"
                class="p-3 my-4 text-white bg-red-700 rounded"
            >
                <span v-text="errors.message"></span>
            </div>
            <form @submit="save">
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="name"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Название
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="name"
                                v-model="affiliate.name"
                                type="text"
                                placeholder="Amazing partner Co."
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                required
                                maxlength="50"
                                @input="errors.clear('name')"
                            />
                        </div>
                        <span
                            v-if="errors.has('name')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('name')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="affiliate_cpl"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Оффер
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-xs rounded-md shadow-sm">
                            <mutiselect
                                v-model="affiliate.offer_id"
                                :multiple="false"
                                :options="offers"
                                placeholder="Выберите оффера"
                                track-by="id"
                                label="name"
                            ></mutiselect>
                        </div>
                        <span
                            v-if="errors.has('offer_id')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('offer_id')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="affiliate_cpl"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Источник траффика
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-xs rounded-md shadow-sm">
                            <mutiselect
                                v-model="affiliate.traffic_source_id"
                                :multiple="false"
                                :options="trafficSources"
                                placeholder="Выберите источник траффика"
                                track-by="id"
                                label="name"
                            ></mutiselect>
                        </div>
                        <span
                            v-if="errors.has('traffic_source_id')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('traffic_source_id')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="branch"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Филиал
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-xs rounded-md shadow-sm">
                            <mutiselect
                                id="branch"
                                v-model="affiliate.branch_id"
                                :multiple="false"
                                :options="branches"
                                placeholder="Выберите филиал"
                                track-by="id"
                                label="name"
                            ></mutiselect>
                        </div>
                        <span
                            v-if="errors.has('branch_id')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('branch_id')"
                        ></span>
                    </div>
                </div>
                <!--        <div
                  class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                  <label
                    for="affiliate_cpl"
                    class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                  >
                    CPL
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                      <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                          <span class="text-gray-500 sm:text-sm sm:leading-5">
                            $
                          </span>
                        </div>
                        <input
                          id="affiliate_cpl"
                          v-model="affiliate.cpl"
                          type="number"
                          name="affiliate_cpl"
                          class="block w-full pr-12 form-input pl-7 sm:text-sm sm:leading-5"
                          placeholder="0"
                          aria-describedby="price-currency"
                          @input="errors.clear('cpl')"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <span
                            id="affiliate-cpl"
                            class="text-gray-500 sm:text-sm sm:leading-5"
                          >
                            USD
                          </span>
                        </div>
                      </div>
                    </div>
                    <span
                      v-if="errors.has('cpl')"
                      class="block mt-1 text-sm text-red-600"
                      v-text="errors.get('cpl')"
                    ></span>
                  </div>
                </div>
                <div
                  class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                  <label
                    for="affiliate_cpa"
                    class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                  >
                    СPA
                  </label>
                  <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                      <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                          <span class="text-gray-500 sm:text-sm sm:leading-5">
                            $
                          </span>
                        </div>
                        <input
                          id="affiliate_cpa"
                          v-model="affiliate.cpa"
                          type="number"
                          name="affiliate_cpa"
                          class="block w-full pr-12 form-input pl-7 sm:text-sm sm:leading-5"
                          placeholder="0"
                          aria-describedby="price-currency"
                          @input="errors.clear('cpa')"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                          <span
                            id="price-currency"
                            class="text-gray-500 sm:text-sm sm:leading-5"
                          >
                            USD
                          </span>
                        </div>
                      </div>
                    </div>
                    <span
                      v-if="errors.has('cpa')"
                      class="block mt-1 text-sm text-red-600"
                      v-text="errors.get('cpa')"
                    ></span>
                  </div>
                </div>-->
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Постбек
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg">
                            <input
                                id="postback"
                                v-model="affiliate.postback"
                                type="text"
                                placeholder="https://somewhere.com"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                required
                                @input="errors.clear('postback')"
                            />
                        </div>
                        <span
                            v-if="errors.has('postback')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('postback')"
                        ></span>
                    </div>
                </div>
                <div class="flex justify-end w-full pt-4 mt-6 border-t">
                    <button
                        type="reset"
                        class="mx-2 button btn-secondary"
                        @click="
                            $router.push({
                                name: 'affiliates.show',
                                params: { id: id }
                            })
                        "
                    >
                        Отмена
                    </button>
                    <button
                        type="submit"
                        class="mx-2 button btn-primary"
                        :disabled="isBusy"
                        @click.prevent="save"
                    >
                        <span v-if="isBusy">
                            <fa-icon
                                :icon="['far', 'spinner']"
                                class="fill-current"
                                spin
                                fixed-width
                            ></fa-icon>
                            Сохранение</span
                        >
                        <span v-else>Сохранить</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import ErrorBag from "../../../utilities/ErrorBag";
export default {
    name: "affiliates-form",
    props: {
        id: {
            type: [Number, String],
            required: false,
            default: null
        }
    },
    data: () => ({
        isBusy: false,
        affiliate: {
            name: null,
            /*cpl: 0.00,
            cpa: 0.00,*/
            offer_id: null,
            traffic_source_id: null,
            branch_id: null,
            postback: null
        },
        offers: [],
        errors: new ErrorBag(),
        trafficSources: [],
        branches: []
    }),
    computed: {
        isUpdating() {
            return this.id !== null && this.id !== undefined;
        }
    },
    created() {
        this.boot();
    },
    methods: {
        boot() {
            if (this.isUpdating) {
                this.load();
            }
            this.getOffers();
            this.getTrafficSources();
            this.getBranches();
        },
        load() {
            axios
                .get(`/api/affiliates/${this.id}`)
                .then(r => {
                    this.affiliate = r.data;
                    this.affiliate.offer_id = r.data.offer;
                    this.affiliate.branch_id = r.data.branch;
                    this.affiliate.traffic_source_id = r.data.traffic_source;
                })
                .catch(err => {
                    this.$toast.error({
                        title: "Не удалось загрузить affiliate",
                        message: err.response.data.message
                    });
                });
        },
        save() {
            this.isUpdating ? this.update() : this.create();
        },
        substitution() {
            this.affiliate.branch_id = this.affiliate.branch_id
                ? this.affiliate.branch_id.id
                : null;
            this.affiliate.offer_id = this.affiliate.offer_id
                ? this.affiliate.offer_id.id
                : null;
            this.affiliate.traffic_source_id = this.affiliate.traffic_source_id
                ? this.affiliate.traffic_source_id.id
                : null;
        },
        create() {
            this.isBusy = true;
            this.substitution();
            axios
                .post("/api/affiliates/", this.affiliate)
                .then(r => {
                    this.$router.push({ name: "affiliates.index" });
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        return this.errors.fromResponse(err);
                    }
                    this.$toast.error({
                        title: "Не удалось сохранить affiliate.",
                        message: err.response.data.message
                    });
                })
                .finally(() => (this.isBusy = false));
        },
        update() {
            this.isBusy = true;
            this.substitution();
            axios
                .put(`/api/affiliates/${this.affiliate.id}`, this.affiliate)
                .then(r =>
                    this.$router.push({
                        name: "affiliates.show",
                        params: { id: r.data.id }
                    })
                )
                .catch(err => {
                    if (err.response.status === 422) {
                        return this.errors.fromResponse(err);
                    }
                    this.$toast.error({
                        title: "Не удалось обновить affiliate",
                        message: err.response.data.message
                    });
                })
                .finally(() => (this.isBusy = false));
        },
        getOffers() {
            axios
                .get("/api/offers")
                .then(r => (this.offers = r.data))
                .catch(err =>
                    this.$toast.error({
                        title: "Не удалось загрузить офферы.",
                        message: err.response.data.message
                    })
                );
        },
        getTrafficSources() {
            axios
                .get("/api/traffic-sources")
                .then(r => (this.trafficSources = r.data))
                .catch(err =>
                    this.$toast.error({
                        title: "Не удалось загрузить источники трафика.",
                        message: err.response.data.message
                    })
                );
        },
        getBranches() {
            axios
                .get("/api/branches")
                .then(r => (this.branches = r.data))
                .catch(err =>
                    this.$toast.error({
                        title: "Не удалось загрузить филиалы.",
                        message: err.response.data.message
                    })
                );
        }
    }
};
</script>
