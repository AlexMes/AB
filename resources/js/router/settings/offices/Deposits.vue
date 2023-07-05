<template>
  <div class="w-full">
    <div v-if="hasDeposits">
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative">
          <thead
            class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky"
          >
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th class="px-2 py-3">
                Дата лида
              </th>
              <th class="px-2 py-3">
                Дата
              </th>
              <th class="px-2 py-3">
                Аккаунт
              </th>
              <th
                v-if="$root.user.role === 'admin'"
                class="px-2 py-3"
              >
                Баер
              </th>
              <th class="px-2 py-3">
                Оффер
              </th>
              <th
                v-if="$root.user.role === 'admin'"
                class="px-2 py-3"
              >
                Телефон
              </th>
              <th
                v-if="$root.user.role !== 'support'"
                class="px-2 py-3"
              >
                Сумма
              </th>
              <th>Updated at:</th>
              <th class="px-2 py-3">
                UTM
              </th>
            </tr>
          </thead>
          <tbody
            v-for="deposit in deposits"
            :key="deposit.id"
            class="w-full"
          >
            <deposit-list-item
              class="bg-white hover:bg-gray-100 text-black font-normal normal-case"
              :deposit="deposit"
              :show-office="false"
            ></deposit-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
      <div
        v-if="shouldShowQuantity"
        class="flex justify-end mt-8"
      >
        <div>
          <span
            class="font-semibold"
            v-text="`1 - ${current}`"
          ></span>
          <span class="font-weight-normal">из</span>
          <span
            class="font-semibold"
            v-text="total"
          ></span>
        </div>
      </div>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Депозиты не найдено</p>
    </div>
  </div>
</template>

<script>
import DepositListItem from '../../../components/deposits/deposit-list-item';
export default {
  name: 'offices-deposits',
  components: {DepositListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    deposits: [],
    response: null,
  }),
  computed:{
    hasDeposits() {
      return this.deposits.length > 0;
    },
    shouldShowQuantity() {
      return this.response.next_page_url === null;
    },
    total() {
      return this.response.total;
    },
    current() {
      return this.response.data.length;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get('/api/deposits', {params: {page: page, office_id: this.id}})
        .then(response => {
          this.deposits = response.data.data;
          this.response = response.data;
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load deposits',
          }),
        );
    },
  },
};
</script>
