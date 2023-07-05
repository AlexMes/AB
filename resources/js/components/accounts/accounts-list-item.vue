<template>
  <div>
    <div class="w-full bg-white p-4 border-b flex">
      <div class="flex flex-col justify-center w-1/4 text-left pr-2">
        <div
          class="flex text-gray-700"
        >
          <span
            v-text="account.name"
          ></span>
        </div>
        <span
          class="mt-1 text-gray-600 text-sm"
          v-text="account.id"
        ></span>
        <div
          class="flex pt-4 justify-between items-center"
        >
          <div
            class="text-gray-700 flex flex-col w-full text-sm"
          >
            <span
              class="w-full truncate hover:whitespace-normal"
              v-text="account.comment"
            ></span>
          </div>
        </div>
      </div>
      <div
        v-if="account.profile"
        class="w-1/5 flex items-center"
      >
        <router-link
          :to="{name: 'profile.general', params: {id: account.profile.id}}"
          v-text="account.profile.name"
        >
        </router-link>
      </div>
      <div class="w-40 text-left flex items-center">
        <span
          v-text="`${account.age} дней`"
        ></span>
        <span
          v-if="account.banned_at"
          class="ml-1"
        > / {{ lifetime }}</span>
      </div>
      <div
        class="w-1/6 flex flex-col"
      >
        <span v-text="account.status"></span>
        <span
          class="text-sm text-gray-600"
          v-text="account.disable_reason"
        ></span>
      </div>
      <div class="flex justify-between items-center w-1/3">
        <span
          v-text="account.expenses"
        ></span>
        <span
          v-text="account.balance"
        ></span>
        <span>{{ account.spend ? account.spend : '-' }}</span>
        <span>{{ account.cpl ? account.cpl : '-' }}</span>
      </div>
      <div
        class="flex w-1/12 items-center justify-center p-4"
      >
        <fa-icon
          :icon="['far','edit']"
          class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
          fixed-width
          @click="$modal.show('account-comment-modal',{account: account})"
        ></fa-icon>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDistanceStrict, parseISO } from 'date-fns';
import ru from 'date-fns/locale/ru';

export default {
  name: 'ads-accounts-list-item',
  props:{
    account:{
      type:Object,
      required:true,
    },
  },
  computed: {
    lifetime() {
      if (this.account.banned_at) {
        return formatDistanceStrict(
          parseISO(this.account.banned_at),
          parseISO(this.account.created_at),
          {locale: ru, roundingMethod: 'ceil'},
        );
      }
      return '';
    },
  },
  created() {
    Echo.private(`App.Account.${this.account.id}`)
      .listen('Updated', event => {
        this.$emit('update', {account: event.account});
      });
  },
  beforeDestroy() {
    Echo.leave(`accounts-${this.account.id}`);
  },
};
</script>

