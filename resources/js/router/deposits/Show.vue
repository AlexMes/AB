<template>
    <div>
        <div class="w-full h-auto mb-8 bg-white rounded shadow">
            <div v-if="deposit" class="w-full h-auto mb-8">
                <div
                    class="flex flex-row items-center justify-between px-4 py-2 border-b"
                >
                    <div
                        class="py-2 text-2xl font-bold text-gray-700"
                        v-text="`Депозит #${deposit.id}`"
                    ></div>
                    <div>
                        <router-link
                            v-if="['admin', 'support'].includes($root.user.role)"
                            :to="{ name: 'deposits.update' }"
                            class="button btn-primary"
                        >
                            Редактировать
                        </router-link>
                    </div>
                </div>
                <div
                    class="flex flex-col flex-1 text-gray-700 bg-white shadow no-last-border"
                >
                    <div
                        v-if="deposit.lead_return_date"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Дата лида</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.lead_return_date"
                            ></span>
                        </div>
                    </div>
                    <div v-if="deposit.date" class="flex flex-row p-3 border-b">
                        <div class="flex w-1/4">
                            <strong>Дата</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.date"
                            ></span>
                        </div>
                    </div>
                    <div
                        v-if="deposit.sum && $root.user.role !== 'support'"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Сумма</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.sum"
                            ></span>
                        </div>
                    </div>
                    <div v-if="deposit.city" class="flex flex-row p-3 border-b">
                        <div class="flex w-1/4">
                            <strong>Город</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.city"
                            ></span>
                        </div>
                    </div>
                    <div
                        v-if="deposit.profession"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Профессия</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.profession"
                            ></span>
                        </div>
                    </div>
                    <div
                        v-if="deposit.manager"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Менеджер</strong>
                        </div>
                        <div class="flex w-3/4">
                            <a
                                target="_blank"
                                rel="noopener"
                                :href="
                                    `https://docs.google.com/spreadsheets/d/${deposit.manager.spreadsheet_id}`
                                "
                                v-text="deposit.manager.name"
                            ></a>
                        </div>
                    </div>
                    <div
                        v-if="deposit.phone && $root.user.role === 'admin'"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Телефон</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.phone"
                            ></span>
                        </div>
                    </div>
                    <div
                        v-if="deposit.account"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Аккаунт</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.account.name"
                            ></span>
                        </div>
                    </div>
                    <div v-if="deposit.user" class="flex flex-row p-3 border-b">
                        <div class="flex w-1/4">
                            <strong>Баер</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.user.name"
                            ></span>
                        </div>
                    </div>
                    <div
                        v-if="deposit.office"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Офис</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.office.name"
                            ></span>
                        </div>
                    </div>
                    <div
                        v-if="deposit.offer"
                        class="flex flex-row p-3 border-b"
                    >
                        <div class="flex w-1/4">
                            <strong>Оффер</strong>
                        </div>
                        <div class="flex w-3/4">
                            <span
                                class="font-normal"
                                v-text="deposit.offer.name"
                            ></span>
                        </div>
                    </div>
                    <div v-if="deposit.lead" class="flex flex-row p-3 border-b">
                        <div class="flex w-1/4">
                            <strong>Лид</strong>
                        </div>
                        <div class="flex w-3/4">
                            <router-link
                                v-if="$root.user.role !== 'support'"
                                :to="{
                                    name: 'leads.show',
                                    params: { id: deposit.lead_id }
                                }"
                                class="hover:text-teal-700"
                                v-text="deposit.lead.firstname"
                            ></router-link>
                            <span v-else v-text="deposit.lead.firstname"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "deposit-show",
    props: {
        id: {
            type: [String, Number],
            default: null
        }
    },
    data() {
        return {
            isLoading: false,
            deposit: {}
        };
    },
    created() {
        this.boot();
    },
    methods: {
        boot() {
            axios
                .get(`/api/deposits/${this.id}`)
                .then(r => (this.deposit = r.data))
                .catch(e => {
                    this.$toast.error({
                        title: "Не удалось загрузить депозит.",
                        message: e.response.data.message
                    });
                });
        }
    }
};
</script>
