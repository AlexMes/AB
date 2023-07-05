<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <span class="inline-flex rounded-md shadow-sm">
          <a
            v-if="$root.isAdmin"
            class="cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            @click="$modal.show('add-domain-sms-campaign-modal')"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400"
              fixed-width
            ></fa-icon>
            <span>
              Добавить
            </span>
          </a>
        </span>
      </div>
    </div>

    <div
      v-if="hasCampaigns"
      class="w-full overflow-scroll"
    >
      <table class="table table-auto w-full">
        <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full">
          <tr class="px-3">
            <th class="px-2 py-3 pl-5">
              #
            </th>
            <th>Название</th>
            <th>Ленд</th>
            <th>Тип</th>
            <th>Филиал</th>
            <th>Сообщений</th>
            <th>Статус</th>
            <th>Текст</th>
            <th>Через X мин</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="w-full">
          <sms-campaigns-list-item
            v-for="campaign in campaigns"
            :key="campaign.id"
            :campaign="campaign"
            :inline-editing="true"
            :branches="branches"
            @updated="campaignUpdated"
          >
          </sms-campaigns-list-item>
        </tbody>
      </table>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <add-domain-sms-campaign-modal
      :domain-id="id"
      :branches="branches"
    ></add-domain-sms-campaign-modal>
  </div>
</template>

<script>
import AddDomainSmsCampaignModal from '../../components/sms/add-domain-sms-campaign-modal';
export default {
  name: 'domains-sms-campaigns',
  components: {
    AddDomainSmsCampaignModal,
  },
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    campaigns: [],
    branches: [],
    response: null,
  }),
  computed:{
    hasCampaigns() {
      return this.campaigns.length > 0;
    },
  },
  created() {
    this.load();
    this.listen();
    this.loadBranches();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/domains/${this.id}/sms-campaigns`, {params: {page, paginate: true}})
        .then(response => {
          this.campaigns = response.data.data;
          this.response = response.data;
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load campaigns.',
          }),
        );
    },
    campaignUpdated(event) {
      const index = this.campaigns.findIndex(c => c.id === event.campaign.id);
      if (index !== -1) {
        this.$set(this.campaigns, index, event.campaign);
      }
    },
    listen() {
      this.$eventHub.$on('domain-sms-campaign-added', ({campaign}) => {
        this.campaigns.unshift(campaign);
      });
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
