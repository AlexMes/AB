<template>
    <div class="flex flex-col">
        <div class="flex flex-row justify-between w-full mb-8">
            <div class="flex flex-1">
                <input
                    type="search"
                    class="w-full pb-1 mr-5 bg-transparent border-b outline-none border-grey-500 text-grey-700"
                    placeholder="Поиск офисов"
                    maxlength="50"
                    @input="search"
                />
            </div>
            <router-link
                v-if="$root.user.role !== 'subsupport'"
                :to="{ name: 'offices.create' }"
                class="button btn-primary"
            >
                Добавить
            </router-link>
        </div>
        <div v-if="hasOffices" class="flex flex-col w-full bg-white shadow">
            <office-list-item
                v-for="office in offices"
                :key="office.id"
                :office="office"
            ></office-list-item>
        </div>
        <div v-else class="p-4 text-center">
            <h2>Офисов не найдено</h2>
        </div>
    </div>
</template>

<script>
import OfficeListItem from "../../../components/settings/office-list-item";
export default {
    name: "offices-index",
    components: { OfficeListItem },
    data: () => ({
        offices: {},
        response: {}
    }),
    computed: {
        hasOffices() {
            return this.offices !== undefined && this.offices.length > 0;
        }
    },
    created() {
        this.load();
    },
    methods: {
        load(search = null) {
            axios
                .get("/api/offices", { params: { search: search } })
                .then(response => {
                    this.offices = response.data;
                })
                .catch(err => {
                    this.$toast.error({
                        title: "Не удалось загрузить офисы.",
                        message: err.response.data.message
                    });
                });
        },
        search(event) {
            this.load(event.target.value === "" ? null : event.target.value);
        }
    }
};
</script>
