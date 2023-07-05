<template>
  <div>
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold leading-6 text-gray-900">
          <span v-if="isEditing">Редактирование записи</span>
          <span v-else>Создание записи</span>
        </h1>
      </div>
    </header>
    <main class="container mx-auto">
      <div class="max-w-full pb-20 mx-auto sm:px-6 lg:px-8">
        <div
          v-if="errors.hasMessage()"
          class="rounded-md bg-red-100 mt-6 p-4 max-w-7xl mx-auto"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <svg
                class="h-5 w-5 text-red-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p
                class="text-sm leading-5 font-medium text-red-800"
                v-text="errors.message"
              >
              </p>
            </div>
          </div>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
          <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              v-text="`[${insight.id}] ${insight.date}`"
            >
            </h3>
          </div>
          <form class="px-6">
            <div
              class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5"
            >
              <label
                for="date"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Дата
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="date"
                    v-model="insight.date"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="date"
                    @input="errors.clear('date')"
                  />
                </div>
                <p
                  v-if="errors.has('date')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('date')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="account_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Аккаунт
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="account_id"
                    v-model="insight.account"
                    :show-labels="false"
                    :options="accounts"
                    placeholder="Выберите аккаунт"
                    track-by="id"
                    label="name"
                    @input="errors.clear('account_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('account_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('account_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="campaign_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Кампания
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="campaign_id"
                    v-model="insight.campaign"
                    :show-labels="false"
                    :options="campaigns"
                    placeholder="Выберите кампанию"
                    track-by="id"
                    label="name"
                    @input="errors.clear('campaign_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('campaign_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('campaign_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="google_app_id"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Приложение
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <multiselect
                    id="google_app_id"
                    v-model="insight.google_app"
                    :show-labels="false"
                    :options="googleApps"
                    placeholder="Выберите приложение"
                    track-by="id"
                    label="name"
                    @input="errors.clear('google_app_id')"
                  ></multiselect>
                </div>
                <p
                  v-if="errors.has('google_app_id')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('google_app_id')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="pour_type"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Способ залива
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <select
                    id="pour_type"
                    v-model="insight.pour_type"
                    class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                    @input="errors.clear('pour_type')"
                  >
                    <option
                      v-for="type in pourTypes"
                      :key="type.id"
                      :value="type.id"
                      v-text="type.name"
                    >
                    </option>
                  </select>
                </div>
                <p
                  v-if="errors.has('pour_type')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('pour_type')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="target"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Таргет
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="target"
                    v-model="insight.target"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('target')"
                  />
                </div>
                <p
                  v-if="errors.has('target')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('target')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="sales"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Продажи
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="sales"
                    v-model="insight.sales"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="number"
                    @input="errors.clear('sales')"
                  />
                </div>
                <p
                  v-if="errors.has('sales')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('sales')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="deposit_cnt"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Количество депозитов
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="deposit_cnt"
                    v-model="insight.deposit_cnt"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="number"
                    @input="errors.clear('deposit_cnt')"
                  />
                </div>
                <p
                  v-if="errors.has('deposit_cnt')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('deposit_cnt')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="deposit_sum"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Сумма депозита
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
                      id="deposit_sum"
                      v-model="insight.deposit_sum"
                      type="number"
                      name="deposit_sum"
                      step="1"
                      class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                      placeholder="0"
                      aria-describedby="deposit-sum"
                      @input="errors.clear('deposit_sum')"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <span
                        id="deposit-sum"
                        class="text-gray-500 sm:text-sm sm:leading-5"
                      >
                        USD
                      </span>
                    </div>
                  </div>
                </div>
                <p
                  v-if="errors.has('deposit_sum')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('deposit_sum')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="impressions"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Показы
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="impressions"
                    v-model="insight.impressions"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="number"
                    @input="errors.clear('impressions')"
                  />
                </div>
                <p
                  v-if="errors.has('impressions')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('impressions')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="installs"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Установки
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="installs"
                    v-model="insight.installs"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="number"
                    @input="errors.clear('installs')"
                  />
                </div>
                <p
                  v-if="errors.has('installs')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('installs')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="spend"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Кост
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
                      id="spend"
                      v-model="insight.spend"
                      type="number"
                      name="spend"
                      step="0.01"
                      class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                      placeholder="0"
                      aria-describedby="price-currency"
                      @input="errors.clear('spend')"
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
                <p
                  v-if="errors.has('spend')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('spend')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="registrations"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Регистрации
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="registrations"
                    v-model="insight.registrations"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="number"
                    @input="errors.clear('registrations')"
                  />
                </div>
                <p
                  v-if="errors.has('registrations')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('registrations')"
                ></p>
              </div>
            </div>
            <div
              class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
            >
              <label
                for="optimization_goal"
                class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
              >
                Цель оптимизации
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                  <input
                    id="optimization_goal"
                    v-model="insight.optimization_goal"
                    class="form-input block w-full sm:text-sm sm:leading-5"
                    type="text"
                    @input="errors.clear('optimization_goal')"
                  />
                </div>
                <p
                  v-if="errors.has('optimization_goal')"
                  class="mt-2 text-sm text-red-600"
                  v-text="errors.get('optimization_goal')"
                ></p>
              </div>
            </div>
            <div class="mt-6 sm:mt-5 border-t border-gray-200 py-5">
              <div class="flex justify-end">
                <span class="inline-flex rounded-md shadow-sm">
                  <a
                    class="inline-flex cursor-pointer items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out"
                    @click="cancel"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Отмена
                  </a>
                </span>
                <span class="ml-3 inline-flex rounded-md shadow-sm">
                  <button
                    type="submit"
                    class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
                    :disabled="isBusy"
                    @click.prevent="save"
                  >
                    <svg
                      class="w-4 h-4 mr-2"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    ><path
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                      fill-rule="evenodd"
                    /></svg> Сохранить
                  </button>
                </span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import ErrorBag from '../../../../../../resources/js/utilities/ErrorBag';

export default {
  name: 'insights-form',
  props: {
    id: {
      type: [String, Number],
      required: false,
      default: null,
    },
  },
  data: () => {
    return {
      isBusy: false,
      insight: {
        date: null,
        account: null,
        campaign: null,
        google_app: null,
        pour_type: 'manual',
        target: null,
        sales: null,
        deposit_cnt: null,
        deposit_sum: null,
        impressions: null,
        installs: null,
        spend: null,
        registrations: null,
        optimization_goal: null,
      },
      accounts: [],
      campaigns: [],
      googleApps: [],
      pourTypes: [
        {id: 'manual', name: 'Ручной'},
        {id: 'auto', name: 'Авто'},
      ],
      errors: new ErrorBag(),
    };
  },
  computed: {
    isEditing() {
      return this.id !== null;
    },
    cleanForm() {
      return {
        date: this.insight.date,
        account_id: this.insight.account !== null ? this.insight.account.id : null,
        campaign_id: this.insight.campaign !== null ? this.insight.campaign.id : null,
        google_app_id: this.insight.google_app !== null ? this.insight.google_app.id : null,
        pour_type: this.insight.pour_type,
        target: this.insight.target,
        sales: this.insight.sales,
        deposit_cnt: this.insight.deposit_cnt,
        deposit_sum: this.insight.deposit_sum,
        impressions: this.insight.impressions,
        installs: this.insight.installs,
        spend: this.insight.spend,
        registrations: this.insight.registrations,
        optimization_goal: this.insight.optimization_goal,
      };
    },
  },
  created() {
    if (this.isEditing) {
      this.load();
    }
    this.loadAccounts();
    this.loadCampaigns();
    this.loadGoogleApps();
  },
  methods: {
    load() {
      axios.get(`/api/insights/${this.id}`)
        .then(({data}) => this.insight = data)
        .catch(err => this.$toast.error({title: 'Unable to load the insight.', message: err.response.data.message}));
    },
    loadAccounts() {
      axios.get('/api/accounts', {params: {all: true}})
        .then(({data}) => {
          this.accounts = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load accounts.', message: err.response.data.message}));
    },
    loadCampaigns() {
      axios.get('/api/campaigns', {params: {all: true}})
        .then(({data}) => {
          this.campaigns = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load campaigns.', message: err.response.data.message}));
    },
    loadGoogleApps() {
      axios.get('/api/applications', {params: {all: true}})
        .then(({data}) => {
          this.googleApps = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load apps.', message: err.response.data.message}));
    },
    save() {
      this.isBusy = true;
      this.isEditing ? this.update() : this.create();
    },
    cancel() {
      this.isEditing
        ? this.$router.push({name:'insights.show', params:{id: this.id}})
        : this.$router.push({name:'insights.index'});
    },
    create() {
      axios
        .post('/api/insights/', this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'insights.index',
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Unable to create insight.', message: err.response.data.message});
        }).finally(()=> this.isBusy = false);
    },
    update() {
      axios
        .put(`/api/insights/${this.id}`, this.cleanForm)
        .then(r => {
          this.$router.push({
            name: 'insights.show',
            params: { id: r.data.id },
          });
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({
            title: 'Unable to update insight.',
            message: err.response.data.message,
          });
        }).finally(()=> this.isBusy = false);
    },
  },
};
</script>

<style scoped>

</style>
