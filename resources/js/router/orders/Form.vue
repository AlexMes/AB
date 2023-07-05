<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="`Редактирование заказа ссылок #${order.id}`"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый заказ ссылок
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
            for="links_count"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Количество ссылок
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="links_count"
                v-model="order.links_count"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('links_count')"
              />
            </div>
            <span
              v-if="errors.has('links_count')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('links_count')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="cloak_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Клоака
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="cloak_id"
                v-model="order.cloak_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('cloak')"
              >
                <option
                  v-for="cloak in cloaks"
                  :key="cloak.id"
                  :value="cloak.id"
                  v-text="cloak.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('cloak_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('cloak_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="binom_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Кампания (бином)
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="binom_id"
                v-model="order.binom_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('binom_id')"
              >
                <option
                  v-for="campaign in campaigns"
                  :key="campaign.id"
                  :value="campaign.campaign_id"
                  v-text="`${campaign.campaign_id} - ${campaign.name}`"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('binom_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('binom_id')"
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
            Тип ссылок
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="linkType"
                v-model="order.linkType"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('linkType')"
              >
                <option
                  v-for="linkType in types"
                  :key="linkType.id"
                  :value="linkType.id"
                  v-text="linkType.name"
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
            for="registrar_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Регистратор
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="registrar_id"
                v-model="order.registrar_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('registrar_id')"
              >
                <option
                  v-for="registrar in registrars"
                  :key="registrar.id"
                  :value="registrar.id"
                  v-text="registrar.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('registrar_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('registrar_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="hosting_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Хостинг
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="hosting_id"
                v-model="order.hosting_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('hosting_id')"
              >
                <option
                  v-for="hosting in hostings"
                  :key="hosting.id"
                  :value="hosting.id"
                  v-text="hosting.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('hosting_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('hosting_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="sp_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Safe page
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="sp_id"
                v-model="order.sp_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('sp_id')"
              >
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
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="bp_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Black page
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="bp_id"
                v-model="order.bp_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('bp_id')"
              >
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
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Cloudflare
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs">
              <toggle v-model="order.useCloudflare"></toggle>
            </div>
            <span
              v-if="errors.has('useCloudflare')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('useCloudflare')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Конструктор
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs">
              <toggle v-model="order.useConstructor"></toggle>
            </div>
            <span
              v-if="errors.has('useConstructor')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('useConstructor')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="landing_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Ленд на который лить
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                v-model="order.landing_id"
                :show-labels="false"
                :options="landings"
                placeholder="Выберите лендинг"
                track-by="id"
                label="url"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('landing_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('landing_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="deadline_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Необходимо выполнить до
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <flat-pickr
                id="deadline_at"
                v-model="order.deadline_at"
                class="w-full px-1 py-2 border rounded text-gray-600"
                :config="pickerConfig"
                placeholder="Выберите даты"
              ></flat-pickr>
            </div>
            <span
              v-if="errors.has('deadline_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('deadline_at')"
            ></span>
          </div>
        </div>

        <div class="w-full flex justify-end mt-5">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="$router.push({name:'orders.index'})"
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
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'orders-form',
  components:{
    flatPickr,
  },
  props: {
    id: {
      type: [String,Number],
      required :false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    order: {
      links_count : null,
      cloak_id : null,
      registrar_id:null,
      binom_id: null,
      linkType: null,
      useCloudflare:false,
      useConstructor:false,
      sp_id:null,
      bp_id:null,
      landing_id: null,
      hosting_id:null,
      deadline_at:null,
    },
    types:[
      {id:'domains', name: 'Домены'},
      {id:'subdomains', name: 'Субдомены'},
    ],
    campaigns:[],
    landings:[],
    registrars:[],
    cloaks:[],
    pages:[],
    hostings:[],
    errors: new ErrorBag(),
    pickerConfig: {
      enableTime: true,
      dateFormat: 'Y-m-d H:i',
    },
  }),
  computed:{
    isUpdating(){
      return this.$props.id !== null;
    },
    safePages(){
      return this.pages.filter(page => page.type === 'safe');
    },
    blackPages(){
      return this.pages.filter(page => page.type === 'black');
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      this.getBinomCampaigns();
      this.getRegistrars();
      this.getCloaks();
      this.getPages();
      this.getLandings();
      this.getHostings();
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/orders/${this.id}`)
        .then(r => this.order = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить заказ', body: err.response.message});
        });
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    create(){
      this.isBusy = true;
      axios.post('/api/orders/',this.order)
        .then(r => {
          this.$router.push({name:'orders.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить заказ', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/orders/${this.order.id}`,this.order)
        .then(r => this.$router.push({name:'orders.show', params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить заказ', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    getBinomCampaigns(){
      axios.get('/api/binom/campaigns').then(r => this.campaigns = r.data);
    },
    getRegistrars(){
      axios.get('/api/registrars').then(r => this.registrars = r.data);
    },
    getCloaks(){
      axios.get('/api/cloaks').then(r => this.cloaks = r.data);
    },
    getPages(){
      axios.get('/api/pages', {params: {all: true}}).then(r => this.pages = r.data);
    },
    getLandings(){
      axios.get('/api/domains', {params: {type: 'landing', status: 'ready', perPage: 100}}).then(r => this.landings = r.data.data);
    },
    getHostings(){
      axios.get('/api/hostings').then(r => this.hostings = r.data);
    },
  },
};

</script>

<style scoped>
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
