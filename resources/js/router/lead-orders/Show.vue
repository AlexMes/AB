<template>
  <div class="container mx-auto">
    <div
      v-if="hasOrder"
      class="w-full h-auto mb-8 bg-white rounded shadow"
    >
      <div class="w-full h-auto mb-8">
        <div
          class="flex flex-row items-center justify-between px-4 py-2 border-b"
        >
          <div
            class="py-2 text-2xl font-bold text-gray-700"
          >
            <fa-icon
              v-if="order.deliverCount > 0"
              class="fill-current text-gray-700 text-lg"
              :icon="runSmoothSplitterIcon"
            ></fa-icon>
            Заказ #{{ order.id }}
          </div>
          <div
            v-if="$root.user.role !== 'sales'"
            class="flex"
          >
            <span class="relative z-0 inline-flex rounded-md shadow-sm">
              <router-link
                :to="{
                  name: 'leads-orders.update',
                  params: { id: order.id }
                }"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-l-md hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700"
              >
                Редактировать
              </router-link>
              <span class="relative block -ml-px">
                <button
                  type="button"
                  class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-r-md hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500"
                  aria-label="Expand"
                  @click.prevent="isButtonOpened = !isButtonOpened"
                >
                  <svg
                    class="w-5 h-5"
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
                  class="absolute right-0 w-56 mt-2 -mr-1 origin-top-right rounded-md shadow-lg"
                  :class="{hidden: !isButtonOpened}"
                >
                  <div class="bg-white rounded-md shadow-xs">
                    <div class="py-1">
                      <a
                        v-if="ordered !== received"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="startOrder"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="['far', 'play']"
                          fixed-width
                        ></fa-icon>
                        Запустить
                      </a>
                      <a
                        v-if="ordered !== received"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="pauseOrder"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="['far', 'pause']"
                          fixed-width
                        ></fa-icon>
                        Приостановить
                      </a>
                      <a
                        v-if="ordered !== received"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="stopOrder"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="['far', 'stop']"
                          fixed-width
                        ></fa-icon>
                        Остановить
                      </a>
                      <a
                        v-if="ordered !== received"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="$modal.show('transfer-mass-route-modal')"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="transferIcon"
                          fixed-width
                        ></fa-icon>
                        Передача заказов
                      </a>
                      <a
                        v-if="ordered !== received"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="$modal.show('change-mass-offer-modal')"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="['far','exchange']"
                          fixed-width
                        ></fa-icon>
                        Замена оффера
                      </a>
                      <a
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="$modal.show('clone-order-modal', {order: order})"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="['far', 'copy']"
                          fixed-width
                        ></fa-icon>
                        Копировать заказ
                      </a>
                      <a
                        v-if="$root.user.role !== 'sales'"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="removeDelayedAssignments"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="removeDelayedIcon"
                          fixed-width
                        ></fa-icon>
                        Удалить плавную выдачу
                      </a>
                      <a
                        v-if="$root.user.role !== 'sales'"
                        href="#"
                        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900"
                        @click.prevent="removeUndeliveredAssignments"
                      >
                        <fa-icon
                          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                          :icon="removeUndeliveredIcon"
                          fixed-width
                        ></fa-icon>
                        Удалить недоставленные назначения
                      </a>
                    </div>
                  </div>
                </div>
              </span>
            </span>
          </div>
        </div>
        <div class="flex flex-col">
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Дата:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="order.date"
              ></span>
            </div>
          </div>
          <div
            v-if="['admin'].includes($root.user.role)"
            class="flex w-full p-3 border-b"
          >
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Филиал:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                v-if="order.branch_id"
                class="font-normal text-gray-700"
                v-text="order.branch.name"
              ></span>
              <span v-else>-</span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Офис:
            </div>
            <div class="flex w-1/3 text-left">
              <router-link
                :to="{
                  name: 'offices.show',
                  params: { id: order.office_id }
                }"
                class="hover:text-teal-700"
                v-text="order.office.name"
              ></router-link>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Доставка:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                v-if="order.destination"
                class="font-normal text-gray-700"
                v-text="order.destination.name"
              ></span>
              <span
                v-else
                class="font-normal text-gray-700"
              >Default</span>
              <span
                class="ml-4 font-normal text-gray-700"
                v-text="`${order.deliveryPercent} %`"
              ></span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Старт в:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="order.start_at"
              ></span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Стоп в:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="order.stop_at"
              ></span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Автоудаление дублей:
            </div>
            <div class="flex w-1/3 text-left">
              <div
                class="w-4 h-4 ml-1 border-0 rounded-full"
                :class="[order.autodelete_duplicates ? 'bg-green-500' : 'bg-red-500']"
              ></div>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Живой трафик:
            </div>
            <div class="flex w-1/3 text-left">
              <div
                class="w-4 h-4 ml-1 border-0 rounded-full"
                :class="[!order.deny_live ? 'bg-green-500' : 'bg-red-500']"
              ></div>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Живой трафик не чаще:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="liveInterval"
              ></span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Прогресс:
            </div>
            <div
              v-if="hasOrder"
              class="flex items-center w-1/3 text-left"
            >
              <progress-widget
                class="flex w-full"
                :current="received"
                :goal="ordered"
              ></progress-widget>
            </div>
            <span
              class="flex ml-3"
              v-text="` ${received} / ${ordered}`"
            ></span>
          </div>
          <div
            v-for="(progress, index) in order.progress"
            :key="index"
            class="flex w-full p-3 border-b"
          >
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
              v-text="progress.name"
            ></div>
            <div class="flex items-center w-1/3 text-left">
              <progress-widget
                class="flex w-full"
                :current="progress.received"
                :goal="progress.ordered"
              ></progress-widget>
            </div>
            <span
              class="flex ml-3"
              v-text="
                `${progress.received} / ${progress.ordered}`
              "
            ></span>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Активные маршруты:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-blue-500"
                v-text="`${order.activeRoutesCount} / ${routes.length}`"
              ></span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Выполненные маршруты:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-green-600"
                v-text="`${order.finishedRoutesCount} / ${routes.length}`"
              ></span>
            </div>
          </div>
          <div class="flex w-full p-3 border-b">
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Приостановленные маршруты:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-orange-400"
                v-text="`${order.pausedRoutesCount} / ${routes.length}`"
              ></span>
            </div>
          </div>
          <div
            class="flex w-full p-3 border-b"
          >
            <div
              class="flex w-1/3 pl-5 font-semibold text-left text-gray-800"
            >
              Прогресс отложенных назначений:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal"
                v-text="`${order.deliverConfirmedCount} / ${order.deliverCount}`"
              ></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="relative w-full mt-3 bg-white rounded-md shadow">
      <route-form
        v-if="hasOrder && $root.user.role !== 'sales'"
        :order="order"
        :destinations="destinations"
      ></route-form>
      <div class="overflow-x-auto">
        <table
          v-if="hasRoutes && hasOrder"
          class="table w-full table-auto"
        >
          <thead
            class="sticky w-full text-gray-700 uppercase bg-gray-300"
          >
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                ID
              </th>
              <th class="px-2 py-3">
              </th>
              <th class="px-2 py-3">
                Менеджер
              </th>
              <th class="px-2 py-3">
                Оффер
              </th>
              <th class="px-2 py-3">
                Доставка
              </th>
              <th class="px-2 py-3">
                Прогресс
              </th>
              <th class="px-2 py-3">
                latest
              </th>
              <th class="px-2 py-3">
                Старт
              </th>
              <th class="px-2 py-3">
                Стоп
              </th>
              <th class="px-2 py-3"></th>
            </tr>
          </thead>
          <route-list-item
            v-for="route in routes"
            :key="route.id"
            :route="route"
            :order="order"
            :destinations="destinations"
            @routeUpdated="updateRoute"
            @stopped="loadOrder"
            @delayed-assignments-deleted="loadOrder();loadRoutes()"
            @delayed-undelivered-deleted="loadOrder();loadRoutes()"
          >
          </route-list-item>
        </table>
        <div
          v-else
          class="flex items-center w-full p-5 bg-white rounded shadow"
        >
          No routes found
        </div>
      </div>
    </div>
    <transfer-assignment-modal
      v-if="hasOrder"
      :order="order"
    ></transfer-assignment-modal>
    <transfer-route-modal
      v-if="hasOrder"
      :order="order"
    >
    </transfer-route-modal>
    <transfer-mass-route-modal
      v-if="hasOrder"
      :order="order"
    ></transfer-mass-route-modal>
    <change-route-offer-modal
      v-if="hasOrder"
      :order="order"
    ></change-route-offer-modal>
    <change-mass-offer-modal
      v-if="hasOrder"
      :order="order"
    ></change-mass-offer-modal>
    <clone-order-modal></clone-order-modal>
    <delete-route-modal
      v-if="hasOrder"
      :order="order"
    >
    </delete-route-modal>
    <edit-assignment-modal v-if="hasOrder"></edit-assignment-modal>
  </div>
</template>

<script>
import TransferAssignmentModal from '../../components/leads-orders/transfer-assignment-modal';
import TransferRouteModal from '../../components/leads-orders/transfer-route-modal';
import ChangeRouteOfferModal from '../../components/leads-orders/change-route-offer-modal';
import {faPeopleArrows, faReplyAll, faTruckPlow, faPlaneSlash} from '@fortawesome/pro-regular-svg-icons';
import ChangeMassOfferModal from '../../components/leads-orders/change-mass-offer-modal';
import TransferMassRouteModal from '../../components/leads-orders/transfer-mass-route-modal';
import CloneOrderModal from '../../components/leads-orders/clone-order-modal';
import DeleteRouteModal from '../../components/leads-orders/delete-route-modal';
import EditAssignmentModal from '../../components/leads-orders/edit-assignment-modal';
export default {
  name: 'show',
  components: {
    EditAssignmentModal,
    DeleteRouteModal,
    TransferMassRouteModal,
    ChangeMassOfferModal,
    TransferRouteModal,
    TransferAssignmentModal,
    ChangeRouteOfferModal,
    CloneOrderModal,
  },
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    order: null,
    routes: [],
    destinations: [],
    isButtonOpened: false,
  }),
  computed: {
    hasOrder() {
      return this.order !== null;
    },
    hasRoutes() {
      return this.routes.length > 0;
    },
    ordered() {
      if (this.hasRoutes) {
        return this.routes
          .map(route => parseInt(route.leadsOrdered, 0))
          .reduce((a, b) => a + b);
      }
      return 0;
    },
    received() {
      if (this.hasRoutes) {
        return this.routes
          .map(route => parseInt(route.leadsReceived, 0))
          .reduce((a, b) => a + b);
      }
      return 0;
    },
    transferIcon() {
      return faPeopleArrows;
    },
    runSmoothSplitterIcon() {
      return faReplyAll;
    },
    removeDelayedIcon() {
      return faTruckPlow;
    },
    removeUndeliveredIcon() {
      return faPlaneSlash;
    },
    liveInterval() {
      const interval = this.order.live_interval / 60;
      return interval === 0 ? 'Неограничено' : `${this.order.live_interval / 60} мин`;
    },
  },
  created() {
    this.loadOrder();
    this.loadRoutes();
    this.listen();
  },
  methods: {
    listen() {
      this.$eventHub.$on('lead-order-route-created', event => {
        this.loadRoutes();
      });
      this.$eventHub.$on('lead-order-route-deleted', event => {
        this.loadRoutes();
      });
      this.$eventHub.$on('lead-order-route-restored', event => {
        this.loadRoutes();
      });

      this.$eventHub.$on('assignment-transferred', () => {
        this.loadRoutes();
      });
    },
    loadOrder() {
      axios
        .get(`/api/leads-orders/${this.id}`)
        .then(({ data }) => {
          this.order = data;
          this.loadDestinations();
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Something gone wrong',
          }),
        );
    },
    updateRoute(event) {
      const index = this.routes.findIndex(r => r.id === event.route.id);
      if (index !== -1) {
        this.$set(this.routes, index, event.route);
      }
    },
    loadRoutes() {
      axios
        .get(`/api/leads-orders/${this.id}/routes`)
        .then(({ data }) => (this.routes = data))
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Cant load order routes',
          }),
        );
    },
    loadDestinations() {
      axios.get('/api/leads-destinations', {params: {office_id: this.order.office_id, active: true}})
        .then(({data}) => this.destinations = data.data)
        .catch(error => this.$toast.error({title: 'Cannot get destinations', message: error.response.data.message}));
    },

    startOrder() {
      if (confirm('Возобновить выдачу по заказу? ' )) {
        axios.post(`/api/leads-order/${this.id}/start`)
          .then(response => {
            this.loadRoutes();
            this.$toast.success({
              title: 'OK',
              message: 'Order was started',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant start order',
            });
          });
      }
    },
    pauseOrder() {
      if (confirm('Приостановить заказ? ')) {
        axios.post(`/api/leads-order/${this.id}/pause`)
          .then(response => {
            this.loadRoutes();
            this.$toast.success({
              title: 'OK',
              message: 'Order was paused',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant pause order',
            });
          });
      }
    },
    stopOrder() {
      if (confirm('Остановка заказа приведет к потере информации о не выданном объеме лидов. Продолжить?')) {
        axios.post(`/api/leads-order/${this.id}/stop`)
          .then(response => {
            this.order.progress = response.data.progress;
            this.loadRoutes();
            this.$toast.success({
              title: 'OK',
              message: 'Order was stopped',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant stop order',
            });
          });
      }
    },
    removeDelayedAssignments() {
      if (confirm('Уверены, что хотите удалить отложенные(неотправленные) назначения ?')) {
        axios.post(`/api/leads-order/${this.id}/remove-delayed-assignments`)
          .then(response => {
            this.loadOrder();
            this.loadRoutes();
            this.$toast.success({
              title: 'OK',
              message: 'Delayed assignments removed.',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant remove delayed assignments',
            });
          });
      }
    },
    removeUndeliveredAssignments() {
      if (confirm('Уверены, что хотите удалить недоставленные назначения ?')) {
        axios.post(`/api/leads-order/${this.id}/remove-undelivered-assignments`)
          .then(response => {
            this.loadOrder();
            this.loadRoutes();
            this.$toast.success({
              title: 'OK',
              message: 'Undelivered assignments removed.',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant remove Undelivered assignments',
            });
          });
      }
    },
  },
};
</script>
