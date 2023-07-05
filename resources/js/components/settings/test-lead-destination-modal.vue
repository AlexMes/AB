<template>
    <modal
        name="test-lead-destination-modal"
        height="auto"
        :adaptive="true"
        :styles="{ overflow: 'visible' }"
        @before-open="beforeOpen"
    >
        <div class="flex flex-col w-full p-6">
            <div
                v-if="errors.hasMessage()"
                class="p-3 mb-2 text-white bg-red-700 rounded"
            >
                <span v-text="errors.message"></span>
            </div>
            <div class="flex flex-col w-full mb-4">
                <label class="flex w-full mb-2 font-semibold">Офер</label>
                <mutiselect
                    v-model="offer"
                    :show-labels="false"
                    :multiple="false"
                    :options="offers"
                    placeholder="Выберите офер"
                    track-by="id"
                    label="name"
                    @input="errors.clear('offer_id')"
                ></mutiselect>
                <span
                    v-if="errors.has('offer_id')"
                    class="mt-1 text-sm text-red-600"
                    v-text="errors.get('offer_id')"
                ></span>
            </div>
            <div class="flex flex-col w-full mb-4">
                <label class="flex w-full mb-2 font-semibold">ГЕО</label>
                <mutiselect
                    v-model="geo"
                    :show-labels="false"
                    :multiple="false"
                    :options="countries"
                    placeholder="Выберите страну"
                    track-by="country"
                    label="country_name"
                    @input="errors.clear('geo')"
                ></mutiselect>
                <span
                    v-if="errors.has('geo')"
                    class="mt-1 text-sm text-red-600"
                    v-text="errors.get('geo')"
                ></span>
            </div>
            <div
                v-if="destination.test_payload"
                class="flex flex-col w-full mb-4"
            >
                <div class="mb-2">
                    Email: <span v-text="destination.test_payload.email"></span>
                </div>
                <div
                    v-if="destination.test_payload.response.delivered"
                    class="p-3 mb-2 text-white bg-green-500 rounded"
                >
                    <div class="mb-2">
                        External ID:
                        <span
                            v-text="
                                destination.test_payload.response.external_id
                            "
                        ></span>
                    </div>
                    <div>
                        Redirect URL:
                        <span
                            v-text="
                                destination.test_payload.response.redirect_url
                            "
                        ></span>
                    </div>
                </div>
                <div v-else class="p-3 mb-2 text-white bg-red-700 rounded h-24 md:h-48 lg:h-96 overflow-y-scroll">
                    <span
                        v-text="destination.test_payload.response.error"
                    ></span>
                </div>
            </div>
            <div class="flex w-full">
                <button
                    class="mr-2 button btn-primary"
                    :disabled="isBusy"
                    @click="makeTest"
                >
                    Тест
                </button>
                <button
                    class="button btn-secondary"
                    @click="$modal.hide('test-lead-destination-modal')"
                >
                    Отмена
                </button>
            </div>
        </div>
    </modal>
</template>

<script>
import ErrorBag from "../../utilities/ErrorBag";
export default {
    name: "test-lead-destination-modal",
    data: () => ({
        destination: {},
        geo: null,
        offer: null,
        countries: [],
        offers: [],
        isBusy: false,
        errors: new ErrorBag()
    }),
    computed: {
        hasOffers() {
            return this.offers.length > 0;
        },
        hasCountries() {
            return this.countries.length > 0;
        }
    },
    methods: {
        beforeOpen(event) {
            this.destination = event.params.destination;
            if (!this.hasOffers) {
                this.loadOffers();
            }
            if (!this.hasCountries) {
                this.loadCountries();
            }
        },
        loadOffers() {
            axios
                .get("/api/offers")
                .then(response => {
                    this.offers = response.data;
                })
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить оферы.",
                        message: e.data.message
                    })
                );
        },
        loadCountries() {
            axios
                .get("/api/geo/countries")
                .then(response => {
                    this.countries = response.data;
                })
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить страны.",
                        message: e.data.message
                    })
                );
        },
        makeTest() {
            this.isBusy = true;
            axios
                .post(`/api/leads-destinations/${this.destination.id}/test`, {
                    offer_id: this.offer === null ? null : this.offer.id,
                    geo: !!this.geo ? this.geo.country : null
                })
                .then(response => {
                    this.$toast.success({
                        title: "OK",
                        message: "Тест доставки выполнен."
                    });
                    this.errors = new ErrorBag();
                    this.$eventHub.$emit(
                        "destination-tested",
                        this.destination,
                        response.data
                    );
                    this.destination = response.data;
                })
                .catch(e => {
                    this.errors.fromResponse(e);
                    this.$toast.error({
                        title: "Ошибка",
                        message: "Не удалось выполнить тест доставки."
                    });
                })
                .finally(() => (this.isBusy = false));
        }
    }
};
</script>
