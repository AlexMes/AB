<template>
  <div class="w-full h-auto bg-white rounded shadow mb-8">
    <div
      v-if="supplier"
      class="w-full h-auto mb-8"
    >
      <div class="px-4 py-2 flex flex-row justify-between border-b items-center">
        <div
          class="text-2xl font-bold p-3 text-gray-700"
          v-text="supplier.name"
        ></div>
        <div>
          <router-link
            :to="{name:'suppliers.update'}"
            class="button btn-primary"
          >
            Редактировать
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'suppliers-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      supplier:{},
    };
  },
  created(){
    this.load();
  },
  methods: {
    load(){
      axios.get(`/api/suppliers/${this.id}`)
        .then(r => this.supplier = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить поставщика.', message: e.response.data.message}));
    },
  },
};
</script>
