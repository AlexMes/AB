<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск команд"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'teams.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasTeams"
      class="flex flex-col w-full bg-white shadow"
    >
      <team-list-item
        v-for="team in teams"
        :key="team.id"
        :team="team"
      ></team-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Команд не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import TeamListItem from '../../../components/settings/team-list-item';
export default {
  name: 'teams-index',
  components: {TeamListItem},
  data:() => ({
    teams:{},
    response: {},
    search: null,
  }),
  computed:{
    hasTeams(){
      return this.teams !== undefined && this.teams.length > 0;
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
      axios.get('/api/teams', {
        params: {
          page: page,
          search: this.search === '' ? null : this.search,
        },
      })
        .then(response => {
          this.response = response.data;
          this.teams = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить команды.', message: err.response.data.message});
        });
    },
  },
};
</script>
