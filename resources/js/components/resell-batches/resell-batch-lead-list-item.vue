<template>
  <tr
    class="items-center w-full px-3 border-b hover:bg-gray-100"
    :class="lead.is_duplicate ? 'bg-red-100' : 'bg-white'"
  >
    <td v-if="isEditing">
      <input
        v-model="lead.selected"
        type="checkbox"
      />
    </td>
    <td class="px-2 py-3 pl-5">
      <router-link
        :to="{ name: 'leads.show', params: { id: lead.id } }"
        class="flex w-1/6"
        v-text="`#${lead.id}`"
      ></router-link>
    </td>
    <td v-text="name"></td>
    <td v-text="lead.phone"></td>
    <td
      v-if="lead.ip_address"
      v-text="lead.ip + ' ' + lead.ip_address.country_name"
    ></td>
    <td
      v-else
      v-text="lead.ip"
    ></td>
    <td
      v-if="lead.assignments && lead.assignments.length > 0"
      v-text="lead.assignments.map(a => a.status).join(',')"
    ></td>
    <td v-else>
      -
    </td>
    <td>
      <span
        v-if="lead.assignments && lead.assignments.length > 0"
        v-text="lead.assignments.map(a => a.id).join(',')"
      ></span>
      <span v-else>-</span>
    </td>
    <td v-if="batch.status !== 'pending' && !!lead.pivot && !isEditing">
      <span
        v-if="lead.pivot.assigned_at"
        v-text="lead.pivot.assigned_at"
      ></span>
      <span v-else>-</span>
    </td>
    <td v-if="batch.status !== 'pending' && !isEditing">
      <span
        v-if="currentAssignment"
        v-text="currentAssignment.created_at"
      ></span>
      <span v-else>-</span>
    </td>
    <td v-if="batch.status !== 'pending' && !isEditing">
      <router-link
        v-if="currentAssignment"
        :to="{
          name: 'leads-orders.show',
          params: { id: currentAssignment.route.order_id }
        }"
      >
        {{ `#${currentAssignment.route.order_id}` }}
      </router-link>
      <span v-else>-</span>
    </td>
    <td v-if="batch.status !== 'pending' && !isEditing">
      <span
        v-if="currentAssignment"
        v-text="currentAssignment.destination_id"
      ></span>
      <span v-else>-</span>
    </td>
    <td v-if="batch.status !== 'pending' && !isEditing">
      <div
        v-if="currentAssignment && currentAssignment.confirmed_at || currentAssignment.delivery_failed"
        class="w-4 h-4 ml-1 border-0 rounded-full"
        :class="[currentAssignment.confirmed_at ? 'bg-green-500' : 'bg-red-500']"
      ></div>
      <span v-else>-</span>
    </td>
  </tr>
</template>

<script>
export default {
  name: 'resell-batch-lead-list-item',
  props: {
    lead: {
      type: Object,
      required: true,
    },
    batch: {
      type: Object,
      required: true,
    },
    isEditing: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    name() {
      return `${this.lead.firstname || ''} ${this.lead.lastname || ''}`;
    },
    currentAssignment() {
      if (!!this.lead.pivot && !!this.lead.pivot.assignment_id) {
        const assignment = this.lead.assignments.find(assignment => assignment.id === this.lead.pivot.assignment_id);
        if (!!assignment) {
          return assignment;
        }
        return null;
      }
      return null;
    },
  },
  methods: {},
};
</script>

<style scoped>
td {
    @apply py-4;
    @apply px-2;
    @apply text-gray-800;
    @apply text-base;
}
</style>
