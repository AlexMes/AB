<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="app.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новое фб приложение
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="name"
                v-model="app.name"
                type="text"
                placeholder="MyNewAwesomeApp"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="40"
                minlength="3"
                @input="errors.clear('name')"
              />
            </div>
            <span
              v-if="errors.has('name')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('name')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            ID
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="id"
                v-model="app.id"
                type="text"
                placeholder="8888888888888"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                minlength="3"
                @input="errors.clear('id')"
              />
            </div>
            <span
              v-if="errors.has('id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="secret"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Secret
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="secret"
                v-model="app.secret"
                type="text"
                placeholder="8888888888888"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                minlength="3"
                @input="errors.clear('secret')"
              />
            </div>
            <span
              v-if="errors.has('secret')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('secret')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="token"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Default token
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="token"
                v-model="app.default_token"
                type="text"
                placeholder="8888888888888"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                minlength="3"
                @input="errors.clear('default_token')"
              />
            </div>
            <span
              v-if="errors.has('default_token')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('default_token')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="domain"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Domain
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="domain"
                v-model="app.domain"
                type="text"
                placeholder="my-awesome-domain.test"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
                minlength="5"
                @input="errors.clear('domain')"
              />
            </div>
            <span
              v-if="errors.has('domain')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('domain')"
            ></span>
          </div>
        </div>

        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="save"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';

export default {
  name: 'facebook-app-form',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    app: {
      name: null,
      id: null,
      secret: null,
      default_token: null,
      domain: null,
    },
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.$props.id !== null;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(vm => vm.boot());
  },
  methods:{
    boot(){
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/facebook/apps/${this.id}`)
        .then(r =>  this.app = r.data)
        .catch(err => this.$toast.error({title: 'App loading failed.', message: err.response.data.message}));
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.$router.push({name:'facebook.apps.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/facebook/apps',  this.app)
        .then(() => {
          this.$toast.success('Application created');
          this.$router.push({name:'facebook.apps.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Error.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/facebook/apps/${this.app.id}`, this.app)
        .then(r => {
          this.$toast.success('Updated');
          this.$router.push({name:'facebook.apps.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Error.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
