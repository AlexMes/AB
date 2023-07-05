<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          Приложения
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div class="mt-8">
          <div class="flex flex-col">
            <form class="flex flex-col md:flex-row items-center mb-6">
              <!--<div class="w-1/6 md:w-1/5 mr-4 sm:col-span-3">
                <label
                  for="offer"
                  class="block text-sm font-medium leading-5 text-gray-700"
                >
                  Офер
                </label>
                <div class="mt-1">
                  offer filter..
                </div>
              </div>
              <div class="mx-4 mt-5 sm:col-span-3">
                <span class="inline-flex rounded-md shadow-sm">
                  <button
                    type="submit"
                    class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Фильтровать
                  </button>
                </span>
              </div>-->

              <div class="sm:col-span-3">
                <span class="inline-flex rounded-md shadow-sm">
                  <router-link
                    :to="{name: 'applications.create'}"
                  >
                    <span class="inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
                      <svg
                        viewBox="0 0 128 128"
                        width="16px"
                        height="16px"
                        fill="white"
                        class="mr-2"
                      >
                        <path
                          d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"
                        /><path
                          d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"
                        />
                      </svg>
                      Добавить приложение
                    </span>
                  </router-link>
                </span>
              </div>
            </form>
            <div class="relative flex pb-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div
                v-if="hasApplications"
                class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto sm:px-6 lg:px-8 max-w-8xl"
              >
                <div
                  class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg"
                >
                  <table class="min-w-full">
                    <thead>
                      <tr>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          ID
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          Name
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          Market ID
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          URL
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          GEO
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          Active
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white">
                      <tr
                        v-for="application in applications"
                        :key="application.id"
                        class="hover:bg-gray-100"
                      >
                        <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                          <router-link
                            :to="{name: 'applications.links', params: {id: application.id}}"
                            class="text-sm font-thin"
                            v-text="application.id"
                          ></router-link>
                        </td>
                        <td class="cursor-pointer px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                          <router-link
                            :to="{name: 'applications.links', params: {id: application.id}}"
                            class="text-sm font-normal"
                            v-text="application.name"
                          ></router-link>
                        </td>
                        <td
                          class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                          v-text="application.market_id"
                        >
                        </td>
                        <td
                          class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                          v-text="application.url"
                        >
                        </td>
                        <td
                          class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                          v-text="application.geo"
                        >
                        </td>
                        <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                          <span
                            class="inline-flex px-2 py-2 rounded-full"
                            :class="application.enabled ? 'bg-green-500' : 'bg-red-500'"
                          ></span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <pagination
                    :response="response"
                    @load="loadApplications"
                  ></pagination>
                </div>
              </div>
              <div
                v-else
                class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow"
              >
                <p>Приложений не найдено</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Pagination from '../../components/pagination';
export default {
  name: 'applications-index',
  components: {Pagination},
  data: () => {
    return {
      applications: [],
      response: {},
    };
  },
  computed: {
    hasApplications() {
      return this.applications.length > 0;
    },
  },
  created() {
    this.loadApplications();
  },
  methods: {
    loadApplications(page = 1) {
      axios.get('/api/applications', {
        params: {
          page,
        },
      })
        .then(r => {
          this.applications = r.data.data;
          this.response = r.data;
        })
        .catch(err => this.$toast.error({title: 'Unable load applications.', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>

</style>
