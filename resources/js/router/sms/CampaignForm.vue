<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="campaign.title"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый шаблон СМС
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
            for="title"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="title"
                v-model="campaign.title"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('title')"
              />
            </div>
            <span
              v-if="errors.has('title')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('title')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="type"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Тип
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="type"
                v-model="campaign.type"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('type')"
              >
                <option
                  v-for="type in types"
                  :key="type.id"
                  :value="type.id"
                  v-text="type.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('type')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('type')"
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
            Ленд
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="landing_id"
                v-model="campaign.landing_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('landing_id')"
              >
                <option
                  v-for="landing in landings"
                  :key="landing.id"
                  :value="landing.id"
                  v-text="landing.url"
                ></option>
              </select>
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
            for="branch"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Филиал
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="branch"
                v-model="campaign.branch_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
              >
                <option :value="null">
                  Не выбран
                </option>
                <option
                  v-for="branch in branches"
                  :key="branch.id"
                  :value="branch.id"
                  v-text="branch.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('branch_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('branch_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="text"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Текст сообщения
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="text"
                v-model="campaign.text"
                type="text"
                rows="3"
                required
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('text')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('text')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('text')"
            ></span>
          </div>
        </div>

        <div
          v-if="campaign.type === 'delayed'"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="after_minutes"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Отправлять через Х минут
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="after_minutes"
                v-model="campaign.after_minutes"
                type="number"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('after_minutes')"
              />
            </div>
            <span
              v-if="errors.has('after_minutes')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('after_minutes')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Статус
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="campaign.status"></toggle>
            </div>
            <span
              v-if="errors.has('status')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('status')"
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

export default {
  name: 'campaigns-form',
  props: {
    id: {
      type: [String,Number],
      required :false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    campaign: {
      title : null,
      text : null,
      type: 'instant',
      after_minutes: null,
      landing_id: null,
      branch_id: null,
      status:false,
    },
    types:[
      {id:'instant', name: 'Сразу'},
      {id:'delayed', name: 'Отложенная'},
    ],
    landings:[],
    branches: [],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.$props.id !== null;
    },
  },
  created() {
    this.campaign.landing_id = this.$route.params.landingId || null;
    this.boot();
  },
  methods:{
    boot(){
      this.getLandings();
      this.loadBranches();
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/sms/campaigns/${this.id}`)
        .then(r => this.campaign = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить', body: err.response.message});
        });
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'sms.campaigns.show', params:{id: this.id}})
        : this.$router.push({name:'sms.campaigns.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/sms/campaigns/',this.campaign)
        .then(r => {
          this.$router.push({name:'sms.campaigns.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/sms/campaigns/${this.campaign.id}`,this.campaign)
        .then(r => this.$router.push({name:'sms.campaigns.show', params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    getLandings(){
      axios.get('/api/domains',{params:{type:'landing', status:'ready', perPage:100}}).then(r => this.landings = r.data.data);
    },
    loadBranches() {
      axios
        .get('/api/branches')
        .then(({ data }) => (this.branches = data))
        .catch(err =>
          this.$toast.error({
            title: 'Something wrong is happened.',
            message: err.response.statusText,
          }),
        );
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
