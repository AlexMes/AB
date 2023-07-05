<template>
  <div class="container mx-auto flex flex-col">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        СМС шаблоны
      </h1>
      <router-link
        :to="{name:'sms.campaigns.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasCampaigns"
      class="w-full"
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
            <th>Сообщений</th>
            <th>Статус</th>
          </tr>
        </thead>
        <tbody class="w-full">
          <sms-campaigns-list-item
            v-for="campaign in campaigns"
            :key="campaign.id"
            :campaign="campaign"
          >
          </sms-campaigns-list-item>
        </tbody>
      </table>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import Pagination from '../../components/pagination';
export default {
  name: 'index',
  components: {
    Pagination,
  },
  data: () => ({
    campaigns: [],
    response: {},
  }),
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
      axios
        .get('/api/sms/campaigns', {
          params: {
            page: page,
          },
        })
        .then(response => {
          this.response = response.data;
          this.campaigns = response.data.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить.',
            message: err.response.data.message,
          });
        });
    },
  },
};
</script>

<style scoped>
    th{
        @apply px-2;
        @apply py-3;
        @apply text-left;
    }
</style>
