<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Статистика
      </h1>
      <router-link
        :to="{ name: 'telegram.stats.bulk' }"
        class="button btn-primary text-white"
      >
        <fa-icon
          :icon="['far', 'plus']"
          class="fill-current mr-2"
        ></fa-icon>
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasStatistics"
      class="w-full shadow rounded"
    >
      <table class="table table-auto w-full">
        <thead>
          <tr>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
            >
              Date
            </th>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
            >
              Channel
            </th>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
            >
              Cost
            </th>
            <th
              class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
            >
              Impressions
            </th>
          </tr>
        </thead>
        <tbody>
          <channel-stats-list-item
            v-for="statistic in statistics"
            :key="statistic.id"
            :statistic="statistic"
          ></channel-stats-list-item>
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
export default {
  name: 'index-stats',
  data: () => ({
    response: null,
    statistics: [],
  }),
  computed: {
    hasStatistics() {
      return this.statistics.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios
        .get('/api/telegram/statistics')
        .then(response => {
          this.response = response.data;
          this.statistics = response.data.data;
        })
        .catch(error =>
          this.$toast.error({
            title: 'Failed',
            message: 'Cant load stats',
          }),
        );
    },
  },
};
</script>

<style scoped></style>
