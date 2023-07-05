<template>
  <tr>
    <td
      class="pl-5"
      v-text="payment.id"
    ></td>
    <td v-text="payment.paid"></td>
    <td v-text="payment.assigned"></td>
    <td v-text="created"></td>
    <td>
      <router-link
        :to="{name: 'office-payments.update', params: {id: payment.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        :icon="['far', 'times-circle']"
        class="text-gray-500 fill-current cursor-pointer hover:text-red-600"
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>

import moment from 'moment';

export default {
  name: 'office-payment-list-item',
  props: {
    payment: {
      required: true,
      type: Object,
    },
  },
  computed: {
    created() {
      return moment(this.payment.created_at).format('YYYY-MM-DD HH:mm:ss');
    },
  },
  methods: {
    remove() {
      if (confirm('Удалить оплату?')) {
        axios.delete(`/api/office-payments/${this.payment.id}`)
          .then(response => this.$emit('deleted', {payment: this.payment}))
          .catch(err => this.$toast.error({title: 'Не удалось удалить оплату.', message: err.response.data.message}));
      }
    },
  },
};
</script>

<style scoped>
td{
    @apply pr-4;
    @apply py-4;
    @apply whitespace-no-wrap;
    @apply text-sm;
    @apply leading-5;
    @apply text-gray-500;
}
</style>
