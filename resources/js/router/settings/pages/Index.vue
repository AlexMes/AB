<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск страницы"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        :to="{'name':'pages.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasPages"
      class="flex flex-col w-full bg-white shadow"
    >
      <pages-list-item
        v-for="page in pages"
        :key="page.id"
        :page="page"
        @pageDeleted="deletePage"
      ></pages-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Страниц не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import PagesListItem from '../../../components/settings/pages-list-item';
export default {
  name: 'pages-index',
  components: {PagesListItem},
  data:() => ({
    response: {},
    pages: [],
    needle: null,
  }),
  computed:{
    hasPages(){
      return this.pages.length > 0;
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(page = 1){
      axios.get('/api/pages', {params:{page, search: this.needle}})
        .then(response => {
          this.response = response.data;
          this.pages = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить страницы.', message: err.response.data.message});
        });
    },
    search(event) {
      this.needle = event.target.value === '' ? null : event.target.value;
      this.load();
    },
    deletePage(event) {
      const index = this.pages.findIndex(page => page.id === event.page.id);
      if (index !== -1) {
        this.pages.splice(index, 1);
      }
    },
  },
};
</script>
