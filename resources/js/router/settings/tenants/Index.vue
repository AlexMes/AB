<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск арендатора"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{'name':'tenants.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasTenants"
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
                      Ключ
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      URL
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tenant-list-item
                    v-for="tenant in tenants"
                    :key="tenant.id"
                    :tenant="tenant"
                  ></tenant-list-item>
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
      <h2>Арендаторов не найдено</h2>
    </div>
  </div>
</template>

<script>
import TenantListItem from '../../../components/settings/tenant-list-item';
export default {
  name: 'tenants-index',
  components: {TenantListItem},
  data:() => ({
    tenants: [],
    response: {},
    search: null,
  }),
  computed:{
    hasTenants(){
      return this.tenants.length > 0;
    },
  },
  watch: {
    search() {
      this.load();
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(page = 1){
      axios.get('/api/tenants', {
        params: {
          page,
          search: this.search,
        },
      })
        .then(response => {
          this.response = response.data;
          this.tenants = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить арендаторов.', message: err.response.data.message});
        });
    },
  },
};
</script>
