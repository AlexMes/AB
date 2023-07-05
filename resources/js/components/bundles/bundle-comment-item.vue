<template>
  <div class="w-full bg-white p-4 border-b flex">
    <div class="w-1/6 ml-2 text-left flex items-center">
      <router-link
        :to="{name: 'users.show', params: {id:comment.user_id}}"
        class="text-gray-800"
        v-text="comment.user.name"
      ></router-link>
    </div>

    <div class="w-1/6 flex items-center">
      <span v-text="comment.created_at"></span>
    </div>

    <div class="w-7/12 flex items-center">
      <span
        v-if="!isUpdating"
        v-text="comment.text"
      ></span>
      <textarea
        v-if="isUpdating"
        v-model="text"
        rows="3"
        class="w-full bg-gray-100"
      ></textarea>
    </div>

    <div
      class="flex w-1/12 items-center justify-center p-4"
    >
      <fa-icon
        v-if="!isUpdating"
        :icon="['far','pencil-alt']"
        class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
        fixed-width
        @click.prevent="isUpdating = !isUpdating"
      ></fa-icon>
      <fa-icon
        v-if="!isUpdating"
        :icon="['far','times-circle']"
        class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
        fixed-width
        @click.prevent="remove"
      ></fa-icon>
      <fa-icon
        v-if="isUpdating"
        :icon="['far','check-circle']"
        class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
        fixed-width
        @click.prevent="update"
      ></fa-icon>
    </div>
  </div>
</template>

<script>
export default {
  name: 'bundle-comment-item',
  props: {
    comment: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      text: this.comment.text,
      isUpdating: false,
    };
  },

  methods: {
    update() {
      axios.put(`/api/bundles/${this.comment.commentable_id}/comments/${this.comment.id}`, {text: this.text})
        .then(r => {
          this.$toast.success('Комментарий обновлён.');
          this.$emit('updated', r.data);
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось обновить комментарий.', message: e.response.data.message});
        })
        .finally(() => {
          this.isUpdating = false;
        });
    },
    remove(){
      axios.delete(`/api/bundles/${this.comment.commentable_id}/comments/${this.comment.id}`)
        .then(r => {
          this.$toast.success({title: 'Комментарий удален.'});
          this.$emit('removed', this.comment);
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось удалить комментарий.', message: e.response.data.message});
        });
    },
  },
};
</script>

<style scoped>

</style>
