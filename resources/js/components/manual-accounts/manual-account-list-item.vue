<template>
  <tr>
    <td v-text="account.id">
    </td>
    <td
      class="font-semibold"
      v-text="account.account_id"
    ></td>
    <td v-text="account.name"></td>
    <td
      v-if="account.user"
      v-text="account.user.name"
    ></td>
    <td v-else></td>
    <td>
      <router-link
        :to="{name: 'manual-accounts.show', params: {id: account.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
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
import {faEdit} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'manual-account-list-item',
  props:{
    account:{
      required:true,
      type:Object,
    },
  },
  computed:{
    icon: () => faEdit,
  },
};
</script>

<style scoped>
td{
    @apply px-6;
    @apply py-4;
    @apply whitespace-no-wrap;
    @apply text-sm;
    @apply leading-5;
    @apply text-gray-500;
}
</style>
