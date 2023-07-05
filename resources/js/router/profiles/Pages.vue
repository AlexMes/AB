<template>
  <div>
    <div v-if="hasPages">
      <div class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
        <div class="w-1/5">
          ID
        </div>
        <div class="w-2/5">
          Название
        </div>
        <div class="w-1/5">
          Комментарии
        </div>
      </div>
      <pages-list-item
        v-for="page in pages"
        :key="page.id"
        :page="page"
        :show-profile="false"
      >
      </pages-list-item>
      <page-comments-list></page-comments-list>
    </div>
    <div
      v-else
      class="text-center"
    >
      <h1 class="text-gray-700">
        Страниц не найдено
      </h1>
    </div>
  </div>
</template>

<script>
import PagesListItem from '../../components/profiles/pages/pages-list-item';
export default {
  name: 'pages',
  components: {PagesListItem},
  props: {
    id:{
      type: [Number, String],
      required: true,
      default: null,
    },
  },
  data: () => ({
    response: {},
    pages: [],
    needle: null,
  }),
  computed:{
    hasPages(){
      return this.pages.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios.get(`/api/profiles/${this.id}/pages`, {params: {search: this.needle, page: page}})
        .then(response => {
          this.pages = response.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить фан-пейджи.',
            message: err.response.data.message,
          });
        });
    },
  },
};
</script>

<style scoped>

</style>
