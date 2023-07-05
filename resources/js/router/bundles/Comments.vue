<template>
  <div>
    <div v-if="hasComments">
      <comment-item
        v-for="comment in comments"
        :key="comment.id"
        :comment="comment"
        class="w-full bg-white p-4 border-b flex"
        @updated="update"
        @removed="remove"
      >
      </comment-item>

      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div v-else>
      Комментариев не найдено
    </div>
    <div class="mt-5 w-1/2">
      <div class="my-2">
        <a
          href="#"
          @click.prevent="showForm = !showForm"
        >Оставить комментарий</a>
      </div>

      <div v-if="showForm">
        <div>
          <textarea
            v-model="text"
            class="w-full"
            rows="5"
            @input="errors.clear('text')"
          ></textarea>
          <span
            v-if="errors.has('text')"
            class="text-red-600 text-sm mt-1"
            v-text="errors.get('text')"
          ></span>
        </div>
        <div class="mt-3">
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="create"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="showForm = false"
          >
            Отмена
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {set} from 'vue';
import CommentItem from '../../components/bundles/bundle-comment-item';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'bundle-comments',
  components: {
    CommentItem,
  },
  props: {
    id: {
      type: [String,Number],
      required: true,
      default: null,
    },
  },
  data() {
    return {
      response: {},
      comments: [],
      showForm: false,
      text: '',
      isBusy: false,
      errors: new ErrorBag(),
    };
  },
  computed:{
    hasComments(){
      return this.comments.length > 0;
    },
  },
  created(){
    this.boot();
  },
  methods: {
    boot(){
      this.load();
    },
    load(page = null) {
      axios.get(`/api/bundles/${this.id}/comments`, {params: {page: page}})
        .then(r => {
          this.comments = r.data.data;
          this.response = r.data;
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить комментарии.', message: e.response.data.message});
        });
    },
    update(event) {
      const index = this.comments.findIndex(comment => comment.id === event.id);
      if(index !== -1) {
        set(this.comments, index, event);
      }
    },
    remove(event) {
      const index = this.comments.findIndex(comment => comment.id === event.id);
      if (index !== -1) {
        this.comments.splice(index, 1);
      }
    },

    create() {
      this.isBusy = true;
      axios.post(`/api/bundles/${this.id}/comments`, {text: this.text})
        .then(r => {
          this.$toast.success('Комментарий добавлен.');
          this.comments.unshift(r.data);
          this.text = '';
          this.showForm = false;
        })
        .catch(e => {
          if (e.response.status === 422) {
            return this.errors.fromResponse(e);
          }
          this.$toast.error({title: 'Не удалось создать комментарий.', message: e.response.data.message});
        })
        .finally(() => {this.isBusy = false;});
    },
  },
};
</script>
