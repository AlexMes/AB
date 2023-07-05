<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Связки
      </h1>
      <div class="flex">
        <search-field @search="search"></search-field>
        <router-link
          v-if="$root.isAdmin"
          :to="{'name':'bundles.create'}"
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
      v-if="hasBundles"
    >
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>Заголовок</th>
              <th>Оффер</th>
              <th>Связка</th>
              <th>Устройство</th>
              <th>Платформа</th>
              <th>Плейсменты</th>
              <th>Криатив</th>
              <th>Преленд</th>
              <th>Ленд</th>
            </tr>
          </thead>
          <tbody>
            <bundle-list-item
              v-for="bundle in response.data"
              :key="bundle.id"
              :bundle="bundle"
            ></bundle-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
  </div>
</template>

<script>
export default {
  name: 'bundles-index',
  data:() => ({
    query: null,
    response: {},
  }),
  computed:{
    hasBundles(){
      if (this.response.data) {
        return this.response.data.length > 0;
      }
      return this.response.length > 0;
    },
  },
  watch:{
    query(){
      this.load();
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(page = null){
      axios.get('/api/bundles', {params: {page: page, search: this.query}})
        .then(response => {
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить связки.', message: err.response.data.message});
        });
    },
    search(needle){
      this.query = needle;
    },
  },
};
</script>

