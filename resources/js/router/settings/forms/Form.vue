<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="form.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новая форма
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
            for="name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Название формы"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('name')"
              />
            </div>
            <span
              v-if="errors.has('name')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('name')"
            ></span>
          </div>
        </div>

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
                v-model="form.url"
                type="text"
                placeholder="URL"
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
            for="method"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Method
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="method"
                v-model="form.method"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('method')"
              >
                <option value="get">
                  GET
                </option>
                <option value="post">
                  POST
                </option>
              </select>
            </div>
            <span
              v-if="errors.has('method')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('method')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="form_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            FORM_ID
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="form_id"
                v-model="form.form_id"
                type="text"
                placeholder="form ID"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('form_id')"
              />
            </div>
            <span
              v-if="errors.has('form_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('form_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="form_api_key"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            API KEY
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="form_api_key"
                v-model="form.form_api_key"
                type="text"
                placeholder="API KEY"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('form_api_key')"
              />
            </div>
            <span
              v-if="errors.has('form_api_key')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('form_api_key')"
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
                v-model="form.landing_id"
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
            for="provider"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Provider
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="provider"
                v-model="form.provider"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('provider')"
              >
                <option
                  v-for="provider in providers"
                  :key="provider"
                  :value="provider"
                  v-text="provider"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('provider')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('provider')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="phone_prefix"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Phone prefix
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="phone_prefix"
                v-model="form.phone_prefix"
                type="text"
                placeholder="Phone prefix"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('phone_prefix')"
              />
            </div>
            <span
              v-if="errors.has('phone_prefix')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('phone_prefix')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="external_affiliate_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Ext. affiliate id
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="external_affiliate_id"
                v-model="form.external_affiliate_id"
                type="text"
                placeholder="Ext. affiliate id"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('external_affiliate_id')"
              />
            </div>
            <span
              v-if="errors.has('external_affiliate_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('external_affiliate_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="external_offer_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Ext. offer id
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="external_offer_id"
                v-model="form.external_offer_id"
                type="text"
                placeholder="Ext. offer id"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('external_offer_id')"
              />
            </div>
            <span
              v-if="errors.has('external_offer_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('external_offer_id')"
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
              <toggle v-model="form.status"></toggle>
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
import ErrorBag from '../../../utilities/ErrorBag';

export default {
  name: 'forms-form',
  props: {
    id: {
      type: [String, Number],
      required :false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    form: {
      name: null,
      url: null,
      method: null,
      form_id: null,
      form_api_key: null,
      status: false,
      landing_id: null,
    },
    landings:[],
    providers: [
      'default', 'convertingteam', 'iwix', 'trafficon', 'bitrix', 'fxg24',
    ],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.$props.id !== null;
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      this.getLandings();
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/integrations/forms/${this.id}`)
        .then(r => this.form = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить форму.', message: err.response.data.message}));
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'forms.show', params:{id: this.id}})
        : this.$router.push({name:'forms.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/integrations/forms/',this.form)
        .then(r => {
          this.$router.push({name:'forms.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if (err.response.status) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить форму.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/integrations/forms/${this.form.id}`,this.form)
        .then(r => this.$router.push({name:'forms.show',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить форму.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    getLandings(){
      axios.get('/api/domains',{params:{type:'landing', status:'ready' ,perPage:100}}).then(r => this.landings = r.data.data);
    },
  },
};
</script>
