<template>
  <div class="flex flex-col">
    <div class="mb-8 flex w-full flex-row justify-between">
      <div class="flex flex-1">
        <input
          type="search"
          class="w-full mr-5 border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
          placeholder="Поиск партнера"
          maxlength="50"
          @input="search"
        />
      </div>
      <router-link
        :to="{'name':'affiliates.create'}"
        class="button btn-primary"
      >
        Добавить
      </router-link>
    </div>
    <div
      v-if="hasAffiliates"
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
                      Имя
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Оффер
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Источник трафика
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Филиал
                    </th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                      Постбек
                    </th>
                    <th class="px-6 py-3 bg-gray-50"></th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <affiliate-list-item
                    v-for="affiliate in affiliates"
                    :key="affiliate.id"
                    :affiliate="affiliate"
                  ></affiliate-list-item>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Affiliates not found</h2>
    </div>
  </div>
</template>

<script>
import AffiliateListItem from '../../../components/settings/affiliate-list-item';
export default {
  name: 'affiliates-index',
  components: {AffiliateListItem},
  data:() => ({
    affiliates:{},
    response: {},
  }),
  computed:{
    hasAffiliates(){
      return this.affiliates !== undefined && this.affiliates.length > 0;
    },
  },
  created(){
    this.load();
  },
  methods:{
    load(search = null){
      axios.get('/api/affiliates', {params: {search: search}})
        .then(response => {
          this.affiliates = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить affiliates.', message: err.response.data.message});
        });
    },
    search(event) {
      this.load(event.target.value === '' ? null : event.target.value);
    },
  },
};
</script>
