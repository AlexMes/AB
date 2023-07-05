<template>
  <modal
    name="leads-copy-to-offer-modal"
    height="auto"
    :adaptive="true"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Период регистрации</label>
        <date-picker
          v-model="copyParams.date"
          class="w-full px-1 py-2 border rounded text-gray-600"
          :config="pickerConfig"
          placeholder="Выберите даты"
          @input="errors.clear('since');errors.clear('until')"
        ></date-picker>
        <span
          v-if="errors.has('since')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('since')"
        ></span>
        <span
          v-if="errors.has('until')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('until')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Гео</label>
        <mutiselect
          v-model="copyParams.country"
          :show-labels="false"
          :options="countries"
          :max-height="100"
          placeholder="Выберите гео"
          @input="errors.clear('country')"
        ></mutiselect>
        <span
          v-if="errors.has('country')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('country')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">С офера</label>
        <mutiselect
          v-model="copyParams.offerFrom"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите офер"
          @input="errors.clear('offer_from')"
        ></mutiselect>
        <span
          v-if="errors.has('offer_from')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offer_from')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">В офер</label>
        <mutiselect
          v-model="copyParams.offerTo"
          :show-labels="false"
          :options="offers"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите офер"
          @input="errors.clear('offer_to')"
        ></mutiselect>
        <span
          v-if="errors.has('offer_to')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('offer_to')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Назначить пользователю</label>
        <mutiselect
          v-model="copyParams.userTo"
          :show-labels="false"
          :options="users"
          :max-height="100"
          track-by="id"
          label="name"
          placeholder="Выберите пользователя"
          @input="errors.clear('user_to')"
        ></mutiselect>
        <span
          v-if="errors.has('user_to')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('user_to')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1">Проставить домен</label>
        <div class="max-w-lg rounded-md shadow-sm">
          <input
            v-model="copyParams.domainTo"
            type="text"
            placeholder="example.com"
            class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
            required
            @input="errors.clear('domain_to')"
          />
        </div>
        <span
          v-if="errors.has('domain_to')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('domain_to')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <label
          for="amount"
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2 mb-1"
        >
          Количество
        </label>
        <div class="max-w-lg rounded-md shadow-sm">
          <input
            id="amount"
            v-model="copyParams.amount"
            type="number"
            placeholder="999"
            class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
            required
            @input="errors.clear('amount')"
          />
        </div>
        <span
          v-if="errors.has('amount')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('amount')"
        ></span>
      </div>

      <div class="flex flex-col w-full mb-2">
        <div class="mt-2 sm:mt-0 sm:col-span-2">
          <div class="flex w-full mt-4">
            <button
              class="mr-2 button btn-primary"
              :disabled="isBusy"
              @click="copy"
            >
              Копировать
            </button>
            <button
              class="button btn-secondary"
              @click="$modal.hide('leads-copy-to-offer-modal')"
            >
              Отмена
            </button>
          </div>
        </div>
      </div>
    </div>
  </modal>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'leads-copy-to-offer-modal',
  components: {DatePicker},
  data: () => ({
    isBusy: false,
    offers: [],
    users: [],
    domains: [],
    countries: [],
    copyParams: {
      date: null,
      country: null,
      offerFrom: null,
      offerTo: null,
      userTo: null,
      domainTo: null,
      amount: 1,
    },
    pickerConfig: {
      mode: 'range',
    },
    errors: new ErrorBag(),
  }),
  computed: {
    datePeriod() {
      if (this.copyParams.date === null || this.copyParams.date === '') {
        return null;
      }
      const dates = this.copyParams.date.split(' ');
      return {
        since: dates[0],
        until: dates[2] || dates[0],
      };
    },
  },
  created() {
    this.getOffers();
    this.getUsers();
    this.getCountries();
  },
  methods: {
    copy() {
      this.isBusy = true;
      axios
        .post('/api/leads-copy-to-offer', {
          since: this.datePeriod === null ? null : this.datePeriod.since,
          until: this.datePeriod === null ? null : this.datePeriod.until,
          country: this.copyParams.country === null ? null : this.copyParams.country,
          offer_from: this.copyParams.offerFrom === null ? null : this.copyParams.offerFrom.id,
          offer_to: this.copyParams.offerTo === null ? null : this.copyParams.offerTo.id,
          user_to: this.copyParams.userTo === null ? null : this.copyParams.userTo.id,
          domain_to: this.copyParams.domainTo,
          amount: this.copyParams.amount,
        })
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Копирование запущено',
          });
          this.$modal.hide('leads-copy-to-offer-modal');
        })
        .catch(err => {
          this.$toast.error({
            title: 'Ошибка',
            message: err.response.data.message,
          });
          this.errors.fromResponse(err);
        },
        )
        .finally(() => (this.isBusy = false));
    },

    getOffers() {
      axios
        .get('/api/offers')
        .then(response => (this.offers = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить оферы',
          }),
        );
    },

    getUsers() {
      axios
        .get('/api/users', {params: {all: true}})
        .then(response => (this.users = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось загрузить пользователей',
          }),
        );
    },

    getCountries() {
      axios.get('/api/geo/countries')
        .then(({ data }) => (this.countries = data.map(country => country.country_name)))
        .catch(error =>
          this.$toast.error({
            title: 'Could not get geo countries list.',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
