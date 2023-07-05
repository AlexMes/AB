<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="proxy.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый прокси
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
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
                v-model="proxy.name"
                type="text"
                placeholder=""
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
            for="protocol"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Протокол
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="protocol"
                v-model="proxy.protocol"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
              >
                <option
                  v-for="(protocol, index) in protocols"
                  :key="`protocol-${index}`"
                  :value="protocol"
                  v-text="protocol"
                ></option>
              </select>
            </div>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label
            for="host"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Хост
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="host"
                v-model="proxy.host"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('host')"
              />
            </div>
            <span
              v-if="errors.has('host')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('host')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label
            for="port"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Порт
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="port"
                v-model="proxy.port"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('port')"
              />
            </div>
            <span
              v-if="errors.has('port')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('port')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label
            for="login"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Логин
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="login"
                v-model="proxy.login"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('login')"
              />
            </div>
            <span
              v-if="errors.has('login')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('login')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label
            for="password"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Пароль
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="password"
                v-model="proxy.password"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('password')"
              />
            </div>
            <span
              v-if="errors.has('password')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('password')"
            ></span>
          </div>
        </div>

        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            Гео
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                v-model="proxy.geo"
                :show-labels="false"
                :multiple="false"
                :options="countries"
                placeholder="Выберите страну"
                track-by="country"
                label="country_name"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('geo')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('geo')"
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
                v-model="proxy.branch_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
              >
                <option :value="null">
                  Не выбрано
                </option>
                <option
                  v-for="branch in branches"
                  :key="branch.id"
                  :value="branch.id"
                  v-text="branch.name"
                ></option>
              </select>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Активно
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="proxy.is_active"></toggle>
            </div>
            <span
              v-if="errors.has('is_active')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('is_active')"
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
  name: 'proxies-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    proxy: {
      name: null,
      protocol: 'socks5',
      host: null,
      port: null,
      login: null,
      password: null,
      geo: null,
      branch_id: null,
      is_active: true,
    },
    countries: [],
    branches: [],
    protocols: ['socks5', 'http'],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
    cleanForm() {
      return {
        name: this.proxy.name,
        protocol: this.proxy.protocol,
        host: this.proxy.host,
        port: this.proxy.port,
        login: this.proxy.login,
        password: this.proxy.password,
        geo: !!this.proxy.geo ? this.proxy.geo.country : null,
        branch_id: this.proxy.branch_id,
        is_active: this.proxy.is_active,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      this.loadBranches();
      if (this.isUpdating) {
        this.load();
      } else {
        this.loadCountries();
      }
    },

    load() {
      axios.get(`/api/proxies/${this.id}`)
        .then(r => {
          this.proxy = r.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить прокси.', message: err.response.data.message});
        })
        .finally(() => this.loadCountries());
    },

    loadCountries() {
      axios.get('/api/geo/countries')
        .then(r => {
          this.countries = r.data;
          const index = this.countries.findIndex(country => country.country === this.proxy.geo);
          if (index !== -1) {
            this.proxy.geo = this.countries[index];
          }
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список стран.', message: e.response.data.message}));
    },

    loadBranches() {
      axios
        .get('/api/branches')
        .then(({ data }) => (this.branches = data))
        .catch(err => this.$toast.error({title: 'Unable to load branches.', message: err.response.statusText}));
    },

    save() {
      this.isUpdating ? this.update() : this.create();
    },

    create() {
      this.isBusy = true;
      axios.post('/api/proxies/', this.cleanForm)
        .then(r => {
          this.$router.push({name:'proxies.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить прокси.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    update() {
      this.isBusy = true;
      axios.put(`/api/proxies/${this.id}`, this.cleanForm)
        .then(r => this.$router.push({name:'proxies.show',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить прокси.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },

    cancel() {
      if (this.isUpdating) {
        this.$router.push({name:'proxies.show', params:{id: this.id}});
      } else {
        this.$router.push({name:'proxies.index'});
      }
    },
  },
};
</script>
