<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <span class="inline-flex rounded-md shadow-sm">
          <router-link
            class="cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            :to="{ name: 'teams.create', params: { branchId: id } }"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400"
              fixed-width
            ></fa-icon>
            <span>
              Создать
            </span>
          </router-link>
        </span>
      </div>
    </div>
    <div
      v-if="hasTeams"
    >
      <div class="shadow">
        <team-list-item
          v-for="team in teams"
          :key="team.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :team="team"
        ></team-list-item>
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
      <p>Команд не найдено</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'branches-teams',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    teams: [],
    response: null,
  }),
  computed:{
    hasTeams() {
      return this.teams.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/branches/${this.id}/teams`, {params: {page}})
        .then(response => {
          this.teams = response.data.data;
          this.response = response.data;
        })
        .catch(err => this.$toast.error({title: 'Error', message: 'Unable to load teams'}));
    },
  },
};
</script>
