<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <span class="inline-flex rounded-md shadow-sm">
          <router-link
            class="cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            :to="{name: 'status-configs.create', params: {officeId: id}}"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400"
              fixed-width
            ></fa-icon>
            <span>
              Добавить
            </span>
          </router-link>
        </span>
      </div>
    </div>
    <div
      v-if="hasConfigs"
    >
      <div
        class="overflow-x-auto overflow-y-hidden flex w-full bg-white shadow no-last-border"
      >
        <table class="w-full table table-auto relative">
          <thead
            class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky"
          >
            <tr>
              <th class="pl-5 px-2 py-3">
                #
              </th>
              <th class="px-2 py-3">
                Новый статус
              </th>
              <th class="px-2 py-3">
                Текущие статусы
              </th>
              <th class="px-2 py-3">
                Тип
              </th>
              <th class="px-2 py-3">
                N дней назад
              </th>
              <th class="px-2 py-3">
                Активно
              </th>
              <th class="px-2 py-3"></th>
            </tr>
          </thead>
          <tbody class="w-full">
            <status-config-list-item
              v-for="config in configs"
              :key="config.id"
              :config="config"
              @deleted="remove"
            ></status-config-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Конфигураций не найдено</p>
    </div>
  </div>
</template>

<script>
import StatusConfigListItem from '../../../components/settings/status-config-list-item';
export default {
  name: 'offices-status-configs',
  components: {StatusConfigListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    configs: [],
    response: null,
  }),
  computed:{
    hasConfigs() {
      return this.configs.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios
        .get(`/api/offices/${this.id}/status-configs`, {params: {page}})
        .then(response => {
          this.configs = response.data.data;
          this.response = response.data;
        })
        .catch(err => this.$toast.error({title: 'Error', message: 'Unable to load status configs.'}));
    },
    remove(event) {
      const index = this.configs.findIndex(config => config.id === event.config.id);
      if (index !== -1) {
        this.configs.splice(index, 1);
      }
    },
  },
};
</script>
