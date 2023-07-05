<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="result.date"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый результат
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
            for="date"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <flat-pickr
                id="date"
                v-model="result.date"
                class="w-full px-1 py-2 border rounded text-gray-600"
                :config="pickerConfig"
                placeholder="Выберите даты"
                @input="errors.clear('date')"
              ></flat-pickr>
            </div>
            <span
              v-if="errors.has('date')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('date')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="offer_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Оффер
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="offer_id"
                v-model="result.offer"
                :options="offers"
                :show-labels="false"
                :allow-empty="true"
                track-by="id"
                label="name"
                @select="errors.clear('offer_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('offer_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('offer_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="office_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="office_id"
                v-model="result.office"
                :options="offices"
                :show-labels="false"
                :allow-empty="true"
                track-by="id"
                label="name"
                @select="errors.clear('office_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('office_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('office_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="leads_count"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Всего лидов
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="leads_count"
                v-model="result.leads_count"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('leads_count')"
              />
            </div>
            <span
              v-if="errors.has('leads_count')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('leads_count')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="no_answer_count"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Без ответа
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="no_answer_count"
                v-model="result.no_answer_count"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('no_answer_count')"
              />
            </div>
            <span
              v-if="errors.has('no_answer_count')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('no_answer_count')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="reject_count"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Отказ
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="reject_count"
                v-model="result.reject_count"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('reject_count')"
              />
            </div>
            <span
              v-if="errors.has('reject_count')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('reject_count')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="wrong_answer_count"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Неправильный номер
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="wrong_answer_count"
                v-model="result.wrong_answer_count"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('wrong_answer_count')"
              />
            </div>
            <span
              v-if="errors.has('wrong_answer_count')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('wrong_answer_count')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="demo_count"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Демо
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="demo_count"
                v-model="result.demo_count"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('demo_count')"
              />
            </div>
            <span
              v-if="errors.has('demo_count')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('demo_count')"
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
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'results-form',
  components: {
    flatPickr,
  },
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => ({
    isBusy:false,
    result: {
      date: null,
      office: null,
      offer: null,
      leads_count: null,
      no_answer_count: null,
      reject_count: null,
      wrong_answer_count: null,
      demo_count: null,
    },
    offers: [],
    offices: [],
    errors: new ErrorBag(),
    pickerConfig: {
      enableTime: false,
      dateFormat: 'Y-m-d',
    },
  }),
  computed: {
    isUpdating() {
      return this.$props.id !== null;
    },
    cleanForm() {
      return {
        date: this.result.date,
        office_id: this.result.office === null ? null : this.result.office.id,
        offer_id: this.result.offer === null ? null : this.result.offer.id,
        leads_count: this.result.leads_count,
        no_answer_count: this.result.no_answer_count,
        reject_count: this.result.reject_count,
        wrong_answer_count: this.result.wrong_answer_count,
        demo_count: this.result.demo_count,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      this.getOffers();
      this.getOffices();
      if (this.isUpdating) {
        this.load();
      }
    },
    load() {
      axios
        .get(`/api/results/${this.id}`)
        .then(r => {
          this.result = r.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить результат',
            body: err.response.message,
          });
        });
    },
    save() {
      this.isBusy = true;
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'results.show', params:{id: this.id}})
        : this.$router.push({name:'results.index'});
    },
    create() {
      axios
        .post('/api/results/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'results.index',
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось сохранить результат',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
    update() {
      axios
        .put(`/api/results/${this.result.id}`, this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'results.show',
            params: { id: r.data.id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось обновить результат',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
    getOffers() {
      axios
        .get('/api/offers')
        .then(response => (this.offers = response.data))
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить оферы',
            message: err.response.data.message,
          });
        });
    },
    getOffices() {
      axios
        .get('/api/offices')
        .then(response => (this.offices = response.data))
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось офисы оферы',
            message: err.response.data.message,
          });
        });
    },
  },
};
</script>
