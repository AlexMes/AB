<template>
  <div class="w-full">
    <div
      v-if="hasComments"
    >
      <comment-item
        v-for="comment in comments"
        :key="comment.id"
        :comment="comment"
        :resource="`domains/${id}`"
        @recommented="updateComment"
      >
      </comment-item>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Ещё нет комментариев</p>
    </div>

    <div>
      <div
        v-if="!showCommentForm"
        class="flex items-center justify-center mt-3"
      >
        <button
          class="button btn-primary"
          @click="showCommentForm = !showCommentForm"
        >
          Добавить комментарий
        </button>
      </div>
      <comment-form
        v-else
        class="mt-6"
        :resource="`domains/${id}`"
        @close="showCommentForm = false"
      ></comment-form>
    </div>
  </div>
</template>

<script>

import { set } from 'vue';
import CommentItem from '../../components/comments/comment-item';
import CommentForm from '../../components/comments/comment-form';
export default {
  name: 'domain-comments',
  components: { CommentItem, CommentForm },
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    comments: [],
    response: null,
    showCommentForm: false,
  }),
  computed:{
    hasComments() {
      return this.comments.length > 0;
    },
  },
  created() {
    this.load();
    this.listen();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/domains/${this.id}/comments`, {params: {page, paginate: true}})
        .then(response => {
          this.comments = response.data.data;
          this.response = response.data;
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: err.response.data.message,
          }),
        );
    },
    listen() {
      this.$on('commented', e => {
        this.comments.unshift(e);
      });
      this.$on('comment_delete', e => {
        const index = this.comments.findIndex(
          comment => comment.id == e.id,
        );
        if (index !== -1) {
          this.comments.splice(index, 1);
        }
      });
    },
    updateComment(event) {
      const index = this.comments.findIndex(
        comment => comment.id == event.id,
      );
      if (index !== -1) {
        set(this.comments, index, event);
      }
    },
  },
};
</script>
