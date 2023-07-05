<template>
    <tr>
        <td v-text="affiliate.id"></td>
        <td class="font-semibold" v-text="affiliate.name"></td>
        <td v-if="affiliate.offer" v-text="affiliate.offer.name"></td>
        <td v-else>
            -
        </td>
        <td
            v-if="affiliate.traffic_source"
            v-text="affiliate.traffic_source.name"
        ></td>
        <td v-else>
            -
        </td>
        <td v-if="affiliate.branch" v-text="affiliate.branch.name"></td>
        <td v-else>
            -
        </td>
        <td>
            <div
                class="w-4 h-4 border-0 rounded-full"
                :class="[affiliate.postback ? 'bg-green-500' : 'bg-red-500']"
            ></div>
        </td>
        <td>
            <router-link
                :to="`affiliates/${affiliate.id}`"
                class="font-semibold text-gray-700 hover:text-teal-700"
            >
                <fa-icon
                    :icon="icon"
                    class="text-gray-500 fill-current hover:text-teal-600"
                ></fa-icon>
            </router-link>
        </td>
    </tr>
</template>

<script>
import { faEdit } from "@fortawesome/pro-regular-svg-icons";

export default {
    name: "affiliate-list-item",
    props: {
        affiliate: {
            required: true,
            type: Object
        }
    },
    computed: {
        icon: () => faEdit
    }
};
</script>

<style scoped>
td {
    @apply px-6;
    @apply py-4;
    @apply whitespace-no-wrap;
    @apply text-sm;
    @apply leading-5;
    @apply text-gray-500;
}
</style>
