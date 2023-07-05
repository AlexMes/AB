<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <div
        class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap"
      >
        <div class="ml-4 mt-4">
          <div class="flex flex-col justify-center">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="team.name"
            ></h3>
          </div>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0 flex">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{name: 'teams.update', params: {id: id}}"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            >
              <fa-icon
                :icon="['far', 'pencil-alt']"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Редактировать
              </span>
            </router-link>
          </span>
        </div>
      </div>
    </div>
    <div class="bg-white shadow px-4 border-b border-gray-200 sm:px-6">
      <div>
        <div>
          <nav class="-mb-px flex">
            <router-link
              :to="{
                name: 'teams.users',
                params: {id: id}
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Пользователи
            </router-link>
          </nav>
        </div>
      </div>
    </div>
    <div class="">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'teams-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      team: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/teams/${this.id}`)
        .then(r => (this.team = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить команду.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
