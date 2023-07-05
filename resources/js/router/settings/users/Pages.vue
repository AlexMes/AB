<template>
  <div class="w-full">
    <div v-if="hasPages">
      <pages-list-item
        v-for="page in pages"
        :key="page.id"
        :page="page"
      >
      </pages-list-item>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
      <page-comments-list></page-comments-list>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Страниц не найдено</p>
    </div>
  </div>
</template>

<script>
import PagesListItem from '../../../components/profiles/pages/pages-list-item';
export default {
  name: 'users-pages',
  components: {PagesListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    pages: [],
    response: null,
  }),
  computed:{
    hasPages() {
      return this.pages.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/users/${this.id}/pages`, {params: {page}})
        .then(({data}) => {this.response = data; this.pages = data.data;})
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить страницы.',
            message: e.response.data.message,
          }),
        );
    },
  },
};
</script>
