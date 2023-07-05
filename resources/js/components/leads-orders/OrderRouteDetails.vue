<template>
    <tr>
        <td colspan="12" class="p-0">
            <table class="table w-full table-auto">
                <thead>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        ID
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        GEO
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Status
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Name
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Phone
                    </th>
                    <th
                        v-if="$root.user.branch_id === 16"
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Email
                    </th>
                    <th
                        v-if="$root.user.branch_id === 16"
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Buyer
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Created at
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Deliver at
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Confirmed at
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    >
                        Autologin
                    </th>
                    <th
                        class="px-4 py-2 text-xs font-medium leading-4 tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b border-gray-200"
                    ></th>
                </thead>
                <tbody>
                    <tr
                        v-for="assignment in assignments"
                        :key="assignment.id"
                        class="text-xs"
                    >
                        <td class="px-4 py-2" v-text="assignment.id"></td>
                        <td
                            v-if="assignment.lead.ip_address"
                            class="px-4 py-2"
                            v-text="assignment.lead.ip_address.country_name"
                        ></td>
                        <td v-else class="px-4 py-2">
                            -
                        </td>
                        <td class="px-4 py-2">
                            <span
                                v-if="assignment.status"
                                v-text="assignment.status"></span>
                            <span v-else>Новый<span class="text-white">[null]</span></span>
                        </td>
                        <td class="px-4 py-2">
                            <a
                                :href="`/results/leads/${assignment.lead_id}`"
                                target="_blank"
                                v-text="assignment.lead.fullname"
                            ></a>
                        </td>
                        <td class="px-4 py-2">
                            <span
                                class="mr-2"
                                v-text="assignment.lead.phone"
                            ></span>
                            <fa-icon
                                :icon="
                                    assignment.lead.phone_valid
                                        ? successIcon
                                        : errorIcon
                                "
                                :class="{
                                    'text-green-600':
                                        assignment.lead.phone_valid,
                                    'text-red-600': !assignment.lead.phone_valid
                                }"
                            ></fa-icon>
                        </td>
                        <td
                            v-if="$root.user.branch_id === 16"
                            class="px-4 py-2"
                            v-text="assignment.lead.email"
                        ></td>
                        <td
                            v-if="$root.user.branch_id === 16"
                            class="px-4 py-2"
                        >
                            <span
                                v-if="assignment.lead.user"
                                v-text="assignment.lead.user.name"
                            ></span>
                        </td>
                        <td
                            class="px-4 py-2"
                            v-text="assignment.created_at"
                        ></td>
                        <td
                            class="px-4 py-2"
                            v-text="assignment.deliver_at"
                        ></td>
                        <td class="px-4 py-2">
                            <span
                                v-if="!assignment.delivery_failed"
                                v-text="assignment.confirmed_at"
                            ></span>
                            <span v-else>
                                <fa-icon
                                    :icon="errorIcon"
                                    class="text-red-600"
                                ></fa-icon>
                                <span
                                    class="whitespace-normal"
                                    v-text="assignment.delivery_failed"
                                ></span>
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <fa-icon
                                :icon="
                                    assignment.autologin
                                        ? successIcon
                                        : errorIcon
                                "
                                :class="{
                                    'text-green-600': assignment.autologin,
                                    'text-red-600': !assignment.autologin
                                }"
                            ></fa-icon>
                        </td>
                        <td>
                            <fa-icon
                                v-if="$root.user.role !== 'sales'"
                                :icon="editIcon"
                                class="ml-2 text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                fixed-width
                                @click="
                                    $modal.show('edit-assignment-modal', {
                                        assignment: assignment
                                    })
                                "
                            ></fa-icon>
                            <fa-icon
                                v-if="canMarkUnpayable && assignment.is_payable"
                                :icon="markUnpayableIcon"
                                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                :spin="isBusy"
                                @click="markUnpayable(assignment.id)"
                            >
                            </fa-icon>
                            <fa-icon
                                v-if="
                                    canResend &&
                                        assignment.confirmed_at === null
                                "
                                :icon="['far', 'sync']"
                                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                :spin="isBusy"
                                @click="resend(assignment.id)"
                            >
                            </fa-icon>
                            <fa-icon
                                v-if="canBeTransferred"
                                :icon="transferIcon"
                                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                                fixed-width
                                @click="
                                    $modal.show('transfer-assignment-modal', {
                                        assignment: assignment
                                    })
                                "
                            ></fa-icon>
                            <fa-icon
                                v-if="$root.user.role !== 'sales'"
                                :icon="deleteIcon"
                                class="text-gray-700 cursor-pointer fill-current hover:text-red-700"
                                fixed-width
                                @click="destroy(assignment.id)"
                            ></fa-icon>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</template>
<script>
import {
    faPeopleArrows,
    faTimes,
    faBan,
    faCheckCircle,
    faPencil,
    faHandshakeSlash
} from "@fortawesome/pro-regular-svg-icons";

export default {
    name: "order-route-details",
    props: {
        route: { type: Object, required: true },
        order: { type: Object, required: true }
    },
    data: () => ({
        assignments: [],
        isBusy: false
    }),
    computed: {
        hasAssignments() {
            return this.assignments.length > 0;
        },
        canBeTransferred() {
            return (
                this.$root.user.role === "admin" ||
                this.$root.user.role === "developer" ||
                this.$root.user.role === "support" ||
                this.$root.user.role === "subsupport"
            );
        },
        canResend() {
            return (
                ["admin", "head", "support"].includes(this.$root.user.role) &&
                this.$root.user.id != 230
            );
        },
        canMarkUnpayable() {
            return (
                this.$root.user.branch_id === 16 &&
                (this.$root.user.role === "admin" ||
                    this.$root.user.role === "support")
            );
        },
        transferIcon() {
            return faPeopleArrows;
        },
        deleteIcon() {
            return faTimes;
        },
        errorIcon() {
            return faBan;
        },
        successIcon() {
            return faCheckCircle;
        },
        editIcon() {
            return faPencil;
        },
        markUnpayableIcon() {
            return faHandshakeSlash;
        }
    },
    created() {
        this.load();
        this.listen();
    },
    methods: {
        load() {
            axios
                .get(`/api/leads-order-routes/${this.route.id}/assignments`)
                .then(({ data }) => (this.assignments = data))
                .catch(err =>
                    this.$toast.error({
                        title: "Error",
                        message: "Failed to load assignments"
                    })
                );
        },
        listen() {
            this.$eventHub.$on(
                "assignment-transferred",
                (oldAssignment, newAssignment) => {
                    let index = this.assignments.findIndex(
                        a => a.id === oldAssignment.id
                    );
                    if (index !== -1) {
                        this.assignments.splice(index, 1);
                    }

                    if (newAssignment.route_id === this.route.id) {
                        this.assignments.push(newAssignment);
                    }
                }
            );
            this.$eventHub.$on("assignment-updated", event => {
                let index = this.assignments.findIndex(
                    a => a.id === event.assignment.id
                );
                if (index !== -1) {
                    this.$set(this.assignments, index, event.assignment);
                }
            });
        },
        destroy(id) {
            if (confirm("Уверены что хотите удалить назначение?")) {
                axios
                    .delete(`/api/assignments/${id}`)
                    .then(() => {
                        let index = this.assignments.findIndex(
                            a => a.id === id
                        );
                        if (index !== -1) {
                            this.assignments.splice(index, 1);
                        }

                        this.$toast.success({
                            title: "OK",
                            message: "The assignment was successful deleted."
                        });
                    })
                    .catch(err =>
                        this.$toast.error({
                            title: "Failed to delete the assignment.",
                            message: err.response.data.message
                        })
                    );
            }
        },
        resend(assignmentId) {
            this.isBusy = true;
            axios
                .post(`/api/resend-assignment-delivery-fail/${assignmentId}`)
                .then(response =>
                    this.$toast.success({
                        title: "Ok",
                        message: "Resending has been started."
                    })
                )
                .catch(err =>
                    this.$toast.error({
                        title: "Unable to resend assignment.",
                        message: err.response.data.message
                    })
                )
                .finally(() => (this.isBusy = false));
        },
        markUnpayable(assignmentId) {
            if (
                confirm(
                    "Уверены, что хотите пометить лид как неплатёжеспособный ?"
                )
            ) {
                this.isBusy = true;
                axios
                    .post(`/api/assignments/${assignmentId}/mark-unpayable`)
                    .then(response => {
                        this.$eventHub.$emit("assignment-updated", {
                            assignment: response.data
                        });
                        this.$toast.success({
                            title: "Ok",
                            message: "Assignment marked as unpayable."
                        });
                    })
                    .catch(err =>
                        this.$toast.error({
                            title: "Unable to mark assignment as unpayable.",
                            message: err.response.data.message
                        })
                    )
                    .finally(() => (this.isBusy = false));
            }
        }
    }
};
</script>
