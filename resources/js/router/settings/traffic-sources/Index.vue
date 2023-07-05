<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск источниов траффика"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        :to="{'name':'traffic-sources.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasTrafficSources"
      class="flex flex-col w-full bg-white shadow"
    >
      <traffic-source-list-item
        v-for="traffic in traffics"
        :key="traffic.id"
        :traffic-source="traffic"
      ></traffic-source-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Источников траффика не найдено</h2>
    </div>
  </div>
</template>

<script>
import TrafficSourceListItem from '../../../components/settings/traffic-source-list-item';

export default {
  name: 'traffic-sources-index',
  components: {TrafficSourceListItem},
  data:() => ({
    response: {},
  }),
  computed:{
    hasTrafficSources(){
      return this.response.data !== undefined && this.response.data.length > 0;
    },
    traffics() {
      return this.hasTrafficSources ? this.response.data : [];
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(search = null){
      axios.get('/api/traffic-sources', {params:{search: search}})
        .then(response => {
          this.response = response;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить источники траффика.', message: err.response.data.message});
        });
    },
    search(event) {
      this.load(event.target.value === '' ? null : event.target.value);
    },
  },
};
</script>
