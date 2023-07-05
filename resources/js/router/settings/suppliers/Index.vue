<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск поставщиков"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        :to="{'name':'suppliers.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasSuppliers"
      class="flex flex-col w-full bg-white shadow"
    >
      <suppliers-list-item
        v-for="supplier in suppliers"
        :key="supplier.id"
        :supplier="supplier"
      ></suppliers-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Поставщиков не найдено</h2>
    </div>
  </div>
</template>

<script>
import SuppliersListItem from '../../../components/settings/suppliers-list-item';
export default {
  name: 'suppliers-index',
  components: {SuppliersListItem},
  data:() => ({
    response: {},
  }),
  computed:{
    hasSuppliers(){
      return this.response.data !== undefined && this.response.data.length > 0;
    },
    suppliers() {
      return this.hasSuppliers ? this.response.data : [];
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(search = null){
      axios.get('/api/suppliers', {params:{search: search}})
        .then(response => {
          this.response = response;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить поставщиков.', message: err.response.data.message});
        });
    },
    search(event) {
      this.load(event.target.value === '' ? null : event.target.value);
    },
  },
};
</script>
