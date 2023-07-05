<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Приложения
      </h1>
      <router-link
        :to="{'name':'apps.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasApps"
    >
      <table class="min-w-full divide-y divide-gray-200 shadow">
        <thead>
          <tr>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Название
            </th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Статус
            </th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Ссылка
            </th>
            <th class="px-6 py-3 bg-gray-50"></th>
          </tr>
        </thead>
        <tbody>
          <apps-list-item
            v-for="app in response.data"
            :key="app.id"
            :app="app"
          ></apps-list-item>
        </tbody>
      </table>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Приложений не найдено</h2>
    </div>
  </div>
</template>

<script>
import AppsListItem from '../../../components/apps/app-list-item.vue';

export default {
  name: 'apps-index',
  components: {AppsListItem},
  data:() => ({
    response: {},
  }),
  computed:{
    hasApps(){
      return this.response.data !== undefined && this.response.data.length > 0;
    },
    apps() {
      return this.hasApps ? this.response.data : [];
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(page = 1){
      axios.get('/api/apps', {params:{page: page}})
        .then(({data}) => {
          this.response = data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить приложения.', message: err.response.data.message});
        });
    },
  },
};
</script>
