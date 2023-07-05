<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Журнал профилей
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
        <router-link
          :to="{'name':'profile-logs.create'}"
          class="button btn-primary flex items-center ml-3"
        >
          <fa-icon
            :icon="['far','plus']"
            class="fill-current mr-2"
          ></fa-icon> Добавить
        </router-link>
      </div>
    </div>

    <div
      v-if="hasProfileLogs"
      class="flex flex-col shadow"
    >
      <profile-log-list-item
        v-for="profileLog in profileLogs"
        :key="profileLog.id"
        :profile-log="profileLog"
      ></profile-log-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Записей не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import ProfileLogListItem from '../../components/profile-logs/profile-log-list-item';
export default {
  name: 'index',
  components:{
    ProfileLogListItem,
  },
  data:() => ({
    response: {},
    profileLogs: [],
    needle: null,
  }),
  computed:{
    hasProfileLogs(){
      return this.profileLogs.length > 0;
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(page = 1){
      axios.get('/api/profile-logs', {params: {page: page, search: this.needle}})
        .then(response => {
          this.response = response.data;
          this.profileLogs = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить записи журнала.', message: err.response.data.message});
        });
    },
    search(needle){
      this.needle = needle === '' ? null : needle;
      this.load();
    },
  },
};
</script>

