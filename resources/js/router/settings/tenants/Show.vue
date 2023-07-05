<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <div
        class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap"
      >
        <div class="ml-4 mt-4">
          <div class="flex flex-col justify-center">
            <h3
              class="text-lg leading-6 font-medium text-gray-900 break-all"
              v-text="tenant.name"
            ></h3>
          </div>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0 flex">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{ name: 'tenants.update', params: { id: id } }"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            >
              <fa-icon
                :icon="['far', 'pencil-alt']"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Редактировать
              </span>
            </router-link>
          </span>
        </div>
      </div>

      <div
        v-if="tenant"
        class="px-4 py-5 sm:p-0 mt-5"
      >
        <dl>
          <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Key
            </dt>
            <dd
              class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="`${tenant.key}`"
            >
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              URL
            </dt>
            <dd
              class="mt-1 text-sm break-all leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="tenant.url"
            >
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Client ID
            </dt>
            <dd
              class="mt-1 text-sm break-all leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="tenant.client_id"
            >
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Client secret
            </dt>
            <dd
              class="mt-1 text-sm break-all leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
              v-text="tenant.client_secret"
            >
            </dd>
          </div>
          <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:py-5">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              API token
            </dt>
            <dd class="mt-1 text-sm break-all leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="flex justify-between">
                <span
                  class="w-5/6 break-all"
                  v-text="tenant.api_token"
                ></span>
                <div class="w-1/6 ml-1 pt-1 flex justify-end">
                  <fa-icon
                    :icon="['far', 'copy']"
                    class="text-gray-500 fill-current hover:text-teal-700 cursor-pointer"
                    @click="$clipboard(tenant.api_token)"
                  >
                  </fa-icon>
                  <fa-icon
                    :icon="['far', 'sync']"
                    class="ml-1 text-gray-500 fill-current hover:text-teal-700 cursor-pointer"
                    @click="generateApiToken"
                  >
                  </fa-icon>
                  <fa-icon
                    :icon="['far', 'times-circle']"
                    class="ml-1 text-gray-500 fill-current hover:text-red-700 cursor-pointer"
                    @click="revokeApiToken"
                  >
                  </fa-icon>
                </div>
              </div>
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'tenants-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      tenant: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/tenants/${this.id}`)
        .then(r => (this.tenant = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить арендатора.',
            message: e.response.data.message,
          });
        });
    },
    generateApiToken() {
      if (confirm('Уверены? Старый токен будет неактивен.')) {
        axios.post(`/api/tenants/${this.id}/generate-api-token`)
          .then(response => this.tenant.api_token = response.data)
          .catch(err => this.$toast.error({title: 'Unable to generate tenant\'s api token.', message: err.response.data.message}));
      }
    },
    revokeApiToken() {
      if (confirm('Уверены? Доступ для тенента будет отключён.')) {
        axios.post(`/api/tenants/${this.id}/revoke-api-token`)
          .then(response => this.tenant.api_token = null)
          .catch(err => this.$toast.error({title: 'Unable to revoke tenant\'s api token.', message: err.response.data.message}));
      }
    },
  },
};
</script>
