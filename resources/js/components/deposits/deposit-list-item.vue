<template>
  <tr
    v-if="deposit.office_id"
    class="text-sm"
  >
    <td class="px-2 py-3 pl-5">
      <router-link
        v-if="deposit.can_update"
        :to="{ name: 'deposits.show', params: { id: deposit.id } }"
        class="font-semibold text-gray-700 whitespace-no-wrap hover:text-teal-700"
        v-text="`# ${deposit.id}`"
      >
      </router-link>
      <span
        v-else
        class="font-semibold text-gray-700 whitespace-no-wrap hover:text-teal-700"
        v-text="`# ${deposit.id}`"
      ></span>
    </td>
    <td class="px-2 py-3">
      <span v-text="leadCreatedAt"></span>
    </td>
    <td class="px-2 py-3">
      <span v-text="deposit.lead_return_date"></span>
    </td>
    <td class="px-2 py-3">
      <span v-text="deposit.date"></span>
    </td>
      <td class="px-2 py-3">
      <span
          v-if="deposit.lead"
          v-text="deposit.lead.clickid"
      ></span>
          <span v-else>-</span>
      </td>
    <td class="px-2 py-3">
      <span
        v-if="deposit.account"
        v-text="deposit.account.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td
      v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
      class="px-2 py-3"
    >
      <!--      <span v-if="show.buyer || !isSupport">-->
      <router-link
        v-if="deposit.user"
        :to="{ name: 'users.show', params: { id: deposit.user.id } }"
        v-text="deposit.user.name"
      ></router-link>
      <span v-else>-</span>
      <!--      </span>
      <span v-else>***********</span>
      <fa-icon
        v-if="isSupport"
        :icon="['far','eye']"
        class="mx-2 text-gray-600 cursor-pointer fill-current hover:text-teal-700"
        fixed-width
        @click="show.buyer = !show.buyer"
      ></fa-icon>-->
    </td>
    <td
      v-if="(isAdmin || isSupport || isTeamLead || isBranchHead) && showOffice"
      class="px-2 py-3"
    >
      <!--      <span v-if="show.office || !isSupport">-->
      <span
        v-if="deposit.office"
        v-text="deposit.office.name"
      ></span>
      <span v-else>-</span>
      <!--      </span>
      <span v-else>***********</span>
      <fa-icon
        v-if="isSupport"
        :icon="['far','eye']"
        class="mx-2 text-gray-600 cursor-pointer fill-current hover:text-teal-700"
        fixed-width
        @click="show.office = !show.office"
      ></fa-icon>-->
    </td>
    <td
      class="px-2 py-3"
    >
      <span
        v-if="deposit.offer"
        v-text="deposit.offer.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td
      v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
      class="px-2 py-3"
    >
      <span v-if="showPhone">
        <span v-text="deposit.phone"></span>
      </span>
      <span v-else>***********</span>
      <!--      <fa-icon
        v-if="isSupport"
        :icon="['far','eye']"
        class="mx-2 text-gray-600 cursor-pointer fill-current hover:text-teal-700"
        fixed-width
        @click="show.phone = !show.phone"
      ></fa-icon>-->
    </td>
    <td
      v-if="isAdmin || isSupport || isTeamLead || isBranchHead"
      class="px-2 py-3"
    >
      <!--      <span v-if="show.sum || !isSupport">-->
      <span v-text="`$ ${deposit.sum}`"></span>
      <!--      </span>
      <span v-else>*****</span>
      <fa-icon
        v-if="isSupport"
        :icon="['far','eye']"
        class="mx-2 text-gray-600 cursor-pointer fill-current hover:text-teal-700"
        fixed-width
        @click="show.sum = !show.sum"
      ></fa-icon>-->
    </td>
    <td v-text="updatedAt"></td>
    <td class="flex flex-col px-2 py-3">
      <span
        v-if="deposit.lead"
        v-text="deposit.lead.utm_type"
      ></span>
      <span v-else>-</span>
      <span
        v-if="deposit.lead"
        v-text="deposit.lead.utm_source"
      ></span>
      <span v-else>-</span>
      <span
        v-if="deposit.lead"
        v-text="deposit.lead.utm_content"
      ></span>
      <span v-else>-</span>
      <span
        v-if="deposit.lead"
        v-text="deposit.lead.utm_campaign"
      ></span>
      <span v-else>-</span>
      <span
        v-if="deposit.lead"
        v-text="deposit.lead.utm_term"
      ></span>
      <span v-else>-</span>
      <span
        v-if="deposit.lead"
        v-text="deposit.lead.utm_medium"
      ></span>
      <span v-else>-</span>
    </td>
    <td v-text="deposit.lead.app_id"></td>
    <td
      v-if="isAdmin || isSupport"
      class="px-2 py-3"
    >
      <fa-icon
        :icon="['far', 'pencil-alt']"
        class="ml-2 text-gray-700 cursor-pointer fill-current hover:text-teal-700"
        fixed-width
        @click="$modal.show('edit-deposit-modal', {deposit: deposit})"
      ></fa-icon>
      <fa-icon
        :icon="deleteIcon"
        class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
        fixed-width
        @click="$modal.show('delete-deposit-modal', {deposit: deposit})"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>
import moment from 'moment';
import {faTimes} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'deposit-list-item',
  props: {
    deposit: {
      required: true,
      type: Object,
    },
    showOffice: {
      required: false,
      type: Boolean,
      default: true,
    },
  },
  data: () => ({
    show: {
      buyer: false,
      office: false,
      phone: false,
      sum: false,
    },
  }),
  computed: {
    isAdmin() {
      return this.$root.user.role === 'admin';
    },
    isTeamLead() {
      return this.$root.user.role === 'teamlead';
    },
    isSupport() {
      return this.$root.user.role === 'support';
    },
    isSubSupport() {
      return this.$root.user.role === 'subsupport';
    },
    isBranchHead(){
      return this.$root.user.role === 'head';
    },
    isSales() {
      return this.$root.user.role === 'sales';
    },
    updatedAt() {
      return moment(this.deposit.updated_at).format('YYYY-MM-DD HH:mm:ss');
    },
    leadCreatedAt() {
      if (!this.deposit.lead) {
        return '-';
      }
      return moment(this.deposit.lead.created_at).format('YYYY-MM-DD');
    },
    showPhone() {
      if (this.isSupport) {
        return true;
      }
      return (this.show.phone || !this.isSupport) && (!this.isBranchHead || this.$root.user.branch_id !== 19);
    },
    deleteIcon() {
      return faTimes;
    },
  },
  methods: {
    //
  },
};
</script>
