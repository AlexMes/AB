<template>
  <div class="w-full h-auto bg-white rounded shadow mb-8">
    <div
      v-if="page"
      class="w-full h-auto mb-8"
    >
      <div class="px-4 py-2 flex flex-row justify-between border-b items-center">
        <div
          class="text-2xl font-bold p-3 text-gray-700"
          v-text="page.name"
        ></div>
        <div>
          <router-link
            :to="{name:'pages.update'}"
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
  name: 'pages-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      page: {},
    };
  },
  created(){
    this.load();
  },
  methods: {
    load(){
      axios.get(`/api/pages/${this.id}`)
        .then(r => this.page = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить страницу.', message: e.response.data.message}));
    },
  },
};
</script>
