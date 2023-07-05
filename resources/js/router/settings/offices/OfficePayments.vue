<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <span class="inline-flex rounded-md shadow-sm">
          <router-link
            class="cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            :to="{name: 'office-payments.create', params: {officeId: id}}"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400"
              fixed-width
            ></fa-icon>
            <span>
              Добавить
            </span>
          </router-link>
        </span>
      </div>
    </div>
    <div
      v-if="hasPayments"
    >
      <div
        class="overflow-x-auto overflow-y-hidden flex w-full bg-white shadow no-last-border"
      >
        <table class="w-full table table-auto relative">
          <thead
            class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky"
          >
            <tr>
              <th class="pl-5 px-2 py-3">
                #
              </th>
              <th class="px-2 py-3">
                Оплачено лидов
              </th>
              <th class="px-2 py-3">
                Выдано лидов
              </th>
              <th class="px-2 py-3">
                Создана
              </th>
              <th class="px-2 py-3"></th>
            </tr>
          </thead>
          <tbody class="w-full">
            <office-payment-list-item
              v-for="payment in payments"
              :key="payment.id"
              :payment="payment"
              @deleted="remove"
            ></office-payment-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Оплат не найдено</p>
    </div>
  </div>
</template>

<script>
import OfficePaymentListItem from '../../../components/settings/office-payment-list-item';
export default {
  name: 'offices-office-payments',
  components: {OfficePaymentListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    payments: [],
    response: null,
  }),
  computed:{
    hasPayments() {
      return this.payments.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios
        .get(`/api/offices/${this.id}/office-payments`, {params: {page}})
        .then(response => {
          this.payments = response.data.data;
          this.response = response.data;
        })
        .catch(err => this.$toast.error({title: 'Error', message: 'Unable to load office payments.'}));
    },
    remove(event) {
      const index = this.payments.findIndex(payment => payment.id === event.payment.id);
      if (index !== -1) {
        this.payments.splice(index, 1);
      }
    },
  },
};
</script>
