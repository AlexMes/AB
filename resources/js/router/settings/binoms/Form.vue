<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="binom.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Добавить Binom
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
                v-model="binom.name"
                type="text"
                placeholder="Название"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
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
            for="url"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            URL
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="url"
                v-model="binom.url"
                type="text"
                placeholder="URL"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                minlength="3"
                @input="errors.clear('url')"
              />
            </div>
            <span
              v-if="errors.has('url')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('url')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="access_token"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Access token
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="access_token"
                v-model="binom.access_token"
                type="text"
                placeholder="API KEY"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('access_token')"
              />
            </div>
            <span
              v-if="errors.has('access_token')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('access_token')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Статус
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="binom.enabled"></toggle>
            </div>
            <span
              v-if="errors.has('enabled')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('enabled')"
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
  name: 'binoms-form',
  props: {
    id: {
      type: [String, Number],
      required :false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    binom: {
      name: null,
      url: null,
      access_token: null,
      enabled: true,
    },
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.$props.id !== null;
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/binoms/${this.id}`)
        .then(r => this.binom = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить.', message: err.response.data.message}));
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'binoms.show', params:{id: this.id}})
        : this.$router.push({name:'binoms.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/binoms/',this.binom)
        .then(r => {
          this.$router.push({name:'binoms.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if (err.response.status) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/binoms/${this.binom.id}`,this.binom)
        .then(r => this.$router.push({name:'binoms.show',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
