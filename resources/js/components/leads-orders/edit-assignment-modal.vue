<template>
    <modal
        name="edit-assignment-modal"
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
                <label class="flex w-full mb-2 font-semibold">Статус</label>
                <mutiselect
                    v-model="assignment.status"
                    :show-labels="false"
                    :allow-empty="false"
                    :options="statuses"
                    :loading="loading.statuses"
                    placeholder="Выберите статус"
                    @input="errors.clear('status')"
                ></mutiselect>
                <span
                    v-if="errors.has('status')"
                    class="mt-1 text-sm text-red-600"
                    v-text="errors.get('status')"
                ></span>
            </div>
            <div class="flex flex-col w-full mb-4">
                <label for="deposit_sum" class="flex w-full mb-2 font-semibold"
                    >Доход</label
                >
                <input
                    id="benefit"
                    v-model="assignment.benefit"
                    type="number"
                    placeholder="999"
                    class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                />
                <span
                    v-if="errors.has('benefit')"
                    class="mt-1 text-sm text-red-600"
                    v-text="errors.get('benefit')"
                ></span>
            </div>
            <div class="flex w-full">
                <button
                    class="mr-2 button btn-primary"
                    :disabled="isBusy"
                    @click="update"
                >
                    Сохранить
                </button>
                <button
                    class="button btn-secondary"
                    @click="$modal.hide('edit-assignment-modal')"
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
    name: "edit-assignment-modal",
    props: {
        /*order: {
      type: Object,
      required: true,
    },*/
    },
    data: () => ({
        assignment: {
            status: null,
            benefit: null
        },
        statuses: [],
        loading: {
            statuses: false
        },
        isBusy: false,
        errors: new ErrorBag()
    }),
    computed: {
        hasStatuses() {
            return this.statuses.length > 0;
        }
    },
    methods: {
        beforeOpen(event) {
            this.assignment.id = event.params.assignment.id;
            this.assignment.status = event.params.assignment.status;
            this.assignment.benefit = event.params.assignment.actual_benefit;

            this.errors.clear();

            if (!this.hasStatuses) {
                this.load();
            }
        },
        load() {
            this.loading.statuses = true;
            axios
                .get("/api/statuses")
                .then(response => {
                    this.statuses = response.data.map(status => status.name);
                })
                .catch(e =>
                    this.$toast.error({
                        title: "Не удалось загрузить статусы CRM.",
                        message: e.data.message
                    })
                )
                .finally(() => (this.loading.statuses = false));
        },
        update() {
            this.isBusy = true;
            axios
                .put(`/api/assignments/${this.assignment.id}`, {
                    status: this.assignment.status,
                    benefit: this.assignment.benefit
                })
                .then(response => {
                    this.$modal.hide("edit-assignment-modal");
                    this.$toast.success({
                        title: "OK",
                        message: "Лид обновлён."
                    });
                    this.errors = new ErrorBag();
                    this.$eventHub.$emit("assignment-updated", {
                        assignment: response.data
                    });
                })
                .catch(e => {
                    this.errors.fromResponse(e);
                    this.$toast.error({
                        title: "Ошибка",
                        message: "Не удалось обновить лид."
                    });
                })
                .finally(() => (this.isBusy = false));
        }
    }
};
</script>
