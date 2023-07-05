<template>
    <div class="container mx-auto">
        <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
            <h3
                v-if="isUpdating"
                class="text-lg font-medium leading-6 text-gray-900"
                v-text="offer.name"
            ></h3>
            <h3 v-else class="text-lg font-medium leading-6 text-gray-900">
                Новый оффер
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
                        Имя
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="name"
                                v-model="offer.name"
                                type="text"
                                placeholder="Offer name"
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
                        for="vertical"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Вертикаль
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-xs rounded-md shadow-sm">
                            <select
                                id="vertical"
                                v-model="offer.vertical"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                                @change="errors.clear('vertical')"
                            >
                                <option :value="null">
                                    Не выбрано
                                </option>
                                <option
                                    v-for="vertical in verticals"
                                    :key="vertical.id"
                                    :value="vertical.id"
                                    v-text="vertical.name"
                                ></option>
                            </select>
                        </div>
                        <span
                            v-if="errors.has('vertical')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('vertical')"
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
                            <select
                                id="branch"
                                v-model="offer.branch_id"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                            >
                                <option :value="null">
                                    Не выбран
                                </option>
                                <option
                                    v-for="branch in branches"
                                    :key="branch.id"
                                    :value="branch.id"
                                    v-text="branch.name"
                                ></option>
                            </select>
                        </div>
                        <span
                            v-if="errors.has('branch_id')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('branch_id')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="description"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Описание
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <textarea
                                id="description"
                                v-model="offer.description"
                                placeholder=""
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                rows="5"
                                @input="errors.clear('description')"
                            ></textarea>
                        </div>
                        <span
                            v-if="errors.has('description')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('description')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="pb_lead"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Postback Lead
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="pb_lead"
                                v-model="offer.pb_lead"
                                type="text"
                                placeholder="URL for lead postback. app external id gonna be added at the end"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                @input="errors.clear('pb_lead')"
                            />
                        </div>
                        <span
                            v-if="errors.has('pb_lead')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('pb_lead')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="pb_sale"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Postback FTD
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="pb_sale"
                                v-model="offer.pb_sale"
                                type="text"
                                placeholder="URL for lead postback. app external id gonna be added at the end"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                @input="errors.clear('pb_sale')"
                            />
                        </div>
                        <span
                            v-if="errors.has('pb_sale')"
                            class="block mt-1 text-sm text-red-600"
                            v-text="errors.get('pb_sale')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Генерация почты
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg">
                            <toggle v-model="offer.generate_email"></toggle>
                        </div>
                        <span
                            v-if="errors.has('generate_email')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('generate_email')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Разрешить дубли
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg">
                            <toggle v-model="offer.allow_duplicates"></toggle>
                        </div>
                        <span
                            v-if="errors.has('allow_duplicates')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('allow_duplicates')"
                        ></span>
                    </div>
                </div>
                <div class="flex justify-end w-full pt-4 mt-6 border-t sm:mt-5">
                    <button
                        type="reset"
                        class="mx-2 button btn-secondary"
                        @click="cancel"
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
    name: "offers-form",
    props: {
        id: {
            type: [String, Number],
            required: false,
            default: null
        }
    },
    data: () => ({
        isBusy: false,
        offer: {
            name: null,
            vertical: null,
            branch_id: null,
            description: null,
            generate_email: true,
            allow_duplicates: false,
        },
        verticals: [
            { id: "Burj", name: "Burj" },
            { id: "Crypt", name: "Crypt" }
        ],
        branches: [],
        errors: new ErrorBag()
    }),
    computed: {
        isUpdating() {
            return this.$props.id !== null;
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
            this.loadBranches();
        },
        load() {
            axios
                .get(`/api/offers/${this.id}`)
                .then(r => (this.offer = r.data))
                .catch(err =>
                    this.$toast.error({
                        title: "Не удалось загрузить офер.",
                        message: err.response.data.message
                    })
                );
        },
        loadBranches() {
            axios
                .get("/api/branches")
                .then(({ data }) => (this.branches = data))
                .catch(err =>
                    this.$toast.error({
                        title: "Something wrong is happened.",
                        message: err.response.statusText
                    })
                );
        },
        save() {
            this.isUpdating ? this.update() : this.create();
        },
        cancel() {
            this.isUpdating
                ? this.$router.push({
                      name: "offers.allowed-users",
                      params: { id: this.id }
                  })
                : this.$router.push({ name: "offers.index" });
        },
        create() {
            this.isBusy = true;
            axios
                .post("/api/offers/", this.offer)
                .then(r => {
                    this.$router.push({
                        name: "offers.allowed-users",
                        params: { id: r.data.id }
                    });
                })
                .catch(err => {
                    if (err.response.status) {
                        return this.errors.fromResponse(err);
                    }
                    this.$toast.error({
                        title: "Не удалось сохранить офер.",
                        message: err.response.data.message
                    });
                })
                .finally(() => (this.isBusy = false));
        },
        update() {
            this.isBusy = true;
            axios
                .put(`/api/offers/${this.offer.id}`, this.offer)
                .then(r =>
                    this.$router.push({
                        name: "offers.allowed-users",
                        params: { id: r.data.id }
                    })
                )
                .catch(err => {
                    if (err.response.status) {
                        return this.errors.fromResponse(err);
                    }
                    this.$toast.error({
                        title: "Не удалось обновить офер.",
                        message: err.response.data.message
                    });
                })
                .finally(() => (this.isBusy = false));
        }
    }
};
</script>
