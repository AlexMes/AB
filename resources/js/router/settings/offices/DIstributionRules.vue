<template>
    <div class="w-full">
        <div class="shadow">
            <div
                class="flex justify-end w-full p-3 space-x-2 bg-white border-b"
            >
                <div class="w-1/4">
                    <mutiselect
                        v-model="offer"
                        :show-labels="false"
                        :options="allOffers"
                        placeholder="Выберите офер"
                        track-by="id"
                        label="name"
                    ></mutiselect>
                </div>
                <div class="w-1/4">
                    <mutiselect
                        v-model="country"
                        :show-labels="false"
                        :options="allCountries"
                        placeholder="Выберите страну"
                        track-by="country"
                        label="country_name"
                    ></mutiselect>
                </div>
                <div class="flex items-center w-auto">
                    <button
                        class="flex items-center button btn-primary"
                        @click="add"
                    >
                        <fa-icon
                            :icon="['far', 'plus']"
                            class="mr-2 fill-current"
                        ></fa-icon>
                        Добавить
                    </button>
                </div>
            </div>
        </div>
        <div v-if="hasRules">
            <div
                class="flex w-full overflow-x-auto overflow-y-hidden bg-white shadow no-last-border"
            >
                <table class="relative table w-full table-auto">
                    <thead
                        class="sticky w-full font-semibold text-gray-700 uppercase bg-gray-300"
                    >
                        <tr>
                            <th class="w-4/12 px-2 px-6 py-3">
                                Офер
                            </th>
                            <th class="w-4/12 px-6 py-3">
                                Страна
                            </th>
                            <th class="w-3/12 px-6 py-3">
                                Разрешено
                            </th>
                            <th class="w-1/12 px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="w-full">
                        <distribution-rule-list-item
                            v-for="(rule, index) in rules"
                            :key="index"
                            :rule="rule"
                            @deleted="remove"
                            @updated="update"
                        ></distribution-rule-list-item>
                    </tbody>
                </table>
            </div>
        </div>
        <div
            v-else
            class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6"
        >
            <p>Пусто</p>
        </div>
    </div>
</template>

<script>
import DistributionRuleListItem from "../../../components/settings/distribution-rule-list-item";
export default {
    name: "offices-distribution-rules",
    components: { DistributionRuleListItem },
    props: {
        id: {
            type: [Number, String],
            required: true
        }
    },
    data: () => ({
        rules: [],
        allCountries: [],
        allOffers: [],
        country: null,
        offer: null
    }),
    computed: {
        hasRules() {
            return this.rules.length > 0;
        }
    },
    created() {
        this.load();
        this.loadAllCountries();
        this.loadAllOffers();
    },
    methods: {
        load() {
            axios
                .get("/api/distribution-rules", {
                    params: { office_id: this.id }
                })
                .then(r => (this.rules = r.data))
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить гео правила офиса.",
                        message: e.response.data.message
                    })
                );
        },
        loadAllCountries() {
            axios
                .get("/api/geo/countries")
                .then(r => (this.allCountries = r.data))
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить список стран.",
                        message: e.response.data.message
                    })
                );
        },
        loadAllOffers() {
            axios
                .get("/api/offers")
                .then(r => (this.allOffers = r.data))
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить список оферов.",
                        message: e.response.data.message
                    })
                );
        },
        update(event) {
            const index = this.rules.findIndex(
                rule => rule.id === event.rule.id
            );
            if (index !== -1) {
                this.$set(this.rules, index, event.rule);
            }
        },
        add() {
            if (this.country !== null) {
                axios
                    .post("/api/distribution-rules", {
                        office_id: this.id,
                        offer_id: !this.offer ? null : this.offer.id,
                        geo: this.country.country,
                        country_name: this.country.country_name
                    })
                    .then(r => {
                        if (
                            this.rules.findIndex(
                                rule => rule.id === r.data.id
                            ) === -1
                        ) {
                            this.rules.push(r.data);
                        }
                        this.country = null;
                        this.offer = null;
                    })
                    .catch(err =>
                        this.$toast.error({
                            title: "Не удалось добавить правило.",
                            message: err.response.data.message
                        })
                    );
            }
        },
        remove(event) {
            const index = this.rules.findIndex(
                rule => rule.id === event.rule.id
            );
            if (index !== -1) {
                this.rules.splice(index, 1);
            }
        }
    }
};
</script>
