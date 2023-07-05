<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск подходов"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'tags.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasTags"
      class="flex flex-col w-full bg-white shadow"
    >
      <tag-list-item
        v-for="tag in tags"
        :key="tag.id"
        :tag="tag"
      ></tag-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Подходов не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import TagListItem from '../../../components/settings/tags-list-item';
import Pagination from '../../../components/pagination';

export default {
  name: 'tags-index',
  components: {
    TagListItem,
    Pagination,
  },
  data:() => ({
    tags:{},
    response: {},
    search: '',
  }),
  computed:{
    hasTags(){
      return this.tags !== undefined && this.tags.length > 0;
    },
  },

  watch: {
    search() {
      this.load();
    },
  },

  created(){
    this.load();
  },

  methods:{
    load(page = null){
      let params = {
        page: page,
        search: this.search === '' ? null : this.search,
      };
      axios.get('/api/tags', {params: params})
        .then(({data}) => {
          this.response = data;
          this.tags = data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить подходы.', message: err.response.data.message});
        });
    },
  },
};
</script>
