<template>
  <modal
    name="change-domain-user-modal"
    height="auto"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 mb-2"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Баер</label>
        <select v-model="user_id">
          <option
            v-for="user in users"
            :key="user.id"
            :value="user.id"
            v-text="user.name"
          ></option>
        </select>
        <span
          v-if="errors.has('user_id')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('user_id')"
        ></span>
        <span
          v-if="errors.has('date')"
          class="text-red-600 text-sm mt-1"
          v-text="errors.get('date')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="changeOrder"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('change-domain-user-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'change-domain-user-modal',
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    user_id: null,
    users: [],
    domain_ids: [],
    isLoading: false,
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasUsers() {
      return this.users.length > 0;
    },
  },
  methods: {
    beforeOpen(event) {
      this.domain_ids = event.params.domains;
      if (!this.hasUsers) {
        this.load();
      }
    },
    load() {
      this.isLoading = true;
      axios.get('/api/users',{params:{all:true}})
        .then(r => this.users = r.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить баеров.', message: e.response.data.message});
        })
        .finally(() => this.isLoading = false);
    },
    changeOrder() {
      this.isBusy = true;
      axios.post('/api/domains/change-bayer-domains', {
        user_id: this.user_id,
        domain_ids: this.domain_ids,
      })
        .then(response => {
          this.$modal.hide('change-domain-user-modal');
          this.$toast.success({title: 'OK', message: 'Баер в доменах изменён.'});
          this.errors = new ErrorBag();

        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title:'Ошибка', message: 'Не удалось изменить баера в доменах.'});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

