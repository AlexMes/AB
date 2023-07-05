<template>
  <modal
    height="auto"
    name="page-comments-list"
    class="scrollable"
    @before-open="beforeOpen"
  >
    <div
      v-if="page"
      class="flex flex-col"
    >
      <div class="flex w-full justify-between p-4">
        <label
          class="flex w-full mb-2 font-semibold"
          v-text="`Страница ${page.name}`"
        ></label>
        <fa-icon
          :icon="['far','times-circle']"
          class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
          fixed-width
          @click="cancel"
        ></fa-icon>
      </div>
      <div v-if="hasComments">
        <div class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border">
          <div class="w-1/4">
            Дата публикации
          </div>
          <div class="w-1/4">
            Аккаунт
          </div>
          <div class="w-1/2">
            Комментарий
          </div>
        </div>
        <div
          v-for="comment in comments"
          :key="comment.id"
          class="flex w-full items-center p-3"
        >
          <div class="w-1/4 text-xs">
            <span v-text="comment.published_at"></span>
          </div>
          <div class="w-1/4">
            <span
              v-if="comment.account"
              v-text="comment.account.name"
            ></span>
            <span v-else></span>
          </div>
          <div class="w-1/2">
            <div class="flex w-full">
              <span
                class="w-full truncate hover:whitespace-normal"
                v-text="comment.text"
              ></span>
            </div>
          </div>
        </div>
      </div>
      <div
        v-else
        class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold no-last-border"
      >
        На странице еще не оставляли комментарии.
      </div>
    </div>
  </modal>
</template>

<script>
export default {
  name: 'page-comments-list',
  data:()=>({
    comments:[],
    page: null,
  }),
  computed:{
    hasComments(){
      return this.comments.length > 0;
    },
  },
  methods:{
    beforeOpen (event) {
      this.page = event.params.page;
      axios.get(`/api/profile-pages/${this.page.id}/fb-comments`)
        .then(response => this.comments = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить комментарии.', message: error.response.data.message});
        });
    },
    cancel(){
      this.comments = [];
      this.$modal.hide('page-comments-list');
    },
  },
};
</script>
