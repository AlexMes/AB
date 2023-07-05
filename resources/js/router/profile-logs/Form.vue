<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="title"
      ></h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit.prevent="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="profile"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Профиль
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="profile"
                v-model="profileLog.profile"
                :show-labels="false"
                :options="profiles"
                placeholder="Поиск профилей"
                track-by="id"
                label="name"
                :loading="isLoadingProfiles"
                @search-change="loadProfiles"
                @select="errors.clear('profile_id')"
              ></mutiselect>
            </div>
            <div
              v-if="errors.has('profile_id')"
              class="text-red-600 text-sm mt-2"
              v-text="errors.get('profile_id')"
            ></div>
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
                v-model="profileLog.link"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('link')"
              />
            </div>
            <div
              v-if="errors.has('link')"
              class="text-red-600 text-sm mt-2"
              v-text="errors.get('link')"
            ></div>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="duration"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Длительность запуска
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="duration"
                v-model="profileLog.duration"
                type="text"
                placeholder="123"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              />
            </div>
            <div
              v-if="errors.has('duration')"
              class="text-red-600 text-sm mt-2"
              v-text="errors.get('duration')"
            ></div>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="miniature"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Миниатюра
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="miniature"
                v-model="profileLog.miniature"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              />
            </div>
            <div
              v-if="errors.has('miniature')"
              class="text-red-600 text-sm mt-2"
              v-text="errors.get('miniature')"
            ></div>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="creative"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Креатив
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="creative"
                v-model="profileLog.creative"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              />
            </div>
            <div
              v-if="errors.has('creative')"
              class="text-red-600 text-sm mt-2"
              v-text="errors.get('creative')"
            ></div>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="text"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Текст
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="text"
                v-model="profileLog.text"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                rows="7"
                placeholder="Текст"
                @input="errors.clear('text')"
              ></textarea>
            </div>
            <div
              v-if="errors.has('text')"
              class="text-red-600 text-sm mt-2"
              v-text="errors.get('text')"
            ></div>
          </div>
        </div>
        <div class="w-full flex justify-end mt-5">
          <button
            type="reset"
            class="button btn-secondary mx-2"
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
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'profile-logs-form',
  props: {
    id: {
      type: [String,Number],
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    profileLog: {
      link: '',
      duration: 0,
      miniature: '',
      creative: '',
      text: '',
      profile: null,
    },
    profiles: [],
    isLoadingProfiles: false,
    errors: new ErrorBag(),
  }),
  computed:{
    title() {
      return this.profileLog.profile !== null ? this.profileLog.profile.name : '';
    },
    isUpdating(){
      return this.id !== null;
    },
    cleanProfileLog(){
      return {
        link: this.profileLog.link,
        duration: this.profileLog.duration,
        miniature: this.profileLog.miniature,
        creative: this.profileLog.creative,
        text: this.profileLog.text,
        profile_id: this.profileLog.profile === null ? null : this.profileLog.profile.id,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      if (this.isUpdating) {
        this.load();
      }
    },
    load() {
      axios.get(`/api/profile-logs/${this.id}`)
        .then(r => this.profileLog = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить запись', message: err.response.data.message});
        });
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/profile-logs/', this.cleanProfileLog)
        .then(r => {
          this.$router.push({name:'profile-logs.show', params:{id:r.data.id}});
        })
        .catch(err => {
          if(err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось создать запись', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/profile-logs/${this.profileLog.id}`, this.cleanProfileLog)
        .then(r => this.$router.push({name:'profile-logs.show', params:{id:r.data.id}}))
        .catch(err => {
          if(err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить запись', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    loadProfiles(search = '') {
      if (typeof search !== 'string' || search.length < 2) {
        return;
      }
      this.isLoadingProfiles = true;
      axios.get('/api/profiles', {params:{all: true, search: search}})
        .then(response => this.profiles = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить профили.', message: error.response.data.message});
        })
        .finally(() => this.isLoadingProfiles = false);
    },
  },
};
</script>

<style scoped>

</style>
