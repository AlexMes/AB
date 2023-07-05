<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="bundle.utm_campaign"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новая связка
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
            for="offer_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Оффера
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="offer_id"
                v-model="bundle.offer"
                :show-labels="false"
                :allow-empty="true"
                :options="offers"
                placeholder="Выберите оффер"
                track-by="id"
                label="name"
                @select="errors.clear('offer_id')"
              ></mutiselect>
              <div
                v-if="errors.has('offer_id')"
                class="text-red-600 text-sm mt-2"
                v-text="errors.get('offer_id')"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="utm_campaign"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название связки (UTM campaign)
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="utm_campaign"
                v-model="bundle.utm_campaign"
                :show-labels="false"
                :allow-empty="true"
                :options="utm"
                placeholder="Выберите UTM campaign"
                @select="errors.clear('utm_campaign')"
              ></mutiselect>
              <div
                v-if="errors.has('utm_campaign')"
                class="text-red-600 text-sm mt-2"
                v-text="errors.get('utm_campaign')"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="examples"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Примеры наименования кампейнов и адсетов
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="examples"
                v-model="bundle.examples"
                type="text"
                rows="3"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('examples')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('examples')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('examples')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="geo"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Гео
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="geo"
                v-model="bundle.geo"
                type="text"
                placeholder="Гео"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('geo')"
              />
            </div>
            <span
              v-if="errors.has('geo')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('geo')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="age"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Возраст
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="age"
                v-model="bundle.age"
                type="number"
                placeholder="10"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('age')"
              />
            </div>
            <span
              v-if="errors.has('age')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('age')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="gender"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Пол
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="gender"
                v-model="bundle.gender"
                :show-labels="false"
                :allow-empty="true"
                :options="genders"
                placeholder="Выберите пол"
                @select="errors.clear('gender')"
              ></mutiselect>
              <div
                v-if="errors.has('gender')"
                class="text-red-600 text-sm mt-2"
                v-text="errors.get('gender')"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="interests"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Список интересов
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="interests"
                v-model="bundle.interests"
                type="text"
                rows="3"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('interests')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('interests')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('interests')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="device"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Устройство
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="device"
                v-model="bundle.device"
                :show-labels="false"
                :allow-empty="true"
                :options="devices"
                placeholder="Выберите устройство"
                @select="errors.clear('device')"
              ></mutiselect>
              <div
                v-if="errors.has('device')"
                class="text-red-600 text-sm mt-2"
                v-text="errors.get('device')"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="platform"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Платформа
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="platform"
                v-model="bundle.platform"
                :show-labels="false"
                :allow-empty="true"
                :options="platforms"
                placeholder="Выберите платформу"
                @select="errors.clear('platform')"
              ></mutiselect>
              <div
                v-if="errors.has('platform')"
                class="text-red-600 text-sm mt-2"
                v-text="errors.get('platform')"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="placements"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Плейсменты
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                id="placements"
                v-model="bundle.placements"
                :multiple="true"
                :close-on-select="false"
                :show-labels="false"
                :allow-empty="true"
                :limit="2"
                :limit-text="placementLimited"
                :options="placements"
                track-by="id"
                label="name"
                placeholder="Выберите плейсменты"
                @select="errors.clear('placements')"
              ></mutiselect>
              <div
                v-if="errors.has('placements')"
                class="text-red-600 text-sm mt-2"
                v-text="errors.get('placements')"
              ></div>
            </div>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="ad"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Ссылка на креативы
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="ad"
                v-model="bundle.ad"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('ad')"
              />
            </div>
            <span
              v-if="errors.has('ad')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('ad')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="prelend_link"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Ссылка на преленд
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="prelend_link"
                v-model="bundle.prelend_link"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('prelend_link')"
              />
            </div>
            <span
              v-if="errors.has('prelend_link')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('prelend_link')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="lend_link"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Ссылка на ленд
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="lend_link"
                v-model="bundle.lend_link"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('lend_link')"
              />
            </div>
            <span
              v-if="errors.has('lend_link')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('lend_link')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="utm_source"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            UTM source
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="utm_source"
                v-model="bundle.utm_source"
                type="text"
                placeholder="UTM source"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('utm_source')"
              />
            </div>
            <span
              v-if="errors.has('utm_source')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('utm_source')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="utm_content"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            UTM content
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="utm_content"
                v-model="bundle.utm_content"
                type="text"
                placeholder="UTM content"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('utm_content')"
              />
            </div>
            <span
              v-if="errors.has('utm_content')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('utm_content')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="utm_term"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            UTM term
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="utm_term"
                v-model="bundle.utm_term"
                type="text"
                placeholder="UTM term"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('utm_term')"
              />
            </div>
            <span
              v-if="errors.has('utm_term')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('utm_term')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="utm_medium"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            UTM medium
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="utm_medium"
                v-model="bundle.utm_medium"
                type="text"
                placeholder="UTM medium"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('utm_medium')"
              />
            </div>
            <span
              v-if="errors.has('utm_medium')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('utm_medium')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="text"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Основой текст
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="text"
                v-model="bundle.text"
                type="text"
                rows="7"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('text')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('text')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('text')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="description"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Описание
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="description"
                v-model="bundle.description"
                type="text"
                rows="3"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('description')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('description')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('description')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="title"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Заголовок
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="title"
                v-model="bundle.title"
                type="text"
                placeholder="Заголовок"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('title')"
              />
            </div>
            <span
              v-if="errors.has('title')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('title')"
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

export default {
  name: 'bundles-form',
  components: {
  },
  props: {
    id: {
      type: [String,Number],
      required: false,
      default: null,
    },
  },
  data: () => ({
    isBusy:false,
    bundle: {
      offer_id: null,
      offer: null,
      utm_campaign: null,
      examples: null,
      geo: null,
      age: null,
      gender: null,
      interests: null,
      device: null,
      platform: null,
      placements: [],
      ad: null,
      prelend_link: null,
      lend_link: null,
      utm_source: null,
      utm_content: null,
      utm_term: null,
      utm_medium: null,
      title: null,
      description: null,
      text: null,
    },
    offers: [],
    utm:[],
    genders: [
      'Мужской',
      'Женский',
    ],
    devices: [
      'Мобильный',
      'ПК',
    ],
    platforms: [
      'Facebook',
      'Instagram',
    ],
    placements: [],
    searchAd: null,
    errors: new ErrorBag(),
  }),
  computed: {
    isUpdating() {
      return this.$props.id !== null;
    },
    cleanForm() {
      return {
        offer_id: this.bundle.offer === null ? null : this.bundle.offer.id,
        utm_campaign: this.bundle.utm_campaign,
        examples: this.bundle.examples,
        geo: this.bundle.geo,
        age: this.bundle.age,
        gender: this.bundle.gender,
        interests: this.bundle.interests,
        device: this.bundle.device,
        platform: this.bundle.platform,
        placements: this.bundle.placements.length > 0 ? this.bundle.placements.map(p => p.id) : [],
        ad: this.bundle.ad,
        prelend_link: this.bundle.prelend_link,
        lend_link: this.bundle.lend_link,
        utm_source: this.bundle.utm_source,
        utm_content: this.bundle.utm_content,
        utm_term: this.bundle.utm_term,
        utm_medium: this.bundle.utm_medium,
        title: this.bundle.title,
        description: this.bundle.description,
        text: this.bundle.text,
      };
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      this.getOffers();
      this.getUtm();
      this.getPlacements();
      if (this.isUpdating) {
        this.load();
      }
    },
    load() {
      axios
        .get(`/api/bundles/${this.id}`)
        .then(r => {
          this.bundle = r.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить связку',
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
        ? this.$router.push({name:'bundles.general', params:{id: this.id}})
        : this.$router.push({name:'bundles.index'});
    },
    create() {
      axios
        .post('/api/bundles/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'bundles.index',
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось сохранить связку',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
    update() {
      axios
        .put(`/api/bundles/${this.bundle.id}`, this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'bundles.general',
            params: { id: r.data.id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Не удалось обновить связку',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },

    placementLimited(count) {
      return `и ещё ${count}`;
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

    getUtm(){
      axios.get('/api/utm-campaigns')
        .then(r => this.utm = r.data)
        .catch(err => this.$toast.error({title:'Ошибка', message:err.response.data.message}));
    },

    getPlacements() {
      axios.get('/api/placements')
        .then(r => this.placements = r.data)
        .catch(e => this.$toast.error({title: 'Ошибка', message: err.response.data.message}));
    },
  },
};
</script>
