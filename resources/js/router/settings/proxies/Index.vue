<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск прокси"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'proxies.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasProxies"
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
                      Хост
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Логин
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Гео
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Филиал
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Активно
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <proxy-list-item
                    v-for="proxy in proxies"
                    :key="proxy.id"
                    :proxy="proxy"
                    @deleted="remove"
                  ></proxy-list-item>
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
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Прокси не найдено</h2>
    </div>
  </div>
</template>

<script>
import ProxyListItem from '../../../components/settings/proxy-list-item';
export default {
  name: 'proxies-index',
  components: {ProxyListItem},
  data:() => ({
    proxies: {},
    response: {},
    search: null,
  }),
  computed:{
    hasProxies() {
      return this.proxies !== undefined && this.proxies.length > 0;
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
      axios.get('/api/proxies', {
        params: {
          paginate: true,
          page: page,
          search: this.search === '' ? null : this.search,
        },
      })
        .then(response => {
          this.response = response.data;
          this.proxies = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить прокси.', message: err.response.data.message});
        });
    },
    remove(event) {
      const index = this.proxies.findIndex(proxy => proxy.id === event.proxy.id);
      if (index !== -1) {
        this.proxies.splice(index, 1);
      }
    },
  },
};
</script>
