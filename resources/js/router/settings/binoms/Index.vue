<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        :to="{'name':'binoms.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasBinoms"
      class="flex flex-col w-full bg-white shadow"
    >
      <binoms-list-item
        v-for="binom in binoms"
        :key="binom.id"
        :binom="binom"
      ></binoms-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Binom не найдено</h2>
    </div>
  </div>
</template>

<script>
import BinomsListItem from '../../../components/settings/binoms-list-item';
export default {
  name: 'binoms-index',
  components: {BinomsListItem},
  data:() => ({
    response: {},
  }),
  computed:{
    hasBinoms(){
      return this.response.data !== undefined && this.response.data.length > 0;
    },
    binoms() {
      return this.hasBinoms ? this.response.data : [];
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(search = null){
      axios.get('/api/binoms', {params:{search: search}})
        .then(response => {
          this.response = response;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить binoms.', message: err.response.data.message});
        });
    },
    search(event) {
      this.load(event.target.value === '' ? null : event.target.value);
    },
  },
};
</script>
