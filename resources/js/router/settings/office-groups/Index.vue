<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск групп"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'office-groups.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasGroups"
    >
      <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Id
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Название
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Филиал
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <office-group-list-item
                    v-for="group in groups"
                    :key="group.id"
                    :group="group"
                    @deleted="remove"
                  ></office-group-list-item>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Групп не найдено</h2>
    </div>
  </div>
</template>

<script>
import OfficeGroupListItem from '../../../components/settings/office-group-list-item';
export default {
  name: 'office-groups-index',
  components: {OfficeGroupListItem},
  data:() => ({
    groups: {},
    response: {},
    search: null,
  }),
  computed:{
    hasGroups() {
      return this.groups !== undefined && this.groups.length > 0;
    },
  },
  watch: {
    search() {
      this.load();
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios.get('/api/office-groups', {
        params: {
          paginate: true,
          page: page,
          search: this.search === '' ? null : this.search,
        },
      })
        .then(response => {
          this.response = response.data;
          this.groups = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить группы.', message: err.response.data.message});
        });
    },
    remove(event) {
      const index = this.groups.findIndex(group => group.id === event.group.id);
      if (index !== -1) {
        this.groups.splice(index, 1);
      }
    },
  },
};
</script>
