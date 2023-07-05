<template>
  <tbody>
    <tr :class="{ strike: isDeleted }">
      <td
        class="px-2 py-3 pl-5"
        v-text="route.id"
      ></td>
      <td v-if="!isFinished && $root.user.role !== 'sales'">
        <fa-icon
          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
          :icon="['far', statusIcon]"
          fixed-width
          @click.prevent="toggleStatus"
        ></fa-icon>
        <fa-icon
          class="text-lg text-gray-700 cursor-pointer fill-current hover:text-teal-700"
          :icon="['far', 'stop']"
          fixed-width
          @click.prevent="stopRoute"
        ></fa-icon>
      </td>
      <td v-else></td>
      <td>
        <span
          v-if="route.manager"
          v-text="route.manager.name"
        ></span>
        <span
          v-else
          class="text-gray-600"
        >All office</span>
        <a
          v-if="route.manager"
          target="_blank"
          rel="noopener"
          :href="
            `https://docs.google.com/spreadsheets/d/${route.manager.spreadsheet_id}`
          "
        ><fa-icon
          :icon="sheetLinkIcon"
          fixed-width
        ></fa-icon></a>
        <fa-icon
          v-if="route.priority"
          class="ml-2 text-lg text-yellow-600 fill-current"
          :icon="priorityIcon"
          fixed-width
        ></fa-icon>
        <span
          v-if="$root.user.role !== 'sales' && !editing"
          class="mx-2 cursor-pointer hover:text-teal-700"
          @click="$modal.show('transfer-route-modal',{route: route})"
        >
          <fa-icon
            :icon="transferIcon"
            class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
            fixed-width
          ></fa-icon>
        </span>
      </td>
      <td>
        <span
          v-if="route.offer"
          v-text="route.offer.name"
        ></span>
        <span
          v-if="!isFinished && $root.user.role !== 'sales' && !editing"
          class="mx-2 cursor-pointer hover:text-teal-700"
          @click="$modal.show('change-route-offer-modal',{route: route})"
        >
          <fa-icon
            :icon="['far','exchange']"
            class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
            fixed-width
          ></fa-icon>
        </span>
      </td>
      <td v-if="editing">
        <select
          v-model="route.destination_id"
        >
          <option
            v-for="destination in destinations"
            :key="destination.id"
            :value="destination.id"
            v-text="destination.name"
          ></option>
        </select>
      </td>
      <td
        v-else-if="route.destination"
        v-text="route.destination.name"
      ></td>
      <td v-else>
        Default
      </td>
      <td v-if="editing">
        <input
          v-model="route.leadsOrdered"
          type="number"
          class="w-32 px-2 py-3 text-gray-700 placeholder-gray-400 border-b"
        />
      </td>
      <td v-else>
        {{ route.leadsReceived + ' / ' + route.leadsOrdered }}
      </td>
      <td v-if="editing">
        <toggle v-model="route.priority"></toggle>
      </td>
      <td
        v-if="!editing && route.last_received_at"
        v-text="route.last_received_at"
      ></td>
      <td v-else-if="!editing && route.last_received_at === null">
        -
      </td>
      <td>
        <input
          v-if="editing"
          v-model="route.start_at"
          type="time"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-else-if="route.start_at"
          v-text="route.start_at"
        ></span>
        <span v-else>
          -
        </span>
      </td>
      <td>
        <input
          v-if="editing"
          v-model="route.stop_at"
          type="time"
          class="block w-full transition duration-150 ease-in-out form-input sm:text-sm sm:leading-5"
        />
        <span
          v-else-if="route.stop_at"
          v-text="route.stop_at"
        ></span>
        <span v-else>
          -
        </span>
      </td>
      <td
        v-if="isDeleted"
        class="mx-2"
        @click="restore"
      >
        <fa-icon
          v-if="$root.user.role !== 'sales'"
          class="text-gray-700 cursor-pointer fill-current"
          :icon="restoreIcon"
          fixed-width
        ></fa-icon>
      </td>
      <td v-else>
        <dropdown>
          <template v-slot:trigger>
            <fa-icon
              :icon="['far','bars']"
              class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
              fixed-width
            ></fa-icon>
          </template>
          <div class="text-gray-700 space-y-3">
            <div
              v-if="route.leadsReceived > 0"
              class="mx-2 cursor-pointer hover:text-teal-700"
              @click="toggleDetails"
            >
              <fa-icon
                :icon="detailsIcon"
                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                fixed-width
              ></fa-icon>
              Детали
            </div>
            <div
              v-if="$root.user.role !== 'sales'"
              class="mx-2 cursor-pointer hover:text-teal-700"
              @click="toggleOrSave"
            >
              <fa-icon
                :icon="editIcon"
                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                fixed-width
              ></fa-icon>
              Редактировать
            </div>
            <div
              v-if="$root.user.role !== 'sales' && !editing"
              class="mx-2 cursor-pointer hover:text-teal-700"
              @click="removeDelayedAssignments"
            >
              <fa-icon
                :icon="removeDelayedIcon"
                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                fixed-width
              ></fa-icon>
              Удалить плавную выдачу
            </div>
            <div
              v-if="$root.user.role !== 'sales' && !editing"
              class="mx-2 cursor-pointer hover:text-teal-700"
              @click="removeUndeliveredAssignments"
            >
              <fa-icon
                :icon="removeUndeliveredIcon"
                class="text-gray-700 cursor-pointer fill-current hover:text-teal-700"
                fixed-width
              ></fa-icon>
              Удалить недоставленные назначения
            </div>
            <div
              v-if="$root.user.role !== 'sales'"
              class="mx-2 cursor-pointer hover:text-red-700"
              @click="destoryOrCancel"
            >
              <fa-icon
                :icon="deleteIcon"
                class="text-gray-700 cursor-pointer fill-current hover:text-red-700"
                fixed-width
              ></fa-icon>
              Удалить роут
            </div>
          </div>
        </dropdown>
      </td>
    </tr>
    <order-route-details
      v-if="showDetails"
      :route="route"
      :order="order"
    ></order-route-details>
  </tbody>
</template>

<script>
import {
  faCheck,
  faFileExcel,
  faMinusCircle,
  faPencil,
  faPlusCircle,
  faTimes,
  faTrashRestore,
  faPeopleArrows,
  faCrown,
  faTruckPlow,
  faPlaneSlash,
} from '@fortawesome/pro-regular-svg-icons';
import OrderRouteDetails from './OrderRouteDetails';
import Dropdown from '../../components/dropdown';

export default {
  name: 'route-list-item',
  components: {OrderRouteDetails, Dropdown},
  props: {
    route: {
      type: Object,
      required: true,
    },
    order: {
      type: Object,
      required: true,
    },
    destinations: {
      type: Array,
      required: true,
    },
  },
  data: () => ({
    editing: false,
    showDetails: false,
  }),
  computed: {
    editIcon() {
      return this.editing ? faCheck : faPencil;
    },
    deleteIcon() {
      return faTimes;
    },
    restoreIcon() {
      return faTrashRestore;
    },
    sheetLinkIcon() {
      return faFileExcel;
    },
    priorityIcon(){
      return faCrown;
    },
    isDeleted() {
      return this.route.deleted_at !== undefined && this.route.deleted_at !== null;
    },
    detailsIcon() {
      return this.showDetails ? faMinusCircle : faPlusCircle;
    },
    isFinished() {
      return this.route.leadsOrdered === this.route.leadsReceived;
    },
    statusIcon() {
      return this.route.status === 'ACTIVE' ? 'pause' : 'play';
    },
    transferIcon() {
      return faPeopleArrows;
    },
    removeDelayedIcon() {
      return faTruckPlow;
    },
    removeUndeliveredIcon() {
      return faPlaneSlash;
    },
  },
  methods: {
    toggle() {
      this.editing = !this.editing;
    },
    toggleDetails() {
      this.showDetails = !this.showDetails;
    },
    toggleOrSave() {
      if (!this.editing) {
        this.toggle();
        return;
      }
      axios
        .put(`/api/leads-order-routes/${this.route.id}`, this.route)
        .then(response => {
          this.$emit('routeUpdated', {route: response.data});
          this.toggle();
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Cant update route',
          }),
        );
    },
    destoryOrCancel() {
      if (this.editing) {
        this.toggle();
        return;
      }
      this.$modal.show('delete-route-modal', {route: this.route});
    },
    restore() {
      axios
        .post(`/api/leads-order-routes/${this.route.id}/restore`)
        .then(response => {
          this.$toast.success({
            title: 'OK',
            message: 'Route restored',
          });
          this.$eventHub.$emit('lead-order-route-restored', {route: this.route});
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Cant restore route',
          }),
        );
    },

    toggleStatus() {
      if (this.route.status === 'ACTIVE') {
        this.pauseRoute();
      } else {
        this.startRoute();
      }
    },
    startRoute() {
      axios.post(`/api/leads-order-routes/${this.route.id}/start`)
        .then(response => {
          this.route.status = response.data.status;
          this.$toast.success({
            title: 'OK',
            message: 'Route was started',
          });
        })
        .catch(err => {
          this.$toast.error({
            title: 'Error',
            message: 'Cant start route',
          });
        });
    },
    pauseRoute() {
      axios.post(`/api/leads-order-routes/${this.route.id}/pause`)
        .then(response => {
          this.route.status = response.data.status;
          this.$toast.success({
            title: 'OK',
            message: 'Route was paused',
          });
        })
        .catch(err => {
          this.$toast.error({
            title: 'Error',
            message: 'Cant pause route',
          });
        });
    },
    stopRoute() {
      axios.post(`/api/leads-order-routes/${this.route.id}/stop`)
        .then(response => {
          this.route.leadsOrdered = response.data.leadsOrdered;
          this.$emit('stopped');
          this.$toast.success({
            title: 'OK',
            message: 'Route was stopped',
          });
        })
        .catch(err => {
          this.$toast.error({
            title: 'Error',
            message: 'Cant stop route',
          });
        });
    },
    removeDelayedAssignments() {
      if (confirm('Уверены, что хотите удалить отложенные(неотправленные) назначения ?')) {
        axios.post(`/api/leads-order-routes/${this.route.id}/remove-delayed-assignments`)
          .then(response => {
            this.showDetails = false;
            this.$emit('delayed-assignments-deleted');
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
        axios.post(`/api/leads-order-routes/${this.route.id}/remove-undelivered-assignments`)
          .then(response => {
            this.showDetails = false;
            this.$emit('delayed-undelivered-deleted');
            this.$toast.success({
              title: 'OK',
              message: 'Undelivered assignments removed.',
            });
          })
          .catch(err => {
            this.$toast.error({
              title: 'Error',
              message: 'Cant remove undelivered assignments',
            });
          });
      }
    },
  },
};
</script>

<style scoped>
.strike {
    @apply line-through;
    @apply text-gray-600;
}
</style>
