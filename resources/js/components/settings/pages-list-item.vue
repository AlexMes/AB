<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="page.id"
    ></div>
    <div>
      <router-link
        :to="`pages/${page.id}`"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="page.name"
      ></router-link>
    </div>
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="page.type"
    ></div>
    <fa-icon
      :icon="['far','times-circle']"
      class="ml-auto text-gray-700 hover:text-teal-700 font-semibold cursor-pointer"
      fixed-width
      @click="deleteItem"
    ></fa-icon>
  </div>
</template>

<script>
export default {
  name: 'pages-list-item',
  props:{
    page:{
      required:true,
      type:Object,
    },
  },
  methods: {
    deleteItem() {
      if (confirm('Sure to delete this page ?')) {
        axios.delete(`/api/pages/${this.page.id}`)
          .then(() => {
            this.$emit('pageDeleted', {page: this.page});
            this.$toast.success({title: 'Success', message: 'Page has been deleted.'});
          })
          .catch(err => this.$toast.error({title: 'Unable to delete this page.', message: err.response.data.message}));
      }
    },
  },
};
</script>
