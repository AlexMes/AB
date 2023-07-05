<template>
  <div class="flex flex-col">
    <div class="container flex flex-col mx-auto">
      <div
        class="flex flex-col items-start justify-between w-full mb-6 md:flex-row md:items-center"
      >
        <h1
          class="flex text-gray-700"
          v-text="`Лиды (${leads.length})`"
        ></h1>
        <a
          href="#"
          class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
          @click="toggleEditing"
        >
          <fa-icon
            :icon="['far', 'pencil-alt']"
            class="w-5 h-5 mr-2 -ml-1 text-gray-400"
            fixed-width
          ></fa-icon>
          <span v-if="!isEditing">Изменить</span>
          <span v-else>Отмена</span>
        </a>
      </div>

      <div
        v-if="isEditing"
        class="flex flex-col mb-6"
      >
        <div
          class="flex flex-wrap items-start md:flex-no-wrap md:items-center"
        >
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Дата регистрации</label>
            <date-picker
              id="filters_registered_at"
              v-model="filters.registered_at"
              class="w-full px-1 py-2 text-gray-600 border rounded"
              :config="pickerConfig"
              placeholder="Выберите даты"
              @input="errors.clear('registered_at')"
            ></date-picker>
            <span
              v-if="errors.has('registered_at')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('registered_at')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Дата выдачи</label>
            <date-picker
              id="filters_created_at"
              v-model="filters.created_at"
              class="w-full px-1 py-2 text-gray-600 border rounded"
              :config="pickerConfig"
              placeholder="Выберите даты"
              @input="errors.clear('created_at')"
            ></date-picker>
            <span
              v-if="errors.has('created_at')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('created_at')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Офисы</label>
            <mutiselect
              v-model="filters.office"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.offices"
              placeholder="Выберите офисы"
              track-by="id"
              label="name"
              @change="errors.clear('office')"
            ></mutiselect>
            <span
              v-if="errors.has('office')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('office')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Кроме офисов</label>
            <mutiselect
              v-model="filters.except_office"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.offices"
              placeholder="Выберите офисы"
              track-by="id"
              label="name"
              @change="errors.clear('except_office')"
            ></mutiselect>
            <span
              v-if="errors.has('except_office')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('except_office')"
            ></span>
          </div>
        </div>

        <div
          class="flex flex-wrap items-start md:flex-no-wrap md:items-center md:mt-2"
        >
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Офферы</label>
            <mutiselect
              v-model="filters.offer"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.offers"
              placeholder="Выберите офферы"
              track-by="id"
              label="name"
              @change="errors.clear('offer')"
            ></mutiselect>
            <span
              v-if="errors.has('offer')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('offer')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Статусы</label>
            <mutiselect
              v-model="filters.status"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.statuses"
              placeholder="Выберите статусы"
              @change="errors.clear('status')"
            ></mutiselect>
            <span
              v-if="errors.has('status')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('status')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Кроме статусов</label>
            <mutiselect
              v-model="filters.except_status"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.statuses"
              placeholder="Выберите статусы"
              @change="errors.clear('except_status')"
            ></mutiselect>
            <span
              v-if="errors.has('except_status')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('except_status')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Возраст</label>
            <mutiselect
              v-model="filters.age"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.ages"
              placeholder="Выберите возраст"
              @change="errors.clear('age')"
            ></mutiselect>
            <span
              v-if="errors.has('age')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('age')"
            ></span>
          </div>
          <div
            class="flex flex-col w-full mx-2 my-2 align-middle md:my-0"
          >
            <label class="mb-2">Гео</label>
            <mutiselect
              v-model="filters.countries"
              :show-labels="false"
              :multiple="true"
              :options="filterSets.countries"
              placeholder="Выберите гео"
              track-by="country"
              label="country_name"
              @change="errors.clear('countries')"
            ></mutiselect>
            <span
              v-if="errors.has('countries')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('countries')"
            ></span>
          </div>
        </div>

        <div
          v-if="batch.status === 'pending'"
          class="flex flex-wrap items-start justify-between md:flex-no-wrap md:items-center md:mt-2"
        >
          <div class="flex flex-col mx-2 my-2 align-middle md:my-0">
            <label class="mb-2">Количество лидов</label>
            <div class="relative rounded-md shadow-sm">
              <input
                v-model="limit"
                type="number"
                class="block w-full pl-4 pr-2 form-input sm:text-sm sm:leading-5"
                placeholder="0"
              />
            </div>
          </div>
          <div class="flex">
            <div
              class="flex flex-col mx-2 my-2 align-middle md:my-0"
            >
              <button
                type="button"
                class="mt-4 ml-3 button btn-primary"
                :disabled="isBusy"
                @click="getLeads"
              >
                <span>
                  <fa-icon
                    v-if="isBusy"
                    :icon="['far', 'spinner']"
                    class="mr-2 fill-current"
                    spin
                    fixed-width
                  ></fa-icon>
                  Загрузить
                </span>
              </button>
            </div>
            <div
              class="flex flex-col mx-2 my-2 align-middle md:my-0"
            >
              <button
                type="button"
                class="mt-4 ml-3 button btn-primary"
                :disabled="isBusy"
                @click="syncLeads"
              >
                <span>
                  <fa-icon
                    v-if="isBusy"
                    :icon="['far', 'spinner']"
                    class="mr-2 fill-current"
                    spin
                    fixed-width
                  ></fa-icon>
                  Сохранить
                </span>
              </button>
            </div>
          </div>
        </div>
        <div
          v-if="errors.has('leads')"
          class="p-3 my-4 text-white bg-red-700 rounded"
        >
          <span v-text="errors.get('leads')"></span>
        </div>
      </div>
    </div>

    <div v-if="hasLeads">
      <div class="overflow-x-auto overflow-y-hidden flex w-full bg-white shadow no-last-border">
        <table class="relative table w-full table-auto">
          <thead
            class="sticky w-full font-semibold text-gray-700 uppercase bg-gray-300"
          >
            <tr class="px-3">
              <th v-if="isEditing">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @click="toggleAllSelected"
                />
              </th>
              <th>#</th>
              <th>Имя</th>
              <th>Номер</th>
              <th>Гео</th>
              <th>Статус</th>
              <th>#Назначения</th>
              <th v-if="batch.status !== 'pending' && !isEditing">
                Назначен
              </th>
              <th v-if="batch.status !== 'pending' && !isEditing">
                Факт назначен
              </th>
              <th v-if="batch.status !== 'pending' && !isEditing">
                Заказ
              </th>
              <th v-if="batch.status !== 'pending' && !isEditing">
                Дестинейшн
              </th>
              <th v-if="batch.status !== 'pending' && !isEditing">
                Успешно
              </th>
            </tr>
          </thead>
          <tbody class="w-full">
            <resell-batch-lead-list-item
              v-for="lead in leads"
              :key="lead.id"
              :lead="lead"
              :batch="batch"
              :is-editing="isEditing"
            >
            </resell-batch-lead-list-item>
          </tbody>
        </table>
      </div>
    </div>
    <div
      v-else-if="batch.status === 'pending'"
      class="flex flex-col p-4 bg-white rounded shadow"
    >
      <p>Пусто</p>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import moment from 'moment';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'resell-batches-leads',
  components: {
    DatePicker,
  },
  props: {
    batch: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      filteredLeads: [],
      isBusy: false,
      isEditing: false,
      filters: {
        registered_at: '',
        created_at: '',
        office: [],
        except_office: [],
        offer: [],
        status: [],
        except_status: [],
        age: [],
        countries: [],
      },
      filterSets: {
        offices: [],
        offers: [],
        statuses: [],
        ages: [],
        countries: [],
      },
      limit: 1000,
      pickerConfig: {
        maxDate: moment().format('YYYY-MM-DD'),
        mode: 'range',
      },
      errors: new ErrorBag(),
    };
  },
  computed: {
    leads() {
      if (this.isEditing) {
        return this.filteredLeads;
      }

      return !!this.batch.leads ? this.batch.leads : [];
    },
    hasLeads() {
      return this.leads.length > 0;
    },
    allSelected() {
      return this.filteredLeads.every(lead => lead.selected);
    },
    cleanFilters() {
      return {
        registered_at: this.registeredPeriod,
        created_at: this.createdPeriod,
        office: this.filters.office.map(office => office.id),
        except_office: this.filters.except_office.map(office => office.id),
        offer: this.filters.offer.map(offer => offer.id),
        status: this.filters.status,
        except_status: this.filters.except_status,
        age: this.filters.age,
        countries: this.filters.countries.map(
          country => country.country,
        ),
      };
    },
    registeredPeriod() {
      if (this.filters.registered_at === '') {
        return [];
      }

      const dates = this.filters.registered_at.split(' ');
      return [dates[0], dates[2] || dates[0]];
    },
    createdPeriod() {
      if (this.filters.created_at === '') {
        return null;
      }

      const dates = this.filters.created_at.split(' ');
      return [dates[0], dates[2] || dates[0]];
    },
  },
  created() {
    this.loadOffices();
    this.loadOffers();
    this.loadStatuses();
    this.loadAges();
    this.loadCountries();
  },
  methods: {
    syncLeads() {
      this.isBusy = true;
      axios
        .post(`/api/resell-batches/${this.batch.id}/leads`, {
          ...this.cleanFilters,
          leads: this.filteredLeads
            .filter(lead => lead.selected)
            .map(lead => lead.id),
        })
        .then(r => {
          this.$emit('batch-updated', { batch: r.data });
          this.$toast.success({
            title: 'OK',
            message: 'Лиды и фильтры сохранены.',
          });
          this.toggleEditing();
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось сохранить лиды.',
            message: err.response.data.message,
          });
        })
        .finally(() => (this.isBusy = false));
    },
    getLeads() {
      this.isBusy = true;
      axios
        .get('/api/resell-batch-leads', {
          params: { ...this.cleanFilters, limit: this.limit },
        })
        .then(r => {
          this.filteredLeads = r.data.map(lead => {
            return {
              id: lead.id,
              firstname: lead.firstname,
              lastname: lead.lastname,
              phone: lead.phone,
              country: lead.country,
              ip: lead.ip,
              ip_address: lead.ip_address,
              assignments: lead.assignments,
              selected:
                this.batch.leads.findIndex(
                  l => l.id === lead.id,
                ) !== -1,
            };
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось загрузить лиды.',
            message: err.response.data.message,
          });
        })
        .finally(() => (this.isBusy = false));
    },
    loadOffices() {
      axios
        .get('/api/offices')
        .then(response => {
          this.filterSets.offices = response.data;
          this.resetOfficeFilter();
        })
        .catch(err =>
          this.$toast.error({
            title: 'Unable to load offices.',
            message: err.response.data.message,
          }),
        );
    },
    loadOffers() {
      axios
        .get('/api/offers')
        .then(response => {
          this.filterSets.offers = response.data;
          this.resetOfferFilter();
        })
        .catch(err =>
          this.$toast.error({
            title: 'Unable to load offers.',
            message: err.response.data.message,
          }),
        );
    },
    loadStatuses() {
      axios
        .get('/api/statuses')
        .then(
          response =>
            (this.filterSets.statuses = response.data.map(
              status => status.name,
            )),
        )
        .catch(err =>
          this.$toast.error({
            title: 'Unable to load statuses.',
            message: err.response.data.message,
          }),
        );
    },
    loadAges() {
      axios
        .get('/api/ages')
        .then(
          response =>
            (this.filterSets.ages = response.data.map(
              age => age.name,
            )),
        )
        .catch(err =>
          this.$toast.error({
            title: 'Unable to load ages.',
            message: err.response.data.message,
          }),
        );
    },
    loadCountries() {
      axios
        .get('/api/geo/countries')
        .then(response => (this.filterSets.countries = response.data))
        .catch(err =>
          this.$toast.error({
            title: 'Unable to load countries.',
            message: err.response.data.message,
          }),
        );
    },
    toggleAllSelected() {
      const selected = !this.allSelected;
      // eslint-disable-next-line no-param-reassign
      this.filteredLeads.forEach(lead => (lead.selected = selected));
    },
    toggleEditing() {
      if (!this.isEditing) {
        this.resetFilters();
      }

      this.isEditing = !this.isEditing;
    },
    resetFilters() {
      this.filteredLeads = [];
      this.errors.clear();

      this.filters.registered_at = Array.isArray(
        this.batch.filters.registered_at,
      )
        ? this.batch.filters.registered_at.join(' — ')
        : '';
      this.filters.created_at = Array.isArray(
        this.batch.filters.created_at,
      )
        ? this.batch.filters.created_at.join(' — ')
        : '';
      this.filters.status = Array.isArray(this.batch.filters.status)
        ? this.batch.filters.status.map(e => e)
        : [];
      this.filters.except_status = Array.isArray(this.batch.filters.except_status)
        ? this.batch.filters.except_status.map(e => e)
        : [];
      this.filters.age = Array.isArray(this.batch.filters.age)
        ? this.batch.filters.age.map(e => e)
        : [];
      this.resetOfficeFilter();
      this.resetOfferFilter();
    },
    resetOfficeFilter() {
      if (this.filterSets.offices.length > 0) {
        this.filters.office = Array.isArray(this.batch.filters.office)
          ? this.batch.filters.office.map(officeId =>
            this.filterSets.offices.find(
              office => office.id === officeId,
            ),
          )
          : [];
        this.filters.except_office = Array.isArray(this.batch.filters.except_office)
          ? this.batch.filters.except_office.map(officeId =>
            this.filterSets.offices.find(
              office => office.id === officeId,
            ),
          )
          : [];
      }
    },
    resetOfferFilter() {
      if (this.filterSets.offers.length > 0) {
        this.filters.offer = Array.isArray(this.batch.filters.offer)
          ? this.batch.filters.offer.map(offerId =>
            this.filterSets.offers.find(
              offer => offer.id === offerId,
            ),
          )
          : [];
      }
    },
  },
};
</script>

<style scoped></style>
