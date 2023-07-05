<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="domain.url"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый домен
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="url"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            URL
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="url"
                v-model="domain.url"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                minlength="3"
                @input="errors.clear('url')"
              />
            </div>
            <span
              v-if="errors.has('url')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('url')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="effectiveDate"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="effectiveDate"
                v-model="domain.effectiveDate"
                class="w-full px-1 py-2 mt-2 border rounded text-gray-600"
                placeholder="Выберите дату"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('effectiveDate')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('effectiveDate')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="linkType"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Тип домена
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="linkType"
                v-model="domain.linkType"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('linkType')"
              >
                <option
                  v-for="type in linkTypes"
                  :key="type.id"
                  :value="type.id"
                  v-text="type.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('linkType')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('linkType')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="status"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Статус
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="status"
                v-model="domain.status"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('status')"
              >
                <option
                  v-for="status in statuses"
                  :key="status.id"
                  :value="status.id"
                  v-text="status.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('status')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('status')"
            ></span>
          </div>
        </div>

        <div
          v-if="domain.linkType === 'prelanding'"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="user_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Пользователь
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="user_id"
                v-model="domain.user_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
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
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('user_id')"
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
              <mutiselect
                id="offer_id"
                v-model="domain.offer"
                :show-labels="false"
                :options="offers"
                placeholder="Выберите офер"
                track-by="id"
                label="name"
                @input="errors.clear('offer_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('offer_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('offer_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="traffic_source_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Источник трафика
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="traffic_source_id"
                v-model="domain.traffic_source_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('traffic_source_id')"
              >
                <option :value="null">
                  Нет
                </option>
                <option
                  v-for="traffic_source in traffic_sources"
                  :key="traffic_source.id"
                  :value="traffic_source.id"
                  v-text="traffic_source.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('traffic_source_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('traffic_source_id')"
            ></span>
          </div>
        </div>

        <div
          v-if="domain.linkType === 'prelanding'"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="sp_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Safe Page
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="sp_id"
                v-model="domain.sp_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('sp_id')"
              >
                <option :value="null">
                  Нет
                </option>
                <option
                  v-for="page in safePages"
                  :key="page.id"
                  :value="page.id"
                  v-text="page.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('sp_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('sp_id')"
            ></span>
          </div>
        </div>

        <div
          v-if="domain.linkType === 'prelanding'"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="bp_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Black Page
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="bp_id"
                v-model="domain.bp_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('bp_id')"
              >
                <option :value="null">
                  Нет
                </option>
                <option
                  v-for="page in blackPages"
                  :key="page.id"
                  :value="page.id"
                  v-text="page.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('bp_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('bp_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="reach_status"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Статус прохождения
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="reach_status"
                v-model="domain.reach_status"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('reach_status')"
              >
                <option
                  v-for="reach_status in reach_statuses"
                  :key="reach_status.id"
                  :value="reach_status.id"
                  v-text="reach_status.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('reach_status')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('reach_status')"
            ></span>
          </div>
        </div>

        <div
          v-if="domain.linkType === 'landing'"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Splitter
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs">
              <toggle v-model="domain.splitterEnabled"></toggle>
            </div>
            <span
              v-if="errors.has('splitterEnabled')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('splitterEnabled')"
            ></span>
          </div>
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Разрешить дубли
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs">
              <toggle v-model="domain.allow_duplicates"></toggle>
            </div>
            <span
              v-if="errors.has('allow_duplicates')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('allow_duplicates')"
            ></span>
          </div>
        </div>

        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
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

export default {
  name: 'domains-form',
  components: {
    DatePicker,
  },
  props: {
    id: {
      type: [String,Number],
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    domain: {
      effectiveDate: null,
      url: '',
      status: null,
      reach_status: null,
      linkType:null,
      comment: '',
      user:null,
      user_id: null,
      order_id: null,
      offer: null,
      traffic_source_id: null,
      sp_id: null,
      bp_id: null,
      splitterEnabled: false,
      allow_duplicates: false,
    },
    statuses: [
      {id:'development',name: 'В работе'},
      {id:'scheduled',name: 'Запланирован'},
      {id:'ready',name: 'Готов'},
      {id:'worked_out',name: 'Отработан'},
    ],
    reach_statuses: [
      {id: null, name: 'Не выбран'},
      {id:'passed',name: 'Прошел'},
      {id:'missed',name: 'Не прошел'},
      {id:'banned',name: 'Бан'},
    ],
    linkTypes:[
      {id:'landing',name:'Ленд'},
      {id:'prelanding',name:'Преленд'},
      {id:'service',name:'Сервис'},
    ],
    users: [],
    offers: [],
    traffic_sources: [],
    pages:[],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null;
    },
    cleanDomain(){
      return {
        effectiveDate: this.domain.effectiveDate,
        url: this.domain.url,
        sp_id: this.domain.sp_id,
        bp_id: this.domain.bp_id,
        status: this.domain.status ? this.domain.status: 'development',
        reach_status: this.domain.reach_status,
        linkType: this.domain.linkType ? this.domain.linkType: null,
        user_id: this.domain.user_id,
        offer_id: !this.domain.offer ? null : this.domain.offer.id,
        traffic_source_id: this.domain.traffic_source_id,
        splitterEnabled: this.domain.splitterEnabled,
        allow_duplicates: this.domain.allow_duplicates,
      };
    },
    safePages(){
      return this.pages.filter(page => page.type === 'safe');
    },
    blackPages(){
      return this.pages.filter(page => page.type === 'black');
    },
  },
  watch: {
    'domain.offer'(value, old) {
      this.domain.allow_duplicates = this.domain.offer.allow_duplicates;
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      if(this.isUpdating){
        this.load();
      }
      this.getBuyers();
      this.getOffers();
      this.getTrafficSources();
      this.getPages();
    },
    load(){
      axios.get(`/api/domains/${this.id}`)
        .then(r => this.domain = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить домен', message: err.response.data.message});
        });
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'domains.show', params:{id: this.id}})
        : this.$router.push({name:'domains.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/domains/', this.cleanDomain)
        .then(r => {
          this.$router.push({name:'domains.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if(err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось создать домен', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/domains/${this.domain.id}`, this.cleanDomain)
        .then(r => this.$router.push({name:'domains.show',params:{id:r.data.id}}))
        .catch(err => {
          if(err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить домен', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    getBuyers(){
      axios.get('/api/users', {params:{all:true}})
        .then(response => this.users = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить баеров', message: error.response.data.message});
        });
    },
    getOffers() {
      axios.get('/api/offers', {params:{all:true}})
        .then(response => this.offers = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить оферы', message: error.response.data.message});
        });
    },
    getTrafficSources() {
      axios.get('/api/traffic-sources', {params:{all:true}})
        .then(response => this.traffic_sources = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить источники трафика', message: error.response.data.message});
        });
    },
    getPages(){
      axios.get('/api/pages/', {params: {all: true}}).then(r => this.pages = r.data);
    },
  },
};
</script>

<style scoped>

</style>
