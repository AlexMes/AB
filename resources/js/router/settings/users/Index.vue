<template>
  <div class="flex flex-col">
    <div class="mb-8 flex flex-row justify-between">
      <div class="flex flex-1">
        <input
          v-model="search"
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск пользователей"
          maxlength="50"
        />
      </div>
      <router-link
        :to="{'name':'users.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>

    <div class="flex flex-no-wrap items-center mb-6">
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Офисы</label>
        <mutiselect
          v-model="filters.offices"
          :show-labels="false"
          :multiple="true"
          :options="offices"
          placeholder="Выберите офисы"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="flex flex-col align-middle mx-2 w-1/5">
        <label class="mb-2">Роль</label>
        <mutiselect
          v-model="filters.role"
          :show-labels="false"
          :multiple="false"
          :options="roles"
          placeholder="Выберите роль"
          track-by="id"
          label="name"
        ></mutiselect>
      </div>
      <div class="flex flex-col ml-auto mr-2 w-1/5">
        <button
          class="button btn-primary text-lg -mb-3 mt-2"
          :class="{'bg-gray-700' : isBusy}"
          :disabled="isBusy"
          @click="load"
        >
          <span v-if="isBusy"><fa-icon
            :icon="['far','spinner']"
            class="fill-current mr-2"
            spin
            fixed-width
          ></fa-icon> Загрузка</span>
          <span v-else>Применить</span>
        </button>
      </div>
    </div>

    <div
      v-if="hasUsers"
      class="flex flex-col shadow"
    >
      <user-list-item
        v-for="user in users"
        :key="user.id"
        :user="user"
      ></user-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Пользователей не найдено</h2>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import UserListItem from '../../../components/users/user-list-item';
import Pagination from '../../../components/pagination';

export default {
  name: 'users-index',
  components:{
    UserListItem,
    Pagination,
  },
  data:() => ({
    search: null,
    response:{},
    isBusy:false,
    filters: {
      offices: [],
      role: [],
    },
    offices: [],
    roles: [
      { id: 'buyer', name: 'Баер' },
      { id: 'teamlead', name: 'Тимлид' },
      { id: 'head', name: 'Office head' },
      { id: 'admin', name: 'Администратор' },
      { id: 'customer', name: 'Customer' },
      { id: 'farmer', name: 'Фармер' },
      { id: 'financier', name: 'Финансист' },
      { id: 'designer', name: 'Дизайнер' },
      { id: 'verifier', name: 'Верифаер' },
      { id: 'gamble-admin', name: 'Gamble Admin' },
      { id: 'gambler', name: 'Gamble Buyer' },
      { id: 'support', name: 'Support' },
      { id: 'developer', name: 'Разработчик' },
      { id: 'subsupport', name: 'Support staging' },
      { id: 'sales', name: 'Sales' },
    ],
  }),
  computed:{
    hasUsers(){
      return this.response.data !== undefined && this.response.data.length > 0;
    },
    users() {
      return this.hasUsers ? this.response.data : [];
    },
    cleanFilters(){
      return {
        office: this.filters.offices === null ? null : this.filters.offices.map(office => office.id),
        userRole: this.filters.role === null ? null : this.filters.role.id,
      };
    },
  },
  watch: {
    search() {
      this.load();
    },
  },
  created(){
    this.load();
    axios.get('/api/offices').then(response => this.offices = response.data).catch(error => console.error);
  },
  methods:{
    load(page = 1) {
      this.isBusy = true;
      axios.get('/api/users', {
        params: {
          page: page,
          name: this.search === '' ? null : this.search,
          ...this.cleanFilters,
        },
      })
        .then(response=>{
          this.response = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить баеров.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>

<style scoped>

</style>
