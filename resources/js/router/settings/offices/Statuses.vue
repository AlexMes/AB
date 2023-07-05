<template>
  <div class="w-full">
    <div
      v-if="actualStatuses.length > 0"
      class="shadow"
    >
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <mutiselect
            v-model="status"
            :show-labels="false"
            :options="actualStatuses"
            placeholder="Выберите статус"
            track-by="name"
            label="name"
          ></mutiselect>
        </div>
        <div class="w-auto flex items-center">
          <button
            class="button btn-primary flex items-center ml-3"
            @click="add"
          >
            <fa-icon
              :icon="['far','plus']"
              class="fill-current mr-2"
            ></fa-icon> Добавить
          </button>
        </div>
      </div>
    </div>
    <div
      v-if="hasStatuses"
    >
      <div class="shadow">
        <office-status-list-item
          v-for="(status, index) in statuses"
          :key="index"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :status="status"
          @deleted="remove"
          @updated="update"
        ></office-status-list-item>
      </div>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Пусто</p>
    </div>
  </div>
</template>

<script>
import OfficeStatusListItem from '../../../components/settings/office-status-list-item';
export default {
  name: 'offices-statuses',
  components: {OfficeStatusListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    statuses: [],
    allStatuses: [],
    status: null,
  }),
  computed: {
    hasStatuses() {
      return this.statuses.length > 0;
    },
    actualStatuses() {
      return this.allStatuses.filter(status => this.statuses.findIndex(item => item.status === status.name) === -1);
    },
  },
  created() {
    this.load();
    this.loadAllStatuses();
  },
  methods:{
    load() {
      axios
        .get('/api/office-statuses', {params: {office_id: this.id}})
        .then(r => (this.statuses = r.data))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить статусы офиса.',
            message: e.response.data.message,
          }),
        );
    },
    loadAllStatuses() {
      axios.get('/api/statuses')
        .then(r => this.allStatuses = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить список статусов.', message: e.response.data.message}));
    },
    update(event) {
      const index = this.statuses.findIndex(status => status.id === event.status.id);
      if (index !== -1) {
        this.$set(this.statuses, index, event.status);
      }
    },
    add() {
      if (this.status !== null) {
        axios.post('/api/office-statuses', {office_id: this.id, status: this.status.name})
          .then(r => {
            if (this.statuses.findIndex(status => status.id === r.data.id) === -1) {
              this.statuses.push(r.data);
            }
            this.status = null;
          })
          .catch(err => this.$toast.error({title: 'Не удалось добавить статус.', message: err.response.data.message}));
      }
    },
    remove(event) {
      const index = this.statuses.findIndex(status => status.id === event.status.id);
      if (index !== -1) {
        this.statuses.splice(index, 1);
      }
    },
  },
};
</script>
