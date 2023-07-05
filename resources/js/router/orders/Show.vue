<template>
  <div class="container mx-auto flex flex-col">
    <div
      class="w-full flex flex-col h-auto mb-8 bg-white rounded shadow"
    >
      <div class="flex justify-between border-b px-4 py-3">
        <h2 class="text-gray-700">
          Заказ ссылок #{{ order.id }}
        </h2>
        <router-link
          v-if="order.can_update"
          :to="{name:'orders.update'}"
          class="button btn-primary"
        >
          Редактировать
        </router-link>
      </div>
      <div class="flex flex-col">
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Количество ссылок:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="order.links_count"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Клоакер:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              v-if="order.cloak"
              class="font-normal text-gray-700"
              v-text="order.cloak.name"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Кампания в биноме:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="order.binom_id"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Тип доменов:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="order.linkType"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Регистратор:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              v-if="order.registrar"
              class="font-normal text-gray-700"
              v-text="order.registrar.name"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Хостинг:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              v-if="order.hosting"
              class="font-normal text-gray-700"
              v-text="order.hosting.name"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Safe Page:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              v-if="order.sp"
              class="font-normal text-gray-700"
              v-text="order.sp.name"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Black Page:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              v-if="order.bp"
              class="font-normal text-gray-700"
              v-text="order.bp.name"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Лендинг:
          </div>
          <div class="flex w-1/3 text-left">
            <a
              v-if="order.landing"
              :href="order.landing.url"
              class="font-semibold text-gray-700 hover:text-teal-700"
              v-text="order.landing.url"
            ></a>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full "
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Выполнить до:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="order.deadline_at"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full "
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Проход:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-bold text-gray-700"
              v-text="adsCount"
            ></span>
            <span class="mx-1">/</span>
            <span
              class="font-bold text-green-700"
              v-text="`${passedAds} (${passedPercent}%)`"
            ></span>
            <span class="mx-1">/</span>
            <span
              class="font-bold text-red-700"
              v-text="`${rejectedAds} (${rejectedPercent}%)`"
            ></span>
          </div>
        </div>
        <div
          v-for="item in rejectReasonStats"
          :key="item.reject_reason"
          class="flex border-b p-3 w-full"
        >
          <div
            class="flex w-1/3 text-left font-semibold text-gray-800 pl-9"
            v-text="item.reject_reason"
          ></div>
          <div class="flex w-1/3 text-left">
            <span
              class="text-gray-700"
              v-text="item.cnt"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full "
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Прогресс:
          </div>
          <div class="flex w-1/3 pr-4 items-center text-left">
            <order-progress-widget
              class="flex w-full"
              :current="order.links_done_count"
              :goal="order.links_count"
            ></order-progress-widget>
          </div>
          <span
            class="flex ml-3"
            v-text="`${order.links_done_count} / ${order.links_count}`"
          ></span>
        </div>
      </div>
    </div>
    <div
      class="w-full bg-white rounded shadow"
    >
      <div class="flex justify-between border-b px-4 py-3">
        <h2 class="text-gray-700">
          Домены:
        </h2>
        <div class="flex flex-wrap justify-between">
          <div class="flex mr-4">
            <select
              v-model="filters.user_id"
              @change="getDomains"
            >
              <option value="all">
                Все
              </option>
              <option value="">
                Без пользователя
              </option>
              <option
                v-for="user in users"
                :key="user.id"
                :value="user.id"
                v-text="user.name"
              ></option>
            </select>
          </div>
          <button
            v-if="$root.user.role === 'admin' && selectedDomains.length > 0"
            class="button btn-primary mr-4"
            @click.prevent="$modal.show('transfer-domains-modal', {domains: selectedDomains})"
          >
            Изменить заказ
          </button>
          <button
            v-if="['admin','teamlead'].includes($root.user.role) && selectedDomains.length > 0"
            class="button btn-primary mr-4"
            @click.prevent="$modal.show('change-domain-user-modal', {domains: selectedDomains})"
          >
            Изменить баера
          </button>
          <button
            v-if="order.can_update"
            class="button btn-primary"
            @click.prevent="toggleForm"
          >
            Добавить домен
          </button>
        </div>
      </div>
      <form
        v-if="addingDomain"
        class="flex items-start p-4"
        @submit.prevent="saveDomain"
      >
        <div class="flex mt-8">
          <fa-icon
            :icon="['far', 'plus']"
            class="fill-current text-green-600 cursor-pointer"
            fixed-width
            @click="domain.urls.push({value: ''})"
          ></fa-icon>
        </div>
        <div class="flex flex-col mr-3 w-1/3">
          <label class="flex mb-2 w-full">URL </label>
          <div
            v-for="(url, index) in domain.urls"
            :key="index"
            class="flex items-center"
          >
            <input
              v-model="url.value"
              class="w-full border-b text-gray-700 placeholder-gray-400 px-1 py-2 "
              type="text"
              placeholder="URL сайта"
            />
            <fa-icon
              v-if="domain.urls.length > 1"
              :icon="['far', 'times-circle']"
              class="fill-current text-red-600 cursor-pointer"
              fixed-width
              @click="domain.urls.splice(index, 1)"
            ></fa-icon>
          </div>
        </div>
        <div class="flex flex-col mx-3 w-1/5">
          <label class="flex mb-2 w-full">Баер</label>
          <select
            v-model="domain.user_id"
          >
            <option
              v-for="user in users"
              :key="user.id"
              :value="user.id"
              v-text="user.name"
            ></option>
          </select>
        </div>
        <div class="flex flex-col items-center">
          <button
            type="submit"
            class="button btn-primary mt-6"
            @submit.prevent="saveDomain"
          >
            Сохранить
          </button>
        </div>
      </form>
      <div v-if="hasDomains">
        <table class="table table-auto w-full">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full">
            <tr class="px-3">
              <th>ID</th>
              <th>Дата</th>
              <th>URL</th>
              <th>Баер</th>
              <th>Статус</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <domain-list-item
              v-for="domain in domains"
              :key="domain.id"
              :domain="domain"
              :selectable="true"
              compact
              @toggle-selected="toggleSelectedDomain"
              @domain-updated="update"
            ></domain-list-item>
          </tbody>
        </table>
      </div>
      <div
        v-else
        class="flex p-6 w-full text-center"
      >
        <span class="text-lg">Ещё нет доменов</span>
      </div>
    </div>
    <transfer-domains-modal :order="order"></transfer-domains-modal>
    <change-domain-user-modal :order="order"></change-domain-user-modal>
  </div>
</template>


<script>
import OrderProgressWidget from '../../components/orders/progress-widget';
import TransferDomainsModal from '../../components/orders/transfer-domains-modal';
import ChangeDomainUserModal from '../../components/orders/change-domain-user-modal';
export default {
  name: 'orders-show',
  components: {TransferDomainsModal, OrderProgressWidget, ChangeDomainUserModal},
  props: {
    id: {
      type: [Number,String],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      addingDomain:false,
      order:{},
      users: [],
      domains:[],
      domain:{
        urls:[{value: ''}],
        user_id:null,
      },
      rejectReasonStats: [],
      filters: {
        user_id: 'all',
      },
      selectedDomains: [],
    };
  },
  computed:{
    hasDomains(){
      return this.domains.length > 0;
    },
    adsCount() {
      return this.domains.reduce((result, domain) => result + domain.total_ads, 0);
    },
    passedAds() {
      return this.domains.reduce((result, domain) => result + domain.passed_ads_count, 0);
    },
    rejectedAds() {
      return this.domains.reduce((result, domain) => result + domain.rejected_ads_count, 0);
    },
    passedPercent() {
      return parseFloat(this.adsCount > 0 ? this.passedAds / this.adsCount * 100 : 0).toFixed(2);
    },
    rejectedPercent() {
      return parseFloat(this.adsCount > 0 ? this.rejectedAds / this.adsCount * 100 : 0).toFixed(2);
    },
  },
  created(){
    this.boot();
  },
  beforeDestroy() {
    Echo.leaveChannel(`App.Orders.${this.id}`);
  },
  methods: {
    boot(){
      this.getOrder();
      this.getDomains();
      this.getBuyers();
      this.listen();
      this.getRejectReasonStats();
    },
    listen(){
      Echo.private(`App.Orders.${this.id}`)
        // We load order because of all relationships. Maybe change in future
        .listen('.Updated',e => {
          this.getOrder();
        })
        .listen('.DomainCreated', event => this.domains.push(event.domain));
    },
    toggleForm(){
      this.addingDomain = !this.addingDomain;
    },
    getOrder() {
      axios
        .get(`/api/orders/${this.id}`)
        .then(r => this.order = r.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить заказ.', message: e.response.data.message});
        });
    },
    getBuyers(){
      axios
        .get('/api/users', {params:{all:true}})
        .then(response => this.users = response.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить баеров.', message: e.response.data.message});
        });
    },
    getDomains(){
      axios.get(`/api/orders/${this.id}/domains`, {params: {
        user_id: this.filters.user_id,
      }})
        .then(response => this.domains = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить домены.', message: error.response.data.message});
        });
    },
    saveDomain(){
      axios.post(`/api/orders/${this.id}/domains`,{
        urls:this.domain.urls.map(url => url.value),
        status:'ready',
        linkType: 'prelanding',
        user_id: this.domain.user_id,
        splitterEnabled: false,
      }).then(response => {
        this.domain = {
          urls:[{value: ''}],
          user_id:null,
        };
        response.data.forEach(domain => this.domains.push(domain));
      })
        .catch(error => this.$toast.error({title: 'Error', message: 'Unable to save domains.'}));
    },
    update(event) {
      const index = this.domains.findIndex(item => item.id == event.domain.id);
      if (index !== -1) {
        this.$set(this.domains, index, event.domain);
      }
    },
    getRejectReasonStats() {
      axios.get(`/api/orders/${this.id}/reject-reason-stats`)
        .then(r => this.rejectReasonStats = r.data)
        .catch(e => this.$toast.error({title: 'Could not get reject reason stats.', message: e.response.data.message}));
    },
    toggleSelectedDomain(event) {
      const index = this.selectedDomains.findIndex(item => item === event.domain_id);
      if (index === -1) {
        this.selectedDomains.push(event.domain_id);
      } else {
        this.selectedDomains.splice(index, 1);
      }
    },
  },
};
</script>

<style>
    th{
        @apply text-left;
        @apply px-2;
        @apply py-3;
    }
    select{
     @apply block;
     @apply appearance-none;
     @apply w-full;
     @apply bg-white;
     @apply border;
     @apply border-gray-400;
     @apply px-3;
     @apply py-2;
     @apply pr-4;
     @apply rounded;
    }
</style>
