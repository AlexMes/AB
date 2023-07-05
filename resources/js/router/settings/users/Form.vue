<template>
    <div class="container mx-auto">
        <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
            <h3
                v-if="isUpdating"
                class="text-lg font-medium leading-6 text-gray-900"
                v-text="user.name"
            ></h3>
            <div
                v-if="errors.hasMessage()"
                class="p-3 my-4 text-white bg-red-700 rounded"
            >
                <span v-text="errors.message"></span>
            </div>
            <form @submit.prevent="save">
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="email"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Имя
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="name"
                                v-model="user.name"
                                type="text"
                                placeholder="John Doe"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                required
                                maxlength="50"
                            />
                        </div>
                        <span
                            v-if="errors.has('name')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('name')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="email"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Email
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="email"
                                v-model="user.email"
                                type="email"
                                placeholder="user@domain.tld"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                                required
                            />
                        </div>
                        <span
                            v-if="errors.has('email')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('email')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="tg_id"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Telegram ID
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="tg_id"
                                v-model="user.telegram_id"
                                type="text"
                                placeholder="9379992"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                            />
                        </div>
                        <span
                            v-if="errors.has('telegram_id')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('telegram_id')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="binom_tag"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Binom tag
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="binom_tag"
                                v-model="user.binomTag"
                                type="text"
                                placeholder="-someone-"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                            />
                        </div>
                        <span
                            v-if="errors.has('binomTag')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('binomTag')"
                        ></span>
                    </div>
                </div>
                <div
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="accesss"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Доступ
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-xs rounded-md shadow-sm">
                            <select
                                id="accesss"
                                v-model="user.role"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                            >
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.id"
                                    v-text="role.name"
                                ></option>
                            </select>
                        </div>
                        <span
                            v-if="errors.has('role')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('role')"
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
                                v-model="user.branch_id"
                                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                            >
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
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('branch_id')"
                        ></span>
                    </div>
                </div>
                <div
                    v-if="!isUpdating"
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="password"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Пароль
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="password"
                                v-model="user.password"
                                type="password"
                                placeholder="******"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                            />
                        </div>
                        <span
                            v-if="errors.has('password')"
                            class="block text-red-600 text-sm mt-1"
                            v-text="errors.get('password')"
                        ></span>
                    </div>
                </div>
                <div
                    v-if="!isUpdating"
                    class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
                >
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
                    >
                        Подтвердите пароль
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="max-w-lg rounded-md shadow-sm">
                            <input
                                id="password_confirmation"
                                v-model="user.password_confirmation"
                                type="password"
                                placeholder="******"
                                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end w-full mt-5">
                    <button type="reset" class="mx-2 button btn-secondary">
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
        <form
            v-if="isUpdating"
            class="flex flex-col p-6 mt-6 bg-white shadow"
            @submit.prevent="resetPassword"
        >
            <h2 class="mb-5 text-lg font-medium leading-6 text-gray-900">
                Сброс пароля
            </h2>
            <div class="flex-col w-full my-2 mr-4">
                <label class="flex w-full font-medium text-gray-700">
                    Новый пароль</label
                >
                <input
                    v-model="passwords.password"
                    type="password"
                    placeholder="**********"
                    class="w-full px-1 py-2 my-2 text-gray-700 placeholder-gray-400 border-b "
                    required
                    @input="resetErrors.clear('password')"
                />
                <span
                    v-if="resetErrors.has('password')"
                    class="mt-1 text-sm text-red-600"
                    v-text="resetErrors.get('password')"
                ></span>
            </div>
            <div class="flex-col w-full my-2">
                <label class="flex w-full font-medium text-gray-700"
                    >Подтвердите пароль</label
                >
                <input
                    v-model="passwords.password_confirmation"
                    placeholder="**********"
                    type="password"
                    class="w-full px-1 py-2 my-2 text-gray-700 placeholder-gray-400 border-b"
                    required
                    @input="resetErrors.clear('password_confirmation')"
                />
                <span
                    v-if="resetErrors.has('password_confirmation')"
                    class="mt-1 text-sm text-red-600"
                    v-text="resetErrors.get('password_confirmation')"
                ></span>
            </div>
            <div class="flex justify-end w-full mt-5">
                <button type="reset" class="mx-2 button btn-secondary">
                    Отмена
                </button>
                <button
                    type="submit"
                    class="mx-2 button btn-primary"
                    @click.prevent="resetPassword"
                >
                    Сбросить пароль
                </button>
            </div>
        </form>
        <div
            v-if="
                isUpdating &&
                    ($root.user.id === 1 || $root.user.role === 'developer')
            "
            class="px-4 py-5 mt-6 bg-white border-b border-gray-200 shadow sm:px-6"
        >
            <google-tfa :id="id"></google-tfa>
        </div>
    </div>
</template>

<script>
import ErrorBag from "../../../utilities/ErrorBag";
import GoogleTfa from "../../../components/users/google-tfa";
export default {
    name: "users-form",
    components: { GoogleTfa },
    props: {
        id: {
            type: [String, Number],
            required: false,
            default: null
        }
    },
    data: () => ({
        isBusy: false,
        user: {
            email: null,
            name: null,
            role: "buyer",
            password: null,
            password_confirmation: null,
            telegram_id: null,
            showFbFields: false
        },
        passwords: {
            password: null,
            password_confirmation: null
        },
        errors: new ErrorBag(),
        resetErrors: new ErrorBag(),
        roles: [
            { id: "buyer", name: "Баер" },
            { id: "teamlead", name: "Тимлид" },
            { id: "head", name: "Office head" },
            { id: "admin", name: "Администратор" },
            { id: "farmer", name: "Фармер" },
            { id: "financier", name: "Финансист" },
            { id: "designer", name: "Дизайнер" },
            { id: "verifier", name: "Верифаер" },
            { id: "gamble-admin", name: "Gamble Admin" },
            { id: "gambler", name: "Gable Buyer" },
            { id: "support", name: "Support" },
            { id: "developer", name: "Разработчик" },
            { id: "subsupport", name: "Support staging" },
            { id: "sales", name: "Sales" },
        ],
        branches: []
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
            axios
                .get("/api/branches")
                .then(({ data }) => (this.branches = data))
                .catch(err =>
                    this.$toast.error({
                        title: "Something wrong is happened.",
                        message: err.response.statusText
                    })
                );
            if (this.isUpdating) {
                this.load();
            }
        },
        load() {
            axios
                .get(`/api/users/${this.id}`)
                .then(r => (this.user = r.data))
                .catch(err => {
                    this.$toast.error({
                        title: "Не удалось загрузить баера",
                        message: err.response.data.message
                    });
                });
        },
        save() {
            this.isUpdating ? this.update() : this.create();
        },
        create() {
            this.isBusy = true;
            axios
                .post("/api/users/", this.user)
                .then(r => {
                    this.$router.push({
                        name: "users.show",
                        params: { id: r.data.id }
                    });
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        return this.errors.fromResponse(err);
                    }
                    this.$toast.error({
                        title: "Не удалось сохранить баера",
                        message: err.response.data.message
                    });
                })
                .finally(() => (this.isBusy = false));
        },
        update() {
            this.isBusy = true;
            axios
                .put(`/api/users/${this.user.id}`, this.user)
                .then(r =>
                    this.$router.push({
                        name: "users.profiles",
                        params: { id: r.data.id }
                    })
                )
                .catch(err => {
                    if (err.response.status === 422) {
                        return this.errors.fromResponse(err);
                    }
                    this.$toast.error({
                        title: "Не удалось обновить баера",
                        message: err.response.data.message
                    });
                })
                .finally(() => (this.isBusy = false));
        },
        resetPassword() {
            axios
                .put(
                    `/api/users/${this.user.id}/reset-password`,
                    this.passwords
                )
                .then(() =>
                    this.$router.push({
                        name: "users.profiles",
                        params: { id: this.id }
                    })
                )
                .catch(errors => {
                    if (errors.response.status === 422) {
                        return this.errors.fromResponse(errors);
                    }
                    this.$toast.error({
                        title: "Не удалось сбросить пароль",
                        message: errors.response.data.message
                    });
                });
        }
    }
};
</script>

<style scoped></style>
