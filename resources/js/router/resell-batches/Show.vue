<template>
  <div class="container mx-auto">
    <div v-if="batch">
      <div class="bg-white rounded shadow">
        <div
          v-if="batch"
          class="flex items-center justify-between px-4 py-5 border-b border-gray-200 sm:px-6"
        >
          <h3
            class="text-lg leading-6 font-medium text-gray-900"
            v-text="batch.name"
          >
          </h3>
          <div
            v-if="['pending', 'paused', 'in_process'].includes(batch.status)"
            class="flex"
          >
            <span class="relative z-0 inline-flex shadow-sm rounded-md">
              <router-link
                :is="['pending'].includes(batch.status) ? 'router-link' : 'span'"
                :to="{name:'resell-batches.update',params:{id:batch.id}}"
                class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
              >
                <fa-icon
                  :icon="['far', 'pencil-alt']"
                  class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                  fixed-width
                ></fa-icon>
                <span v-if="['pending'].includes(batch.status)">
                  Редактировать
                </span>
                <span v-else>-----</span>
              </router-link>
              <span class="-ml-px relative block">
                <button
                  type="button"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                  aria-label="Expand"
                  @click.prevent="isEditMenuOpen = !isEditMenuOpen"
                >
                  <svg
                    class="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </button>
                <!--
          Dropdown panel, show/hide based on dropdown state.

          Entering: "transition ease-out duration-100"
            From: "transform opacity-0 scale-95"
            To: "transform opacity-100 scale-100"
          Leaving: "transition ease-in duration-75"
            From: "transform opacity-100 scale-100"
            To: "transform opacity-0 scale-95"
        -->
                <div
                  class="origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg"
                  :class="{hidden: !isEditMenuOpen}"
                >
                  <div class="rounded-md bg-white shadow-xs">
                    <div class="py-1">
                      <a
                        v-if="['pending', 'paused'].includes(batch.status)"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="$modal.show('start-resell-batch-modal', {batch: batch})"
                      >
                        <fa-icon
                          class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                          :icon="['far', 'play']"
                          fixed-width
                        ></fa-icon>
                        Запустить
                      </a>
                      <a
                        v-if="['in_process'].includes(batch.status)"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="pause"
                      >
                        <fa-icon
                          class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                          :icon="['far', 'pause']"
                          fixed-width
                        ></fa-icon>
                        На паузу
                      </a>
                      <a
                        v-if="['pending', 'in_process', 'paused'].includes(batch.status)"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="cancel"
                      >
                        <fa-icon
                          class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                          :icon="['far', 'times-circle']"
                          fixed-width
                        ></fa-icon>
                        Отменить
                      </a>
                    </div>
                  </div>
                </div>
              </span>
            </span>
          </div>
        </div>
        <div
          v-if="batch"
          class="px-4 py-5 sm:p-0"
        >
          <dl>
            <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Статус
              </dt>
              <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                <span
                  class="text-gray-800 rounded-full py-1 px-2"
                  :class="status.color"
                  v-text="status.text"
                ></span>
              </dd>
            </div>
            <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Дата регистрации лидов
              </dt>
              <dd
                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                v-text="registeredAt"
              >
              </dd>
            </div>
            <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Получающие офисы
              </dt>
              <dd
                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                v-text="offices"
              >
              </dd>
            </div>
            <div
              v-if="batch.substitute_offer"
              class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
            >
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Оффер для подмены
              </dt>
              <dd
                class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2"
                v-text="batch.substitute_offer.name"
              >
              </dd>
            </div>
            <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Создавать копию офера _R
              </dt>
              <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                <div
                  class="w-4 h-4 ml-1 border-0 rounded-full"
                  :class="[batch.create_offer ? 'bg-green-500' : 'bg-red-500']"
                ></div>
              </dd>
            </div>
            <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Симулировать автологин
              </dt>
              <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                <div
                  class="w-4 h-4 ml-1 border-0 rounded-full"
                  :class="[batch.simulate_autologin ? 'bg-green-500' : 'bg-red-500']"
                ></div>
              </dd>
            </div>
            <div class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Игнорировать роуты в паузе
              </dt>
              <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                <div
                  class="w-4 h-4 ml-1 border-0 rounded-full"
                  :class="[batch.ignore_paused_routes ? 'bg-green-500' : 'bg-red-500']"
                ></div>
              </dd>
            </div>
          </dl>
        </div>
      </div>
      <resell-batches-leads
        :batch="batch"
        class="mt-8"
        @batch-updated="update"
      ></resell-batches-leads>
    </div>
    <start-resell-batch-modal @updated="update"></start-resell-batch-modal>
  </div>
</template>

<script>
import moment from 'moment';
import ResellBatchesLeads from './Leads';
import StartResellBatchModal from '../../components/resell-batches/start-resell-batch-modal';

export default {
  name: 'resell-batches-show',
  components: {
    ResellBatchesLeads,
    StartResellBatchModal,
  },
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      batch: {
        id: this.id,
        substitute_offer: {
          id: null,
          name: null,
        },
      },
      isEditMenuOpen: false,
    };
  },
  computed: {
    status() {
      let status = {
        color: 'bg-gray-200',
        text: 'В ожидании',
      };

      if (this.batch.status === 'in_process') {
        status.color = 'bg-yellow-200';
        status.text = 'В процесе';
      }
      if (this.batch.status === 'paused') {
        status.color = 'bg-blue-200';
        status.text = 'На паузе';
      }
      if (this.batch.status === 'canceled') {
        status.color = 'bg-red-200';
        status.text = 'Отменён';
      }
      if (this.batch.status === 'finished') {
        status.color = 'bg-green-200';
        status.text = 'Завершён';
      }

      return status;
    },
    registeredAt() {
      return moment(this.batch.registered_at).format('YYYY-MM-DD');
    },
    offices() {
      return this.batch.offices !== undefined && this.batch.offices.length > 0
        ? this.batch.offices.map(office => office.name).join(', ')
        : 'Все';
    },
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/resell-batches/${this.id}`)
        .then(r => (this.batch = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить пакет.',
            message: e.response.data.message,
          });
        });
    },

    update(event) {
      this.batch = event.batch;
    },

    pause() {
      axios.post(`/api/resell-batches/${this.batch.id}/pause`)
        .then(response => {
          this.$toast.success({title: 'Ok', message: 'Перевыдача на паузе.'});
          this.update({batch: response.data});
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось поставить перевыдачу на паузу.', message: err.response.data.message});
        });
    },

    cancel() {
      axios.post(`/api/resell-batches/${this.batch.id}/cancel`)
        .then(response => {
          this.$toast.success({title: 'Ok', message: 'Перевыдача отменена.'});
          this.update({batch: response.data});
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось отменить перевыдачу.', message: err.response.data.message});
        });
    },
  },
};
</script>
