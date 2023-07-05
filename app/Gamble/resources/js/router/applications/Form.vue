<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          <span v-if="isEditing">Редактирование приложения</span>
          <span v-else>Создание приложения</span>
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div
          v-if="errors.hasMessage()"
          class="rounded-md bg-red-100 mt-6 p-4 max-w-7xl mx-auto"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-red-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p
                class="text-sm leading-5 font-medium text-red-800"
                v-text="errors.message"
              >
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
          <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="`[${application.id}] ${application.name}`"
            >
            </h3>
          </div>
          <form class="px-6">
            <div
              class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5"
            >
              <label
                for="name"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Название
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="name"
                    v-model="application.name"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('name')"
                  />
                </div>
                <p
                  v-if="errors.has('name')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('name')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Статус WebView
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs">
                  <toggle v-model="application.enabled"></toggle>
                </div>
                <p
                  v-if="errors.has('enabled')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('enabled')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="market_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Play Market ID
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="market_id"
                    v-model="application.market_id"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('market_id')"
                  />
                </div>
                <p
                  v-if="errors.has('market_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('market_id')"
                ></p>
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
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="url"
                    v-model="application.url"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('url')"
                  />
                </div>
                <p
                  v-if="errors.has('url')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('url')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="geo"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                GEO
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="geo"
                    v-model="application.countries"
                    :show-labels="false"
                    :multiple="true"
                    :options="countries"
                    placeholder="Выберите разрешенные гео"
                    track-by="code"
                    label="name"
                    @input="errors.clear('geo')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('geo')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('geo')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="fb_app_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Facebook App ID
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="fb_app_id"
                    v-model="application.fb_app_id"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('fb_app_id')"
                  />
                </div>
                <p
                  v-if="errors.has('fb_app_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('fb_app_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="fb_app_secret"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Facebook App Secret
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="fb_app_secret"
                    v-model="application.fb_app_secret"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('fb_app_secret')"
                  />
                </div>
                <p
                  v-if="errors.has('fb_app_secret')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('fb_app_secret')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="fb_app_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Facebook App Token
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="fb_token"
                    v-model="application.fb_token"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('fb_token')"
                  />
                </div>
                <p
                  v-if="errors.has('fb_token')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('fb_token')"
                ></p>
              </div>
            </div>
            <div class="mt-8 border-t border-gray-200 py-5">
              <div class="flex justify-end">
                <span class="inline-flex rounded-md shadow-sm">
                  <a
                    class="inline-flex cursor-pointer items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                    @click="cancel"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Отмена
                  </a>
                </span>
                <span class="ml-3 inline-flex rounded-md shadow-sm">
                  <button
                    type="submit"
                    class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
                    :disabled="isBusy"
                    @click.prevent="save"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Сохранить
                  </button>
                </span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import ErrorBag from '../../../../../../resources/js/utilities/ErrorBag';
import Toggle from '../../components/toggle';

export default {
  name: 'applications-form',
  components: {
    Toggle,
  },
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => {
    return {
      isBusy: false,
      application: {
        name: null,
        enabled: false,
        market_id: null,
        url: null,
        geo: null,
        countries: [],
        fb_app_id:null,
        fb_app_secret:null,
        fb_token:null,
      },
      countries: [],
      errors: new ErrorBag(),
    };
  },
  computed: {
    isEditing() {
      return this.id !== null;
    },
    cleanForm() {
      return {
        name: this.application.name,
        enabled: this.application.enabled,
        market_id: this.application.market_id,
        url: this.application.url,
        geo: this.application.countries.map(country => country.code).join(','),
        fb_app_id: this.application.fb_app_id,
        fb_app_secret: this.application.fb_app_secret,
        fb_token: this.application.fb_token,
      };
    },
  },
  created() {
    if (this.isEditing) {
      this.load();
    }
    this.loadCountries();
  },
  methods: {
    load() {
      axios.get(`/api/applications/${this.id}`)
        .then(({data}) => this.application = data)
        .catch(err => this.$toast.error({title: 'Unable to load the application.', message: err.response.data.message}));
    },
    loadCountries() {
      axios.get('/api/countries')
        .then(({data}) => {
          this.countries = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load countries.', message: err.response.data.message}));
    },
    save() {
      this.isBusy = true;
      this.isEditing ? this.update() : this.create();
    },
    cancel() {
      this.isEditing
        ? this.$router.push({name:'applications.links', params:{id: this.id}})
        : this.$router.push({name:'applications.index'});
    },
    create() {
      axios
        .post('/api/applications/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'applications.index',
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Unable to create application.',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
    update() {
      axios
        .put(`/api/applications/${this.application.id}`, this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'applications.links',
            params: { id: r.data.id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Unable to update application.',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
  },
};
</script>

<style scoped>

</style>
