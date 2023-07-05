<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="branch.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый филиал
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
                v-model="branch.name"
                type="text"
                placeholder=""
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
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Доступ к статистике
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="branch.stats_access"></toggle>
            </div>
            <span
              v-if="errors.has('stats_access')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('stats_access')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="telegram_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Telegram ID
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="telegram_id"
                v-model="branch.telegram_id"
                type="text"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="50"
                @input="errors.clear('telegram_id')"
              />
            </div>
            <span
              v-if="errors.has('telegram_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('telegram_id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="sms_service"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            SMS сервис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="sms_service"
                v-model="branch.sms_service"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
              >
                <option :value="null">
                  Не задан
                </option>
                <option
                  v-for="(driver, index) in smsDrivers"
                  :key="`sms-service-${index}`"
                  :value="driver.driver"
                  v-text="driver.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('sms_service')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('sms_service')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="sms_config"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            SMS конфиг
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="sms_config"
                v-model="branch.sms_config"
                placeholder=""
                class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
                rows="5"
                @input="errors.clear('sms_config')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('sms_config')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('sms_config')"
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
  name: 'branches-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    branch: {
      name : null,
      stats_access: false,
      telegram_id: null,
      sms_service: null,
      sms_config: null,
    },
    smsDrivers: [],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
  },
  watch:{
    'branch.sms_service'(value, old) {
      if (!this.branch.sms_config) {
        const index = this.smsDrivers.findIndex(driver => driver.driver === value);
        if (index !== -1 && !!this.smsDrivers[index].config) {
          this.branch.sms_config = JSON.stringify(this.smsDrivers[index].config, null, 2);
        } else {
          this.branch.sms_config = null;
        }
      }
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
      this.getSmsDrivers();
    },
    load() {
      axios.get(`/api/branches/${this.id}`)
        .then(r => {
          this.branch = r.data;
          this.branch.sms_config = !!this.branch.sms_config
            ? JSON.stringify(
              this.branch.sms_config,
              null,
              2,
            )
            : null;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить филиал.', message: err.response.data.message});
        });
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/branches/',this.branch)
        .then(r => {
          this.$router.push({name:'branches.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить филиал.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/branches/${this.id}`,this.branch)
        .then(r => this.$router.push({name:'branches.teams',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить филиал.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    cancel() {
      if (this.isUpdating) {
        this.$router.push({name:'branches.teams', params:{id: this.id}});
      } else {
        this.$router.push({name:'branches.index'});
      }
    },
    getSmsDrivers() {
      axios.get('/api/sms/drivers')
        .then(r => this.smsDrivers = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить SMS сервисы.', message: err.response.data.message});
        });
    },
  },
};
</script>
