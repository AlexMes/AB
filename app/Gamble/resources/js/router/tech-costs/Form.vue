<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          <span v-if="isEditing">Редактирование записи</span>
          <span v-else>Создание записи</span>
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div
          v-if="errors.hasMessage()"
          class="rounded-md bg-red-100 mt-6 p-4 max-w-7xl mx-auto"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-red-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p
                class="text-sm leading-5 font-medium text-red-800"
                v-text="errors.message"
              >
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
          <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="`[${techCost.id}] ${techCost.date}`"
            >
            </h3>
          </div>
          <form class="px-6">
            <div
              class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5"
            >
              <label
                for="date"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Дата
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="date"
                    v-model="techCost.date"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="date"
                    @input="errors.clear('date')"
                  />
                </div>
                <p
                  v-if="errors.has('date')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('date')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="user_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Пользователь
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="user_id"
                    v-model="techCost.user"
                    :show-labels="false"
                    :options="users"
                    placeholder="Выберите аккаунт"
                    track-by="id"
                    label="name"
                    @input="errors.clear('user_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('user_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('user_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="spend"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Кост
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm sm:leading-5">
                        $
                      </span>
                    </div>
                    <input
                      id="spend"
                      v-model="techCost.spend"
                      type="number"
                      name="spend"
                      step="0.01"
                      class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                      placeholder="0"
                      aria-describedby="price-currency"
                      @input="errors.clear('spend')"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <span
                        id="price-currency"
                        class="text-gray-500 sm:text-sm sm:leading-5"
                      >
                        USD
                      </span>
                    </div>
                  </div>
                </div>
                <p
                  v-if="errors.has('spend')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('spend')"
                ></p>
              </div>
            </div>
            <div class="mt-6 sm:mt-5 border-t border-gray-200 py-5">
              <div class="flex justify-end">
                <span class="inline-flex rounded-md shadow-sm">
                  <a
                    class="inline-flex cursor-pointer items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                    @click="cancel"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Отмена
                  </a>
                </span>
                <span class="ml-3 inline-flex rounded-md shadow-sm">
                  <button
                    type="submit"
                    class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
                    :disabled="isBusy"
                    @click.prevent="save"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Сохранить
                  </button>
                </span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import ErrorBag from '../../../../../../resources/js/utilities/ErrorBag';

export default {
  name: 'tech-costs-form',
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => {
    return {
      isBusy: false,
      techCost: {
        date: null,
        user: null,
        spend: null,
      },
      users: [],
      errors: new ErrorBag(),
    };
  },
  computed: {
    isEditing() {
      return this.id !== null;
    },
    cleanForm() {
      return {
        date: this.techCost.date,
        user_id: this.techCost.user !== null ? this.techCost.user.id : null,
        spend: this.techCost.spend,
      };
    },
  },
  created() {
    if (this.isEditing) {
      this.load();
    }
    this.loadUsers();
  },
  methods: {
    load() {
      axios.get(`/api/tech-costs/${this.id}`)
        .then(({data}) => this.techCost = data)
        .catch(err => this.$toast.error({title: 'Unable to load the record.', message: err.response.data.message}));
    },
    loadUsers() {
      axios.get('/api/users')
        .then(({data}) => {
          this.users = data;
          if (!this.isEditing) {
            const user = this.users.find(user => user.id === this.$root.user.id);
            if (user !== undefined) {
              this.techCost.user = user;
            }
          }
        })
        .catch(err => this.$toast.error({title: 'Unable to load users.', message: err.response.data.message}));
    },
    save() {
      this.isBusy = true;
      this.isEditing ? this.update() : this.create();
    },
    cancel() {
      this.isEditing
        ? this.$router.push({name:'tech-costs.show', params:{id: this.id}})
        : this.$router.push({name:'tech-costs.index'});
    },
    create() {
      axios
        .post('/api/tech-costs/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'tech-costs.index',
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Unable to create a record.', message: err.response.data.message});
        }).finally(()=> this.isBusy = false);
    },
    update() {
      axios
        .put(`/api/tech-costs/${this.id}`, this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'tech-costs.show',
            params: { id: r.data.id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Unable to update the record.',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
  },
};
</script>

<style scoped>

</style>
