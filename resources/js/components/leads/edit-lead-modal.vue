<template>
  <modal
    name="edit-lead-modal"
    height="auto"
    :adaptive="true"
    :styles="{ overflow: 'visible' }"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div
        v-if="errors.hasMessage()"
        class="p-3 mb-2 text-white bg-red-700 rounded"
      >
        <span v-text="errors.message"></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          for="email"
          class="flex w-full mb-2 font-semibold"
        >E-mail</label>
        <input
          id="email"
          v-model="lead.email"
          type="email"
          placeholder="bob@example.com"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-if="errors.has('email')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('email')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          for="firstname"
          class="flex w-full mb-2 font-semibold"
        >Имя</label>
        <input
          id="firstname"
          v-model="lead.firstname"
          type="text"
          placeholder="bob@example.com"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-if="errors.has('firstname')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('firstname')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          for="lastname"
          class="flex w-full mb-2 font-semibold"
        >Фамилия</label>
        <input
          id="lastname"
          v-model="lead.lastname"
          type="text"
          placeholder="bob@example.com"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-if="errors.has('lastname')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('lastname')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          for="middlename"
          class="flex w-full mb-2 font-semibold"
        >Отчество</label>
        <input
          id="middlename"
          v-model="lead.middlename"
          type="text"
          placeholder="bob@example.com"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-if="errors.has('middlename')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('middlename')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Офер</label>
        <mutiselect
          v-model="lead.offer"
          :show-labels="false"
          :allow-empty="false"
          :options="offers"
          :loading="loading.offers"
          track-by="id"
          label="name"
          placeholder="Выберите офера"
          @input="errors.clear('offer_id')"
        ></mutiselect>
        <span
          v-if="errors.has('offer_id')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('offer_id')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label
          for="ip"
          class="flex w-full mb-2 font-semibold"
        >IP</label>
        <input
          id="ip"
          v-model="lead.ip"
          type="text"
          placeholder="bob@example.com"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-if="errors.has('ip')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('ip')"
        ></span>
      </div>
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Valid</label>
        <toggle v-model="lead.valid"></toggle>
        <span
          v-if="errors.has('valid')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('valid')"
        ></span>
      </div>
      <div
        v-if="(isSupport && $root.user.branch_id === 16) || isAdmin"
        class="flex flex-col w-full mb-4"
      >
        <label class="flex w-full mb-2 font-semibold">Разрешить повторное стягивание депозита</label>
        <toggle v-model="lead.recreate_deposit"></toggle>
        <span
          v-if="errors.has('recreate_deposit')"
          class="mt-1 text-sm text-red-600"
          v-text="errors.get('recreate_deposit')"
        ></span>
      </div>
      <div class="flex w-full">
        <button
          class="mr-2 button btn-primary"
          :disabled="isBusy"
          @click="update"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('edit-lead-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'edit-lead-modal',
  data: () => ({
    lead: {
      email: null,
      firstname: null,
      lastname: null,
      middlename: null,
      offer: null,
      ip: null,
      valid: false,
      recreate_deposit: null,
    },
    offers: [],
    loading: {
      offers: false,
    },
    isBusy: false,
    errors: new ErrorBag(),
  }),
  computed: {
    hasOffers() {
      return this.offers.length > 0;
    },
    isAdmin() {
      return this.$root.user.role === 'admin';
    },
    isSupport() {
      return this.$root.user.role === 'support';
    },
  },
  methods: {
    beforeOpen(event) {
      this.lead.id = event.params.lead.id;
      this.lead.email = event.params.lead.email;
      this.lead.firstname = event.params.lead.firstname;
      this.lead.lastname = event.params.lead.lastname;
      this.lead.middlename = event.params.lead.middlename;
      this.lead.offer = event.params.lead.offer;
      this.lead.ip = event.params.lead.ip;
      this.lead.valid = event.params.lead.valid;
      this.lead.recreate_deposit = event.params.lead.recreate_deposit;

      this.errors.clear();

      if (!this.hasOffers) {
        this.load();
      }
    },
    load() {
      this.loading.offers = true;
      axios
        .get('/api/offers')
        .then(response => {
          this.offers = response.data;
        })
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить оферы.',
            message: e.data.message,
          }),
        )
        .finally(() => (this.loading.offers = false));
    },
    update() {
      this.isBusy = true;
      axios
        .put(`/api/leads/${this.lead.id}`, {
          email: this.lead.email,
          firstname: this.lead.firstname,
          lastname: this.lead.lastname,
          middlename: this.lead.middlename,
          offer_id: !!this.lead.offer ? this.lead.offer.id : null,
          ip: this.lead.ip,
          valid: this.lead.valid,
          recreate_deposit: this.lead.recreate_deposit,
        })
        .then(response => {
          this.$modal.hide('edit-lead-modal');
          this.$toast.success({
            title: 'OK',
            message: 'Лид обновлён.',
          });
          this.errors = new ErrorBag();
          this.$eventHub.$emit('lead-updated', {
            lead: response.data,
          });
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({
            title: 'Ошибка',
            message: 'Не удалось обновить лид.',
          });
        })
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
