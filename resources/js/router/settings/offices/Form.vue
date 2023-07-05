<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="office.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый офис
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
                v-model="office.name"
                type="text"
                placeholder="Amazing partner Co."
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
        <!--        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="office_cpl"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            CPL
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm sm:leading-5">
                    $
                  </span>
                </div>
                <input
                  id="office_cpl"
                  v-model="office.cpl"
                  type="number"
                  name="office_cpl"
                  class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                  placeholder="0"
                  aria-describedby="price-currency"
                  @input="errors.clear('cpl')"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <span
                    id="office-cpl"
                    class="text-gray-500 sm:text-sm sm:leading-5"
                  >
                    USD
                  </span>
                </div>
              </div>
            </div>
            <span
              v-if="errors.has('cpl')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('cpl')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="office_cpa"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            СPA
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm sm:leading-5">
                    $
                  </span>
                </div>
                <input
                  id="office_cpa"
                  v-model="office.cpa"
                  type="number"
                  name="office_cpa"
                  class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                  placeholder="0"
                  aria-describedby="price-currency"
                  @input="errors.clear('cpa')"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <span
                    id="price-currency"
                    class="text-gray-500 sm:text-sm sm:leading-5"
                  >
                    USD
                  </span>
                </div>
              </div>
            </div>
            <span
              v-if="errors.has('cpa')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('cpa')"
            ></span>
          </div>
        </div>-->
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="destination"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Доставка
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <mutiselect
                v-model="office.destination"
                :show-labels="false"
                :allow-empty="false"
                :options="destinations"
                placeholder="Выберите доставку"
                track-by="id"
                label="name"
                @input="errors.clear('destination_id')"
              ></mutiselect>
            </div>
            <span
              v-if="errors.has('destination_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('destination_id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Разрешить менеджерам передачу лидов
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="office.allow_transfer"></toggle>
            </div>
            <span
              v-if="errors.has('allow_transfer')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('allow_transfer')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Учавствует в утренней раздачи
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="office.distribution_enabled"></toggle>
            </div>
            <span
              v-if="errors.has('distribution_enabled')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('distribution_enabled')"
            ></span>
          </div>
        </div>
          <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
          >
              <label
                  class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                  Запретить выдачу с меткой caucassian
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                  <div class="max-w-lg">
                      <toggle v-model="office.disallow_caucasian"></toggle>
                  </div>
                  <span
                      v-if="errors.has('disallow_caucasian')"
                      class="block text-red-600 text-sm mt-1"
                      v-text="errors.get('disallow_caucasian')"
                  ></span>
              </div>
          </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="default_start_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Время поумолчанию страрта заказа
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="default_start_at"
                v-model="office.default_start_at"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                type="time"
              />
            </div>
            <span
              v-if="errors.has('default_start_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('default_start_at')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="default_stop_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Время поумолчанию стопа заказа
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="default_stop_at"
                v-model="office.default_stop_at"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                type="time"
              />
            </div>
            <span
              v-if="errors.has('default_stop_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('default_stop_at')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="frx_office_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            FRX office ID
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="frx_office_id"
                v-model="office.frx_office_id"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                type="number"
                min="1"
              />
            </div>
            <span
              v-if="errors.has('frx_office_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('frx_office_id')"
            ></span>
          </div>
        </div>
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="tenant"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Арендатор
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="tenant"
                v-model="office.frx_tenant_id"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('frx_tenant_id')"
              >
                <option
                  v-for="tenant in tenants"
                  :key="tenant.id"
                  :value="tenant.id"
                  v-text="tenant.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('frx_tenant_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('frx_tenant_id')"
            ></span>
          </div>
        </div>
        <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
          <label class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
            C/P
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="office.is_cp"></toggle>
            </div>
            <span
              v-if="errors.has('is_cp')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('is_cp')"
            ></span>
          </div>
        </div>
        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="$router.push({name:'offices.show', params:{id:id}})"
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
  name: 'offices-form',
  props: {
    id: {
      type: [Number,String],
      required: false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    office: {
      name: null,
      /*cpl: 0.00,
      cpa: 0.00,*/
      destination_id: null,
      destination: null,
      allow_transfer: false,
      distribution_enabled: false,
      default_start_at: null,
      default_stop_at: null,
      frx_office_id: null,
      frx_tenant_id: null,
      is_cp: false,
      disallow_caucasian: false,
    },
    destinations: [],
    tenants: [],
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.id !== null && this.id !== undefined;
    },
    cleanForm() {
      return {
        name: this.office.name,
        destination_id: !!this.office.destination ? this.office.destination.id : null,
        allow_transfer: this.office.allow_transfer,
        distribution_enabled: this.office.distribution_enabled,
        default_start_at: this.office.default_start_at,
        default_stop_at: this.office.default_stop_at,
        frx_office_id: this.office.frx_office_id,
        frx_tenant_id: this.office.frx_tenant_id,
        is_cp: this.office.is_cp,
        disallow_caucasian: this.office.disallow_caucasian,
      };
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      axios.get('/api/leads-destinations', {params: {active: true}})
        .then(({data}) => this.destinations = data.data)
        .catch(err => this.$toast.error({title:'Error',message:err.response.data.message}));

      this.loadTenants();

      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/offices/${this.id}`)
        .then(r => this.office = r.data)
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить офис', message: err.response.data.message});
        });
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    create(){
      this.isBusy = true;
      axios.post('/api/offices/', this.cleanForm)
        .then(r => {
          this.$router.push({name:'offices.index'});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить офис.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/offices/${this.office.id}`, this.cleanForm)
        .then(r => this.$router.push({name:'offices.show',params:{id:r.data.id}}))
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить офис', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    loadTenants() {
      axios.get('/api/tenants', {params: {all: true}})
        .then(({data}) => {
          this.tenants = data;
          this.tenants.unshift({id: null, name: 'Не выбран'});
        })
        .catch(err => this.$toast.error({title:'Unable to load tenants', message:err.response.data.message}));
    },
  },
};
</script>
