<template>
  <modal
    name="leads-upload-modal"
    height="auto"
    scrolable
  >
    <div class="flex flex-col p-4 justify-between h-full">
      <div class="mb-4 w-full flex items-center">
        <span
          v-if="origin"
          v-cloak
        >{{ origin }}</span>
        <label
          for="file"
          class="btn-primary w-48 ml-auto mr-0 px-4 py-2 rounded flex justify-center"
        >
          Загрузить файл
          <input
            id="file"
            ref="file"
            type="file"
            accept=".xlsx, .xls, .csv"
            hidden
            @change="handleFile"
          />
        </label>
      </div>
      <form
        class="flex flex-col justify-end w-full"
        style="min-height: 50px"
        @submit.prevent="upload"
      >
        <div
          class="w-full flex flex-col space-y-2 mb-5"
        >
          <label for="offer">Выберите оффера</label>
          <select
            id="offer"
            v-model="form['offer_id']"
            class="w-1/2 mb-1"
            :disabled="!origin"
          >
            <option
              v-for="offer in offers"
              :key="offer.id"
              :value="offer.id"
              v-text="offer.name"
            ></option>
          </select>
          <div
            class="text-red-800 text-xs"
            v-text="errors.get('0.offer_id')"
          ></div>
        </div>
        <div
          class="w-full flex flex-col space-y-2 mb-5"
        >
          <label for="affiliate">Выберите аффилиата</label>
          <select
            id="affiliate"
            v-model="form['affiliate_id']"
            class="w-1/2 mb-1"
            :disabled="!origin"
          >
            <option
              v-for="affiliate in affiliates"
              :key="affiliate.id"
              :value="affiliate.id"
              v-text="affiliate.name"
            ></option>
          </select>
          <div
            class="text-red-800 text-xs"
            v-text="errors.get('0.affiliate_id')"
          ></div>
        </div>
        <div class="pb-3">
          <ads-checkbox v-model="form.ignoreDoubles">
            Игнорировать дубли?
          </ads-checkbox>
        </div>
        <div class="flex w-full justify-center">
          <div>
            <button
              type="submit"
              :disabled="!origin"
              class="mr-2 btn-primary px-4 py-2 rounded"
            >
              Загрузить
            </button>
            <button
              type="reset"
              class="btn-secondary px-4 py-2 rounded"
              @click="reset"
            >
              Отмена
            </button>
          </div>
        </div>
      </form>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'leads-import-modal',
  data: () => ({
    origin: null,
    offers: [],
    affiliates: [],
    form: {
      offer_id: null,
      affiliate_id: null,
      ignoreDoubles: true,
    },

    errors: new ErrorBag(),
  }),
  created() {
    this.loadOffers();
    this.loadAffiliates();
  },
  methods: {
    loadOffers() {
      axios.get('/api/offers', {params: {all: true}})
        .then(r => this.offers = r.data)
        .catch(err => this.$toast.error({title: 'Unable load offers.', message: err.response.data.message}));
    },
    loadAffiliates() {
      axios.get('/api/affiliates', {params: {all: true}})
        .then(r => this.affiliates = r.data)
        .catch(err => this.$toast.error({title: 'Unable load affiliates.', message: err.response.data.message}));
    },
    handleFile() {
      this.origin = this.$refs.file.files[0]['name'];
    },
    upload() {
      const formData = new FormData();
      formData.append('leads', this.$refs.file.files[0]);
      if (this.form.offer_id) {
        formData.append('offer_id', this.form.offer_id);
      }
      if (this.form.affiliate_id) {
        formData.append('affiliate_id', this.form.affiliate_id);
      }
      formData.append('ignore_doubles', this.form.ignoreDoubles);
      if(this.$refs.file.files[0]) {
        this.origin = this.$refs.file.files[0].name;
        axios
          .post('/api/imports/upload-leads', formData, {headers: {
            'Content-Type': 'multipart/form-data',
          }})
          .then(r => {
            this.$toast.info({title: 'Ок', message: 'Загрузка начата'});
            this.clear();
            this.close();
          })
          .catch(e => {
            this.errors.fromResponse(e);
            this.$toast.error({title: 'Ошибка', message: 'Не удалось загрузить файл'});
          });
      }
    },
    close() {
      this.$modal.hide('leads-upload-modal');
    },
    clear() {
      this.origin = null;
    },
    reset() {
      this.clear();
      this.close();
    },
  },
};
</script>
