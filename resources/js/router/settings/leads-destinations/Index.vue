<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск дестинейшенов"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'leads-destinations.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasDestinations"
    >
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Id
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Название
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Драйвер
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Автологин
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Филиал
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Офис
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Активно
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <leads-destination-list-item
                    v-for="destination in destinations"
                    :key="destination.id"
                    :destination="destination"
                    @deleted="remove"
                  ></leads-destination-list-item>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
      <test-lead-destination-modal></test-lead-destination-modal>
      <collect-lead-destination-results-modal></collect-lead-destination-results-modal>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Дестинейшенов не найдено</h2>
    </div>
  </div>
</template>

<script>
import LeadsDestinationListItem from '../../../components/settings/leads-destination-list-item';
import TestLeadDestinationModal from '../../../components/settings/test-lead-destination-modal';
import CollectLeadDestinationResultsModal from '../../../components/settings/collect-lead-destination-results-modal';
export default {
  name: 'leads-destinations-index',
  components: {CollectLeadDestinationResultsModal, TestLeadDestinationModal, LeadsDestinationListItem},
  data:() => ({
    destinations:{},
    response: {},
    search: null,
  }),
  computed:{
    hasDestinations(){
      return this.destinations !== undefined && this.destinations.length > 0;
    },
  },
  watch: {
    search() {
      this.load();
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios.get('/api/leads-destinations', {
        params: {
          paginate: true,
          page: page,
          search: this.search === '' ? null : this.search,
        },
      })
        .then(response => {
          this.response = response.data;
          this.destinations = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить дестинейшены.', message: err.response.data.message});
        });
    },
    remove(event) {
      const index = this.destinations.findIndex(destination => destination.id === event.destination.id);
      if (index !== -1) {
        this.destinations.splice(index, 1);
      }
    },
  },
};
</script>
