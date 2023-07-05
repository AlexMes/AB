<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="batch.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый пакет
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span
          v-if="errors.has('modification_forbidden')"
          v-text="errors.get('modification_forbidden')"
        ></span>
        <span
          v-else
          v-text="errors.message"
        ></span>
      </div>
      <form @submit="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="batch_name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="batch_name"
                v-model="batch.name"
                type="text"
                placeholder="Title for a batch"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
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
            for="batch_registered_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата регистрации
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="batch_registered_at"
                v-model="batch.registered_at"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                :config="pickerConfig"
                placeholder="Выберите дату"
                @input="errors.clear('registered_at')"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('registered_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('registered_at')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="batch_offices"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Получающие офисы
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="batch_offices"
                v-model="batch.offices"
                :options="offices"
                :multiple="true"
                :allow-empty="true"
                :close-on-select="false"
                :clear-on-select="false"
                :show-labels="false"
                placeholder="Выберите офисы"
                track-by="id"
                label="name"
                class="block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('offices')"
              >
              </mutiselect>
            </div>
            <span
              v-if="errors.has('offices')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('offices')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="substitute_offer"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Оффер для подмены
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="substitute_offer"
                v-model="batch.substitute_offer"
                :options="offers"
                :multiple="false"
                :allow-empty="true"
                :close-on-select="true"
                :clear-on-select="false"
                :show-labels="false"
                placeholder="Выберите оффер"
                track-by="id"
                label="name"
                class="block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('offers')"
              >
              </mutiselect>
            </div>
            <span
              v-if="errors.has('offers')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('offers')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Создавать копию офера _R
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle
                v-model="batch.create_offer"
                @input="errors.clear('create_offer')"
              ></toggle>
            </div>
            <span
              v-if="errors.has('create_offer')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('create_offer')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Симулировать автологин
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle
                v-model="batch.simulate_autologin"
                @input="errors.clear('simulate_autologin')"
              ></toggle>
            </div>
            <span
              v-if="errors.has('simulate_autologin')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('simulate_autologin')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Игнорировать роуты в паузе
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle
                v-model="batch.ignore_paused_routes"
                @input="errors.clear('ignore_paused_routes')"
              ></toggle>
            </div>
            <span
              v-if="errors.has('ignore_paused_routes')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('ignore_paused_routes')"
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
import ErrorBag from '../../utilities/ErrorBag';
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: 'resell-batches-form',
  components: {
    DatePicker,
  },
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    batch: {
      name: null,
      registered_at: null,
      offices: [],
      substitute_offer: null,
      create_offer: false,
      simulate_autologin: false,
      ignore_paused_routes: false,
    },
    offices: [],
    offers: [],
    errors: new ErrorBag(),
    pickerConfig:{
      maxDate: moment().format('YYYY-MM-DD'),
    },
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
    cleanForm() {
      return {
        name: this.batch.name,
        registered_at: this.batch.registered_at,
        offices: this.batch.offices.map(office => office.id),
        substitute_offer: this.batch.substitute_offer?.id ?? null,
        create_offer: this.batch.create_offer,
        simulate_autologin: this.batch.simulate_autologin,
        ignore_paused_routes: this.batch.ignore_paused_routes,
        filters: this.batch.filters,
      };
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      if(this.isUpdating){
        this.load();
      } else {
        if (!!this.$route.params.batch) {
          this.batch = this.$route.params.batch;
        }
      }

      this.loadOffices();
      this.loadOffers();
    },
    load(){
      axios.get(`/api/resell-batches/${this.id}`)
        .then(r => this.batch = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить пакет', message: err.response.data.message});
        });
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/resell-batches/', this.cleanForm)
        .then(r => this.$router.push({name:'resell-batches.show', params: {id: r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить пакет.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/resell-batches/${this.batch.id}`,this.cleanForm)
        .then(r => this.$router.push({name:'resell-batches.show',params: {id: r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить пакет', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'resell-batches.show', params: {id: this.id}})
        : this.$router.push({name:'resell-batches.index'});
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(response => {
          this.offices = response.data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load offices.', message: err.response.data.message}));
    },
    loadOffers() {
      axios.get('/api/offers')
        .then(response => {
          this.offers = response.data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load offers.', message: err.response.data.message}));
    },
  },
};
</script>
