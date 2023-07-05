<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск групп"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'groups.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasGroups"
      class="flex flex-col w-full bg-white shadow"
    >
      <group-list-item
        v-for="group in groups"
        :key="group.id"
        :group="group"
      ></group-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Групп не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import GroupListItem from '../../../components/settings/group-list-item';
import Pagination from '../../../components/pagination';

export default {
  name: 'groups-index',
  components: {
    GroupListItem,
    Pagination,
  },
  data:() => ({
    groups:{},
    response: {},
    search: '',
  }),
  computed:{
    hasGroups(){
      return this.groups !== undefined && this.groups.length > 0;
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
    load(page = null){
      let params = {
        page: page,
        search: this.search === '' ? null : this.search,
      };
      axios.get('/api/groups', {params: params})
        .then(({data}) => {
          this.response = data;
          this.groups = data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить группы.', message: err.response.data.message});
        });
    },
  },
};
</script>
