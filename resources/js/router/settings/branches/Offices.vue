<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <mutiselect
            v-model="office"
            :show-labels="false"
            :options="allOffices"
            placeholder="Выберите офис"
            track-by="id"
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

    <div v-if="hasOffices">
      <div class="shadow">
        <branch-office-list-item
          v-for="o in offices"
          :key="o.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :office="o"
          @deleted="remove(o)"
        ></branch-office-list-item>
        <pagination
          :response="response"
          @load="load"
        ></pagination>
      </div>
    </div>

    <div
      v-else
      class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6"
    >
      <p>Офисов не найдено</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'branches-offices',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    office: null,
    offices: [],
    allOffices: [],
    response: null,
  }),
  computed: {
    hasOffices() {
      return this.offices.length > 0;
    },
  },
  created() {
    this.load();
    this.loadOffices();
  },
  methods: {
    load(page = 1) {
      axios
        .get(`/api/branches/${this.id}/offices`, { params: { page } })
        .then(({ data }) => {
          this.offices = data.data;
          this.response = data;
        })
        .catch(() =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load offices',
          }),
        );
    },
    loadOffices() {
      axios.get('/api/offices')
        .then(({data}) => {
          this.allOffices = data;
        })
        .catch(error => {
          this.$toast.error({
            title: 'Unable to load offices list.',
            message: error.response.data.message,
          });
        });
    },
    add() {
      if (this.office !== null) {
        axios
          .post(`/api/branches/${this.id}/offices`, {office_id: this.office.id})
          .then(response => {
            if (this.offices.findIndex(item => item.id === response.data.id) === -1) {
              this.offices.push(response.data);
            }
            this.office = null;
            this.$toast.success({title: 'Ok', message: 'Office was added successfully.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to add office.', message: err.response.data.message}));
      }
    },
    remove(office) {
      axios.delete(`/api/branches/${this.id}/offices/${office.id}`)
        .then(response => {
          const index = this.offices.findIndex(item => item.id === office.id);
          if (index !== -1) {
            this.offices.splice(index, 1);
          }

          this.$toast.success({title: 'Ok', message: 'Offices was detached successfully.'});
        })
        .catch(err => this.$toast.error({title: 'Unable to detach office.', message: err.response.data.message}));
    },
  },
};
</script>
