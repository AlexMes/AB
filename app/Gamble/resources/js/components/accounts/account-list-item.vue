<template>
  <tr class="hover:bg-gray-100">
    <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'accounts.show', params: {id: account.id}}"
        class="text-sm font-thin"
        v-text="`#${account.id}`"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <div class="font-semibold">
        <router-link
          :to="{name: 'accounts.show', params: {id: account.id}}"
          v-text="account.name"
        ></router-link>
      </div>
      <div
        class="text-xs text-gray-600"
        v-text="account.account_id"
      ></div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="account.user_id"
        v-text="account.user.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td
      class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="createdAt"
    >
    </td>
  </tr>
</template>

<script>
import moment from 'moment';

export default {
  name: 'account-list-item',
  props: {
    account: {
      type: Object,
      required: true,
    },
  },
  computed: {
    createdAt() {
      return moment(this.account.created_at).format('DD.MM.YYYY HH:mm:ss');
    },
  },
};
</script>

<style scoped>

</style>
