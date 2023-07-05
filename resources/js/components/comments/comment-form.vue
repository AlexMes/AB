<template>
  <form
    class="bg-white w-full my-4 rounded shadow flex flex-col p-6"
    @submit.prevent="submit"
  >
    <h3
      class="text-lg leading-6 font-medium text-gray-900 mb-3"
    >
      {{ this.isUpdate ? 'Редактировать комментарий' : 'Добавить комментарий' }}
    </h3>
    <textarea
      v-model="text"
      rows="3"
      maxlength="255"
      class="input-wrap my-3 resize-none"
      aria-label="New comment field"
      placeholder="Новый комментарий"
    >
    </textarea>
    <div class="flex justify-end">
      <button
        type="submit"
        class="button btn-primary mx-4"
        @submit.prevent="submit"
      >
        Сохранить
      </button>
      <button
        type="reset"
        class="button btn-secondary"
        @click="$emit('close')"
      >
        Отмена
      </button>
    </div>
  </form>
</template>

<script>
export default {
  name: 'comment-form',
  props:{
    resource: {
      type: String,
      required: true,
    },
    comment: {
      type: Object,
      required: false,
    },
  },
  data:()=> ({
    text:'',
  }),
  computed:{
    isUpdate() {
      return this.comment !== null && this.comment !== undefined;
    },
  },
  created() {
    if(this.isUpdate) {
      this.text = this.comment.text;
    }
  },
  methods:{
    submit(){
      if(this.isUpdate){
        axios.put(`/api/${this.resource}/comments/${this.comment.id}`, {text: this.text})
          .then(r => {
            this.$toast.success('Comment updated');
            this.$parent.$emit('recommented', r.data);
          })
          .catch(e => {
            this.$toast.error({title: 'Не удалось обновить комментарий.', message: e.response.data.message});
          });
        return;
      }
      axios.post(`/api/${this.resource}/comments`, {text: this.text})
        .then(r => {this.$toast.success('Comment added'); this.$parent.$emit('commented', r.data); this.text = '';})
        .catch(e => {
          this.$toast.error({title: 'Не удалось создать комментарий.', message: e.response.data.message});
        });
    },
  },
};
</script>
