<template>
  <div class="container mx-auto flex flex-col">
    <div class="flex w-full justify-between mb-8">
      <div class="w-2/5 flex-col px-2">
        <label class="flex w-full font-semibold mb-2">Менеджеры</label>
        <mutiselect
          v-model="filters.managers"
          :show-labels="false"
          :multiple="true"
          :options="managers"
          placeholder="Выберите менеджеров"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>

      <div class="w-2/5 flex flex-col px-2">
        <button
          type="button"
          class="button btn-primary mt-7"
          :disabled="isBusy"
          @click.prevent="load"
        >
          <span v-if="isBusy">
            <fa-icon
              :icon="['far','spinner']"
              class="mr-2 fill-current"
              spin
              fixed-width
            ></fa-icon> Загрузка
          </span>
          <span v-else>Загрузить</span>
        </button>
      </div>
    </div>

    <div class="overflow-x-auto overflow-y-hidden flex flex-col w-full">
      <div
        v-if="hasAssignments"
        class="flex flex-col w-full"
      >
        <div
          class="w-full px-4 py-3 bg-gray-200 text-gray-600 flex items-center uppercase font-bold"
        >
          <div class="w-1/12">
            ID
          </div>
          <div class="w-2/12">
            Менеджер
          </div>
          <div class="w-3/12">
            Драйвер
          </div>
          <div class="w-5/12">
            Ошибка
          </div>
          <div class="w-1/12">
          </div>
        </div>
        <assignment-delivery-fails-list-item
          v-for="assignment in assignments"
          :key="assignment.id"
          :assignment="assignment"
        ></assignment-delivery-fails-list-item>
      </div>
      <div
        v-else
        class="w-full flex justify-center bg-white p-4 font-medium text-xl text-gray-700"
      >
        <span v-if="isBusy">
          <fa-icon
            :icon="['far','spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon>Загрузка
        </span>
        <span
          v-else
          class="flex text-center"
        >
          Нет ошибок
        </span>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
  </div>
</template>

<script>

export default {
  name: 'logs-assignment-delivery-fails',
  data:() => ({
    response: null,
    assignments: [],
    isBusy: false,
    managers: [],
    filters: {
      managers:[],
    },
  }),
  computed:{
    hasAssignments(){
      return this.assignments.length > 0;
    },
    cleanFilters() {
      return {
        managers: this.filters.managers === null ? null : this.filters.managers.map(manager => manager.id),
      };
    },
  },
  created() {
    this.load();
    this.getManagers();
  },
  methods:{
    load(page = 1) {
      this.isBusy = true;
      axios.get('/api/assignment-delivery-fails',{params: {page, ...this.cleanFilters}})
        .then(response => {
          this.response = response.data;
          this.assignments = response.data.data;
        })
        .catch(error => console.error)
        .finally(() => this.isBusy = false);
    },
    getManagers() {
      axios
        .get('/api/managers')
        .then(response => (this.managers = response.data))
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось загрузить менеджеров.',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>
<style>
    th{
        @apply .text-left;
        @apply px-2;
        @apply py-3;
        @apply whitespace-no-wrap;
    }
    td{
        @apply py-4;
        @apply px-2;
        @apply border-b;
        @apply whitespace-no-wrap;
    }
</style>
