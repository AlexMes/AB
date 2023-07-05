<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Страницы
      </h1>
    </div>
    <div class="flex mb-8">
      <search-field
        class="w-4/5"
        @search="search"
      ></search-field>
      <div class="w-1/5">
        <multiselect
          v-model="selectedProfiles"
          :options="profiles"
          :multiple="true"
          :show-labels="false"
          label="name"
          placeholder="Выберите профиль"
          track-by="id"
          :loading="isLoading"
          @search-change="searchProfile"
        ></multiselect>
      </div>
    </div>
    <div
      v-if="hasPages"
      class="shadow"
    >
      <div class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
        <div class="w-1/5">
          ID
        </div>
        <div class="w-1/5">
          Профиль
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
      >
      </pages-list-item>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <page-comments-list></page-comments-list>
  </div>
</template>

<script>
import SearchField from '../../components/SearchField';
import PagesListItem from '../../components/profiles/pages/pages-list-item';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
  name: 'all-profile-pages',
  components: {SearchField, PagesListItem, Multiselect},
  data: () => ({
    response: {},
    pages: [],
    needle: null,
    profiles: [],
    selectedProfiles: [],
    isLoading: false,
  }),
  computed:{
    hasPages(){
      return this.pages.length > 0;
    },
    needleProfiles() {
      if (this.selectedProfiles.length > 0) {
        return this.selectedProfiles.map(profile => profile.id);
      }
      return null;
    },
  },
  watch:{
    needle(){
      this.load();
    },
    selectedProfiles() {
      this.load();
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      this.isLoading = true;
      axios.get('/api/profile-pages', {params: {
        search: this.needle,
        page: page,
        profile_id: this.needleProfiles,
      }})
        .then(response => {
          this.response = response.data;
          this.pages = response.data.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить фан-пейджи.',
            message: err.response.data.message,
          });
        }).finally(() => this.isLoading = false);
    },
    search(needle) {
      this.needle = needle;
    },
    searchProfile(query) {
      axios.get('/api/profiles', {
        params: {
          all: true,
          search: query,
        },
      })
        .then(r => {
          this.profiles = r.data;
        })
        .catch(e => {
          this.$toast.error({title: 'Ошибка', message: e.response.message});
        });
    },
  },
};
</script>

<style scoped>

</style>
