<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Редактирование оплаты
      </h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новая оплата
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
            for="office_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Офис
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <select
                id="office_id"
                v-model="payment.office_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('office_id')"
              >
                <option
                  v-for="office in offices"
                  :key="office.id"
                  :value="office.id"
                  v-text="office.name"
                ></option>
              </select>
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
            for="paid"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Оплачено лидов
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="paid"
                v-model="payment.paid"
                type="number"
                min="1"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="255"
                @input="errors.clear('paid')"
              />
            </div>
            <span
              v-if="errors.has('paid')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('paid')"
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
  name: 'office-payments-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    payment: {
      office_id: null,
      paid: null,
    },
    offices: [],
    errors: new ErrorBag(),
  }),
  computed: {
    isUpdating() {
      return this.id !== null && this.id !== undefined;
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      if (this.isUpdating) {
        this.load();
      } else {
        this.payment.office_id = this.$route.params.officeId || null;
      }
      this.loadOffices();
    },
    load() {
      axios.get(`/api/office-payments/${this.id}`)
        .then(r => this.payment = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить оплату.', message: err.response.data.message}));
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(r => this.offices = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить список офисов.', message: err.resoinse.data.message}));
    },
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    create() {
      this.isBusy = true;
      axios.post('/api/office-payments/', this.payment)
        .then(r => this.$router.back())
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить оплату.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update() {
      this.isBusy = true;
      axios.put(`/api/office-payments/${this.id}`, this.payment)
        .then(r => this.$router.back())
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить оплату.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    cancel() {
      this.$router.back();
    },
  },
};
</script>
