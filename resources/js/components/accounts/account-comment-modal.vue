<template>
  <modal
    name="account-comment-modal"
    height="auto"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Группа</label>
        <multiselect
          v-model="account.group"
          :options="groups"
          :show-labels="false"
          label="name"
          placeholder="Выберите группу"
          track-by="id"
          :loading="isLoading"
        ></multiselect>
        <span
          v-if="errors.has('group')"
          class="text-xs text-red-600 font-medium mt-2"
          v-text="errors.get('group')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Примечание</label>
        <textarea
          v-model="account.comment"
          type="text"
          class="bg-gray-200 rounded text-gray-700 bg-gray-200 placeholder-gray-400 py-2 px-3"
          placeholder="Comment for account"
          maxlength="255"
          @input="errors.clear('comment')"
        ></textarea>
        <span
          v-if="errors.has('comment')"
          class="text-xs text-red-600 font-medium mt-2"
          v-text="errors.get('comment')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Разрешить стоппер</label>
        <toggle v-model="account.stopper_enabled"></toggle>
        <span
          v-if="errors.has('stopper_enabled')"
          class="text-xs text-red-600 font-medium mt-2"
          v-text="errors.get('stopper_enabled')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="save"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('account-comment-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';

export default {
  name: 'account-comment-modal',
  components: {
    Multiselect,
  },
  data:() => ({
    isBusy:false,
    errors: new ErrorBag(),
    account: {},
    isLoading: false,
    groups: [],
  }),
  watch: {
    'account.group'() {
      this.account.group_id = this.account.group ? this.account.group.id : null;
    },
  },
  created() {
    this.load();
  },
  methods:{
    beforeOpen (event) {
      this.account = event.params.account;
    },
    load() {
      this.isLoading = true;
      axios.get('/api/groups', {params: {all:true}})
        .then(r => {this.groups = r.data;})
        .catch(e => {
          this.$toast.error({title: 'Ошибка', message: 'Не удалось загрузить группы'});
        })
        .finally(() => this.isLoading = false);
    },
    save(){
      this.isBusy = true;
      axios.put(`/api/accounts/${this.account.id}`, {
        comment: this.account.comment,
        group_id: this.account.group_id,
        stopper_enabled: this.account.stopper_enabled,
      })
        .then(r => {
          this.$toast.success('Comment updated');
          this.$modal.hide('account-comment-modal');
        })
        .catch(e => {
          if(e.response.status === 422) {
            return this.errors.fromResponse(e);
          }
          this.$toast.error({title: 'Не удалось обновить комментарий.', message: e.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
