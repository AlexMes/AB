<template>
  <modal
    name="add-application-link-modal"
    height="auto"
    :adaptive="true"
    styles="overflow: visible"
  >
    <form class="p-6">
      <div class="sm:flex sm:items-start">
        <div class="mt-1 sm:text-left w-full">
          <h3
            id="modal-headline"
            class="text-lg font-medium leading-6 text-gray-900"
          >
            Создание ссылки
          </h3>

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

          <form class="mt-4 sm:mt-0">
            <div
              class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5"
            >
              <label
                for="url"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                URL
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="rounded-md shadow-sm">
                  <input
                    id="url"
                    v-model="link.url"
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
                <div class="rounded-md shadow-sm">
                  <multiselect
                    id="geo"
                    v-model="link.countries"
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
                for="user"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Байер
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="rounded-md shadow-sm">
                  <multiselect
                    id="user"
                    v-model="link.user"
                    :show-labels="false"
                    :multiple="false"
                    :options="users"
                    placeholder="Выберите байера"
                    track-by="id"
                    label="name"
                    @input="errors.clear('user_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('user_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('user_id')"
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
                  <toggle v-model="link.enabled"></toggle>
                </div>
                <p
                  v-if="errors.has('enabled')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('enabled')"
                ></p>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="mt-8 sm:mt-8 sm:flex sm:flex-row-reverse">
        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
          <button
            type="submit"
            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 sm:text-sm sm:leading-5"
            :disabled="isBusy"
            @click.prevent="save"
          >
            Создать
          </button>
        </span>
        <span class="flex w-full mt-3 rounded-md shadow-sm sm:mt-0 sm:w-auto">
          <button
            type="button"
            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium leading-6 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue sm:text-sm sm:leading-5"
            @click="$modal.hide('add-application-link-modal')"
          >
            Отмена
          </button>
        </span>
      </div>
    </form>
  </modal>
</template>

<script>
import ErrorBag from '../../../../../../resources/js/utilities/ErrorBag';
import Toggle from '../toggle';
export default {
  name: 'add-application-link-modal',
  components: {
    Toggle,
  },
  props: {
    applicationId: {
      type: [String, Number],
      required: true,
    },
    countries: {
      type: Array,
      required: true,
    },
    users: {
      type: Array,
      required: true,
    },
  },
  data: () => ({
    isBusy: false,
    link: {
      user: null,
      url: null,
      geo: null,
      enabled: false,
      countries: [],
    },
    errors: new ErrorBag(),
  }),
  computed: {
    cleanForm() {
      return {
        user_id: !this.link.user ? null : this.link.user.id,
        url: this.link.url,
        geo: this.link.countries.map(country => country.code).join(','),
        enabled: this.link.enabled,
      };
    },
  },
  methods: {
    save() {
      this.isBusy = true;
      axios
        .post(`/api/applications/${this.applicationId}/links`, this.cleanForm)
        .then(({data}) => {
          this.$toast.success({title: 'Success', message: 'Link has been added.'});
          this.$modal.hide('add-application-link-modal');
          this.$eventHub.$emit('link-created', {link: data});
          this.link = {
            user_id: null,
            url: null,
            geo: null,
            enabled: false,
            countries: [],
          };
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Unable to create link.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

