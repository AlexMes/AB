<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск формы"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        :to="{'name':'forms.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasForms"
      class="flex flex-col w-full bg-white shadow"
    >
      <forms-list-item
        v-for="form in forms"
        :key="form.id"
        :form="form"
      ></forms-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Форм не найдено</h2>
    </div>
  </div>
</template>

<script>
import FormsListItem from '../../../components/settings/forms-list-item';
export default {
  name: 'forms-index',
  components: {FormsListItem},
  data:() => ({
    response: {},
  }),
  computed:{
    hasForms(){
      return this.response.data !== undefined && this.response.data.length > 0;
    },
    forms() {
      return this.hasForms ? this.response.data : [];
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(search = null){
      axios.get('/api/integrations/forms', {params:{search: search}})
        .then(response => {
          this.response = response;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить формы.', message: err.response.data.message});
        });
    },
    search(event) {
      this.load(event.target.value === '' ? null : event.target.value);
    },
  },
};
</script>
