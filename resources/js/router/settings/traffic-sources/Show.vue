<template>
  <div class="w-full h-auto bg-white rounded shadow mb-8">
    <div
      v-if="trafficSource"
      class="w-full h-auto mb-8"
    >
      <div class="px-4 py-2 flex flex-row justify-between border-b items-center">
        <div
          class="text-2xl font-bold p-3 text-gray-700"
          v-text="trafficSource.name"
        ></div>
        <div>
          <router-link
            :to="{name: 'traffic-sources.update', params:{id:id}}"
            class="button btn-primary"
          >
            Редактировать
          </router-link>
        </div>
      </div>
    </div>

    <div class="bg-white shadow px-4 border-b border-gray-200 sm:px-6">
      <div>
        <div>
          <nav class="-mb-px flex">
            <router-link
              :to="{
                name: 'traffic-sources.domains',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Домены
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
  name: 'traffic-sources-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      trafficSource:null,
    };
  },
  created(){
    this.load();
  },
  methods: {
    load(){
      axios.get(`/api/traffic-sources/${this.id}`)
        .then(({data}) => this.trafficSource = data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить источник траффика.', message: e.response.data.message}));
    },
  },
};
</script>
