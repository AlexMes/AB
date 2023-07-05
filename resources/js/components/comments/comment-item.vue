<template>
  <div>
    <div
      v-if="!updateComment"
      class="bg-white w-full my-4 rounded shadow flex flex-col"
    >
      <div class="flex flex-row px-5 py-4 items-center justify-between align-middle">
        <div
          v-if="hasUser"
          class="flex font-medium items-center"
        >
          <router-link
            :to="{name: 'users.show', params: {id:comment.user_id}}"
            class="text-gray-800"
            v-text="comment.user.name"
          ></router-link>
        </div>
        <div
          v-else
          class="flex font-medium items-center"
        >
          <router-link
            :to="{name: comment.commentable_type + '.show', params: {id:comment.commentable.id}}"
            class="text-gray-800"
            v-text="comment.commentable.name ? comment.commentable.name :comment.commentable.phone"
          ></router-link>
        </div>
        <div class="flex flex-col items-end text-gray-800">
          <span
            class="text-sm text-gray-650 italic"
            v-text="comment.created_at"
          ></span>
          <span
            class="cursor-pointer stroke-current text-xs mt-2"
            @click.prevent="updateComment = !updateComment"
          >Изменить</span>
          <span
            class="cursor-pointer text-gray-400 stroke-current text-xs mt-2"
            @click.prevent="deleteComment"
          >Удалить</span>
        </div>
      </div>
      <div class="px-5 pb-4 text-gray-700 flex flex-col text-sm">
        <span v-text="comment.text"></span>
      </div>
    </div>
    <div v-else>
      <comment-form
        class="mt-2"
        :resource="resource"
        :comment="comment"
        @close="updateComment = false"
      ></comment-form>
    </div>
  </div>
</template>

<script>
export {format, parse} from 'date-fns';
export default {
  name:'comment-item',
  props:{
    resource: {
      type: String,
      required: true,
    },
    comment:{
      type:Object,
      required:true,
    },
  },
  data: () => ({
    updateComment: false,
  }),
  computed:{
    hasUser(){
      return this.comment.user !== undefined;
    },
  },
  created() {
    this.$on('recommented', e => {
      this.updateComment = !this.updateComment;
    });
  },
  methods: {
    deleteComment(){
      axios.delete(`/api/${this.resource}/comments/${this.comment.id}`)
        .then(r => {
          this.$toast.success({title: 'Комментарий удален.'});
          this.$parent.$emit('comment_delete', this.comment);
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось удалить комментарий.', message: e.response.data.message});
        });
    },
  },
};
</script>
