<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск филиалов"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{name:'branches.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasBranches"
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
                      Доступ к статистике
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <branch-list-item
                    v-for="branch in branches"
                    :key="branch.id"
                    :branch="branch"
                    @deleted="remove"
                  ></branch-list-item>
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
      <branch-telegram-modal></branch-telegram-modal>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Филиалов не найдено</h2>
    </div>
  </div>
</template>

<script>
import BranchListItem from '../../../components/settings/branch-list-item';
import BranchTelegramModal from '../../../components/settings/branch-telegram-modal';
export default {
  name: 'branches-index',
  components: {BranchTelegramModal, BranchListItem},
  data:() => ({
    branches:{},
    response: {},
    search: null,
  }),
  computed:{
    hasBranches(){
      return this.branches !== undefined && this.branches.length > 0;
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
      axios.get('/api/branches', {
        params: {
          paginate: true,
          page: page,
          search: this.search === '' ? null : this.search,
        },
      })
        .then(response => {
          this.response = response.data;
          this.branches = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить филиалы.', message: err.response.data.message});
        });
    },
    remove(event) {
      const index = this.branches.findIndex(branch => branch.id === event.branch.id);
      if (index !== -1) {
        this.branches.splice(index, 1);
      }
    },
  },
};
</script>
