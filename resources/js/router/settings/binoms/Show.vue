<template>
  <div class="w-full h-auto mb-8">
    <div class="w-full h-auto mb-8 bg-white rounded shadow">
      <div
        v-if="binom"
        class="w-full h-auto"
      >
        <div
          class="flex flex-row items-center justify-between px-4 py-2 border-b"
        >
          <div class="p-3 text-2xl font-bold text-gray-700">
            <span
              v-if="binom.enabled"
              class="inline-block w-4 h-4 bg-green-500 rounded-full"
            ></span>
            <span
              v-else
              class="inline-block w-4 h-4 bg-red-500 rounded-full"
            ></span>
            {{ binom.name }}
          </div>
          <div>
            <router-link
              :to="{ name: 'binoms.update' }"
              class="button btn-primary"
            >
              Редактировать
            </router-link>
          </div>
        </div>
      </div>
      <div class="flex">
        <div
          class="flex flex-col flex-1 text-gray-700 bg-white shadow no-last-border"
        >
          <div class="flex flex-row p-3 border-b">
            <div class="flex w-1/4">
              <strong>Название</strong>
            </div>
            <div class="flex w-3/4">
              <span v-text="binom.name"></span>
            </div>
          </div>
          <div class="flex flex-row p-3 border-b">
            <div class="flex w-1/4">
              <strong>URL</strong>
            </div>
            <div class="flex w-3/4">
              <span v-text="binom.url"></span>
            </div>
          </div>
          <div class="flex flex-row p-3 border-b">
            <div class="flex w-1/4">
              <strong>Access Token</strong>
            </div>
            <div class="flex w-3/4">
              <span v-text="binom.access_token"></span>
            </div>
          </div>
          <div class="flex flex-row p-3 border-b">
            <div class="flex w-1/4">
              <strong>Status</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="binom.enabled ? 'Active' : 'Disabled'"
              ></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="hasTrafficSources"
    >
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-gray-700">
          Источники трафика
        </h1>
      </div>
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>TS ID</th>
              <th>Name</th>
              <th>Traffic sources</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <binom-traffic-source-list-item
              v-for="trafficSource in binom.traffic_sources"
              :key="trafficSource.id"
              :binom-traffic-source="trafficSource"
              :inner-traffic-sources="innerTrafficSources"
              @traffic-source-updated="updateTrafficSource"
            ></binom-traffic-source-list-item>
          </tbody>
        </table>
      </div>
    </div>
    <div
      v-if="hasCampaign"
    >
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-gray-700">
          Компании
        </h1>
      </div>
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>Campaign ID</th>
              <th>Name</th>
              <th>Offer</th>
              <th>Binom</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <binom-campaign-list-item
              v-for="campaign in campaigns"
              :key="campaign.id"
              :campaign="campaign"
              @binom-campaign-updated="update"
            ></binom-campaign-list-item>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import BinomTrafficSourceListItem from '../../../components/settings/binom-traffic-source-list-item';
export default {
  name: 'binoms-show',
  components: {BinomTrafficSourceListItem},
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      binom: {},
      campaigns: {},
      innerTrafficSources: [],
    };
  },
  computed:{
    hasCampaign(){
      if (this.campaigns.data) {
        return this.campaigns.data.length > 0;
      }
      return this.campaigns.length > 0;
    },
    hasTrafficSources() {
      return !!this.binom.traffic_sources && this.binom.traffic_sources.length > 0;
    },
  },
  created() {
    this.load();
    this.loadCampaigns();
    this.loadInnerTrafficSources();
  },
  methods: {
    load() {
      axios
        .get(`/api/binoms/${this.id}`)
        .then(r => (this.binom = r.data))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить форму.',
            message: e.response.data.message,
          }),
        );
    },
    loadCampaigns(){
      axios.get('/api/binom/campaigns', {params:{binom_id: this.id}})
        .then(r => this.campaigns = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить компании.', message: err.response.data.message});
        });
    },
    loadInnerTrafficSources() {
      axios.get('/api/traffic-sources')
        .then(r => this.innerTrafficSources = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить внутренние источники трафика.', message: err.response.data.message});
        });
    },
    update(event) {
      const index = this.campaigns.findIndex(item => item.id == event.campaign.id);
      if (index !== -1) {
        this.$set(this.campaigns, index, event.campaign);
      }
    },
    updateTrafficSource(event) {
      const index = this.binom.traffic_sources.findIndex(source => source.id === event.binomTrafficSource.id);
      if (index !== -1) {
        this.$set(this.binom.traffic_sources, index, event.binomTrafficSource);
      }
    },
  },
};
</script>
