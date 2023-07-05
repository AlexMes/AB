<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        ФБ Приложения
      </h1>
      <router-link
        :to="{'name':'facebook.apps.create'}"
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
            <th class="px-6 py-3 bg-gray-50"></th>
          </tr>
        </thead>
        <tbody>
          <facebook-app-list-item
            v-for="app in response.data"
            :key="app.id"
            :app="app"
          ></facebook-app-list-item>
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
import FacebookAppListItem from '../../../components/apps/facebook-app-list-item.vue';

export default {
  name: 'facebook-apps-index',
  components: {FacebookAppListItem},
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
  beforeRouteEnter(to, from, next) {
    next(vm => vm.load());
  },
  methods:{
    load(page = 1){
      axios.get('/api/facebook/apps', {params:{page: page}})
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
