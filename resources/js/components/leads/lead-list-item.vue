<template>
  <tr
    class="w-full items-center border-b px-3 hover:bg-gray-100"
    :class="[lead.is_duplicate ? 'bg-red-100' : 'bg-white', isDeleted ? 'line-through text-gray-600' : '']"
  >
    <td class="px-2 py-3 pl-5">
      <router-link
        :to="{name:'leads.show', params:{id:lead.id}}"
        class="w-1/6 flex"
        v-text="`#${lead.id}`"
      ></router-link>
    </td>
    <td v-text="date"></td>
    <td
      v-if="lead.ip_address"
      v-text="lead.ip_address.country_name"
    ></td>
    <td v-else></td>
    <td v-text="name">
    </td>
    <td v-text="lead.phone">
    </td>
    <td v-text="lead.domain">
    </td>
    <td v-text="lead.ip">
    </td>
    <td v-text="lead.clickid">
    </td>
    <td v-text="lead.app_id">
    </td>
    <td
      v-if="lead.offer"
      v-text="lead.offer.name"
    >
    </td>
    <td v-else></td>
    <td
      v-if="lead.user"
      v-text="lead.user.name"
    >
    </td>
    <td v-else></td>
    <td
      v-if="
        $root.user.role === 'admin' ||
          $root.user.role === 'support'
      "
    >
      <fa-icon
        :icon="editIcon"
        class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
        fixed-width
        @click="$modal.show('edit-lead-modal', {lead: lead})"
      ></fa-icon>
      <fa-icon
        v-if="$root.user.branch_id === 16"
        :icon="markUnpayableIcon"
        class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
        @click="markUnpayable"
      >
      </fa-icon>
      <fa-icon
        v-if="isDeleted"
        class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
        :icon="restoreIcon"
        fixed-width
        @click="restore"
      ></fa-icon>
      <span v-else>
        <fa-icon
          v-if="!isLeftover"
          :icon="['far','exchange']"
          class="fill-current text-gray-700 cursor-pointer hover:text-teal-700"
          fixed-width
          @click.prevent="$modal.show('mark-lead-leftover-modal', {lead: lead})"
        ></fa-icon>
        <fa-icon
          :icon="deleteIcon"
          class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
          fixed-width
          @click="remove"
        ></fa-icon>
      </span>
    </td>
  </tr>
</template>

<script>
import moment from 'moment';
import {faHandshakeSlash, faPencil, faTimes, faTrashRestore} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'lead-list-item',
  props: {
    lead: {
      type: Object,
      required: true,
    },
  },
  computed: {
    name() {
      return `${this.lead.firstname || ''} ${this.lead.lastname || ''}`;
    },
    date() {
      return moment(this.lead.created_at).format('YYYY-MM-DD HH:mm');
    },
    isDeleted() {
      return this.lead.deleted_at !== undefined && this.lead.deleted_at !== null;
    },
    deleteIcon() {
      return faTimes;
    },
    restoreIcon() {
      return faTrashRestore;
    },
    markUnpayableIcon() {
      return faHandshakeSlash;
    },
    editIcon() {
      return faPencil;
    },
    isLeftover() {
      return !this.lead.offer ? false : this.lead.offer.name.startsWith('LO_');
    },
  },
  methods: {
    remove() {
      if (confirm('Уверены, что хотите удалить лид ?')) {
        axios.delete(`/api/leads/${this.lead.id}`)
          .then(response => {
            this.$emit('updated', {lead: response.data});
            this.$toast.success({title: 'OK', message: 'Лид удалён.'});
          })
          .catch(err => {
            if (err.response.status === 422) {
              this.$toast.error({title: 'Не удалось удалить лид.', message: err.response.data.errors.has_assignments[0]});
              return;
            }
            this.$toast.error({title: 'Не удалось удалить лид.', message: err.response.data.message});
          });
      }
    },
    restore() {
      if (confirm('Уверены, что хотите восстановить лид ?')) {
        axios.post(`/api/leads/${this.lead.id}/restore`)
          .then(response => {
            this.$emit('updated', {lead: response.data});
            this.$toast.success({title: 'OK', message: 'Лид восстановлен.'});
          })
          .catch(err => this.$toast.error({
            title: 'Не удалось восстановить лид.',
            message: err.response.data.message,
          }));
      }
    },
    markUnpayable() {
      if (confirm('Уверены, что хотите пометить лид как неплатёжеспособный ?')) {
        axios.post(`/api/leads/${this.lead.id}/mark-unpayable`)
          .then(response => {
            /*this.$eventHub.$emit('assignment-updated', {assignment: response.data});*/
            this.$toast.success({title: 'Ok', message: 'Лид помечен как неплатёжеспособный.'});
          })
          .catch(err => this.$toast.error({
            title: 'Не удалось пометить лид как неплатёжеспособный.',
            message: err.response.data.message,
          }));
      }
    },
  },
};
</script>

<style scoped>
    td{
        @apply py-4;
        @apply px-2;
        @apply text-gray-800;
        @apply text-base;
    }
</style>
