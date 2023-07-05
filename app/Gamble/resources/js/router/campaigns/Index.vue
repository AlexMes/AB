<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
            Кампании
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div class="mt-8">
          <div class="flex flex-col">
            <form class="flex flex-col md:flex-row items-center mb-6">
              <div class="sm:col-span-3">
                <span class="inline-flex rounded-md shadow-sm">
                  <router-link
                    :to="{name: 'campaigns.create'}"
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
                      Добавить кампанию
                    </span>
                  </router-link>
                </span>
              </div>
            </form>
            <div class="relative flex pb-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div
                v-if="hasCampaigns"
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
                          Кампания
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          Аккаунт
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          Офер
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                          Создана
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white">
                      <campaign-list-item
                        v-for="campaign in campaigns"
                        :key="campaign.id"
                        :campaign="campaign"
                      >
                      </campaign-list-item>
                    </tbody>
                  </table>
                  <pagination
                    :response="response"
                    @load="load"
                  ></pagination>
                </div>
              </div>
              <div
                v-else
                class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow"
              >
                <p>Кампаний не найдено</p>
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
import CampaignListItem from '../../components/campaigns/campaign-list-item';
export default {
  name: 'campaigns-index',
  components: {CampaignListItem, Pagination},
  data: () => {
    return {
      campaigns: [],
      response: {},
    };
  },
  computed: {
    hasCampaigns() {
      return this.campaigns.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios.get('/api/campaigns', {
        params: {
          page,
        },
      })
        .then(r => {
          this.campaigns = r.data.data;
          this.response = r.data;
        })
        .catch(err => this.$toast.error({title: 'Unable load campaigns.', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>

</style>
