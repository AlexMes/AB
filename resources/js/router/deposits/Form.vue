<template>
  <div class="container mx-auto">
    <div class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg font-medium leading-6 text-gray-900"
        v-text="`Редактирование депозита ${deposit.id}`"
      ></h3>
      <h3
        v-else
        class="text-lg font-medium leading-6 text-gray-900"
      >
        Новый депозит
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="p-3 my-4 text-white bg-red-700 rounded"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="lead_return_date"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата лида
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="lead_return_date"
                v-model="deposit.lead_return_date"
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                :config="picker"
                placeholder="Выберите дату"
                @input="errors.clear('lead_return_date')"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('lead_return_date')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('lead_return_date')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="date"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата депозита
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="date"
                v-model="deposit.date"
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                :config="picker"
                placeholder="Выберите дату"
                @input="errors.clear('date')"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('date')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('date')"
            ></span>
          </div>
        </div>

        <div
          v-if="$root.user.role !== 'support' || !isUpdating"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="sum"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Сумма
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="sum"
                v-model="deposit.sum"
                type="number"
                placeholder="0"
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                @input="errors.clear('sum')"
              />
            </div>
            <span
              v-if="errors.has('sum')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('sum')"
            ></span>
          </div>
        </div>

        <div
          v-if="$root.user.role === 'admin' || !isUpdating"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="phone"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Телефон
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="phone"
                v-model="deposit.phone"
                type="text"
                placeholder="Введите телефон"
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                @input="errors.clear('phone')"
              />
            </div>
            <span
              v-if="errors.has('phone')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('phone')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="office_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="office_id"
                v-model="deposit.office_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                @change="errors.clear('office_id')"
              >
                <option
                  v-for="office in offices"
                  :key="office.id"
                  :value="office.id"
                  v-text="office.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('office_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('office_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="offer_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Оффер
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="offer_id"
                v-model="deposit.offer_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                @change="errors.clear('offer_id')"
              >
                <option
                  v-for="offer in offers"
                  :key="offer.id"
                  :value="offer.id"
                  v-text="offer.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('offer_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('offer_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="user_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Баер
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="user_id"
                v-model="deposit.user_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
                @change="errors.clear('user_id')"
              >
                <option
                  v-for="user in users"
                  :key="user.id"
                  :value="user.id"
                  v-text="user.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('user_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('user_id')"
            ></span>
          </div>
        </div>

        <!-- <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="account_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Аккаунт
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <multiselect
                id="account_id"
                :value="accounts.find(acc => acc.account_id == deposit.account_id)"
                :options="accounts"
                :allow-empty="true"
                :show-labels="false"
                label="name"
                placeholder=""
                track-by="account_id"
                @select="errors.clear('account_id')"
                @input="event => deposit.account_id = event.account_id"
              ></multiselect>
            </div>
            <span
              v-if="errors.has('account_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('account_id')"
            ></span>
          </div>
        </div> -->

        <div class="flex flex-wrap items-center w-full my-4 border-t">
          <search-field
            class="w-full my-6"
            placeholder="Поиск лида (телефон, e-mail, id, uuid, domain, clickid, utm_source, utm_content)"
            @search="findLeads"
          ></search-field>
          <div
            v-if="leads.length === 0"
            class="py-4"
          >
            <span>Лидов не найдено</span>
          </div>
          <div
            v-else
            class="w-full overflow-x-auto sm:overflow-hidden"
          >
            <div
              v-for="lead in leads"
              :key="lead.id"
              class="flex sm:justify-around items-center w-full py-4"
            >
              <div
                class="flex px-3 py-2"
                v-text="`#${lead.id}`"
              ></div>
              <div
                class="flex px-3 py-2"
                v-text="lead.firstname +' ' + lead.lastname"
              ></div>
              <div
                class="flex px-3 py-2"
                v-text="lead.clickid"
              ></div>
              <div
                class="flex px-3 py-2"
                v-text="lead.phone"
              ></div>
              <div
                v-if="lead.offer"
                class="flex px-3 py-2"
                v-text="lead.offer.name"
              >
              </div>
              <div
                v-if="lead.account"
                class="flex px-3 py-2"
                v-text="lead.account.name"
              ></div>
              <div
                v-if="lead.user"
                class="flex px-3 py-2"
                v-text="lead.user.name"
              ></div>
              <div
                v-if="lead.last_assignment"
                class="flex px-3 py-2"
                v-text="lead.last_assignment.route.order.office.name"
              ></div>
              <div
                v-if="lead.last_assignment"
                class="flex px-3 py-2"
                v-text="lead.last_assignment.created_at"
              ></div>
              <div>
                <button
                  type="button"
                  class="button btn-primary"
                  @click="pick(lead)"
                >
                  Выбрать
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end w-full pt-4 mt-6 border-t">
          <button
            type="reset"
            class="mx-2 button btn-secondary"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="mx-2 button btn-primary"
            :disabled="isBusy"
            @click.prevent="save"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import {uniqBy} from 'lodash-es';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
  name: 'domains-form',
  components: {
    DatePicker,
    Multiselect,
  },
  props: {
    id: {
      type: [String,Number],
      default: null,
    },
  },
  data:()=>({
    deposit: {
      lead_return_date: null,
      date: null,
      sum: 0,
      phone: null,
      account: null,
      // account_id: null,
      user_id: null,
      office_id: null,
      offer_id: null,
      lead_id: null,
    },
    isBusy:false,
    // accounts: [],
    users: [],
    offices: [],
    offers: [],
    leads: [],
    errors: new ErrorBag(),
    picker: {
      enableTime:false,
      dateFormat: 'Y-m-d',
    },
  }),
  computed:{
    isUpdating(){
      return this.id !== null;
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      this.getBuyers();
      // this.getAccounts();
      this.getOffers();
      this.getOffices();
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/deposits/${this.id}`)
        .then(r => this.deposit = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить депозит', message: err.response.data.message});
        });
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'deposits.show', params:{id: this.id}})
        : this.$router.push({name:'deposits.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/deposits/', this.deposit)
        .then(r => {
          if(this.$root.user.role === 'subsupport'){
            this.$router.push({name:'deposits.index'});
            return;
          }
          this.$router.push({name:'deposits.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if(err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить депозит', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/deposits/${this.deposit.id}`, this.deposit)
        .then(r => this.$router.push({name:'deposits.show',params:{id:r.data.id}}))
        .catch(err => {
          if(err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить депозит', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    getBuyers(){
      axios.get('/api/users', {params:{all:true}})
        .then(response => this.users = response.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить баеров', message: e.response.data.message});
        });
    },
    getAccounts() {
      axios.get('/api/accounts', {params:{users:this.deposit.user_id,all:true}})
        .then(response => this.accounts = uniqBy(response.data, account => account.account_id) )
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить аккаунты', message: e.response.data.message});
        });
    },
    getLeads(query) {
      axios.get('/api/leads', {params:{search:query}})
        .then(response => this.leads = response.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить лиды', message: e.response.data.message});
        });
    },
    getOffices() {
      axios.get('/api/offices', {params:{all:true}})
        .then(response => this.offices = response.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить офисы', message: e.response.data.message});
        });
    },
    getOffers() {
      axios.get('/api/offers', {params:{all:true}})
        .then(response => this.offers = response.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить оферы', message: e.response.data.message});
        });
    },
    findLeads(needle) {
      if (needle === '') {
        this.leads = [];
        return;
      }
      axios.get('/api/leads', {params: {search: needle}})
        .then(r => this.leads = r.data.data)
        .catch(err => console.error);
    },
    pick(lead){
      this.deposit.lead_id = lead.id;
      this.deposit.offer_id = lead.offer_id;
      // this.deposit.account_id = lead.account_id;
      this.deposit.user_id = lead.user_id;
      this.deposit.phone = lead.phone;
      this.deposit.office_id = lead.last_assignment ? lead.last_assignment.route.order.office_id : null;
      this.deposit.lead_return_date = lead.last_assignment ? lead.last_assignment.created_at : null;
    },
  },
};
</script>
