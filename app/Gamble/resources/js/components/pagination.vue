<template>
  <nav
    v-if="shouldPaginate"
    class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6"
  >
    <div class="hidden sm:block">
      <p class="text-sm leading-5 text-gray-700">
        От
        <span
          class="font-medium"
          v-text="metaFrom"
        ></span>
        до
        <span
          class="font-medium"
          v-text="metaTo"
        ></span>
        из
        <span
          class="font-medium"
          v-text="metaTotal"
        ></span>
      </p>
    </div>
    <div class="flex justify-between flex-1 sm:justify-end">
      <a
        v-if="hasPreviousPage"
        class="relative cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700"
        @click="previous"
      >
        Предыдущая
      </a>
      <a
        v-if="hasNextPage"
        class="relative cursor-pointer inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700"
        @click="next"
      >
        Следующая
      </a>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'pagination',
  props: {
    response: {
      type: Object,
      default: null,
    },
  },
  data(){
    return{
      page: 1,
    };
  },
  computed: {
    shouldPaginate() {
      return !!this.hasPreviousPage || !!this.hasNextPage;
    },
    hasNextPage() {
      if (this.response !== null){
        if(this.response.links !== undefined)
          return this.response.links.next !== null;
        return this.response.next_page_url !== null;
      }
      return false;
    },
    hasPreviousPage() {
      if (this.response !== null){
        if(this.response.links !== undefined)
          return this.response.links.prev !== null;
        return this.response.prev_page_url !== null;
      }
      return false;
    },
    /*currentPage(){
      if(this.response.meta !== undefined){
        return this.response.meta.current_page;
      }
      return this.response.current_page;
    },
    lastPage() {
      if(this.response.meta !== undefined){
        return this.response.meta.last_page;
      }
      return this.response.last_page;
    },*/
    metaFrom() {
      if(this.response.meta !== undefined){
        return this.response.meta.from;
      }
      return this.response.from;
    },
    metaTo() {
      if(this.response.meta !== undefined){
        return this.response.meta.to;
      }
      return this.response.to;
    },
    metaTotal() {
      if(this.response.meta !== undefined){
        return this.response.meta.total;
      }
      return this.response.total;
    },
    /*pages() {
      if(this.response.meta !== undefined) {
        let leftOffset = this.response.meta.current_page - 3,
            rightOffset = this.response.meta.current_page + 3 + 1,
            range = [];
        for (let i = 1; i <= this.response.meta.last_page; i++) {
          if (i >= leftOffset && i < rightOffset) {
            range.push(i);
          }
        }
        return range;
      }
      let leftOffset = this.response.current_page - 3,
          rightOffset = this.response.current_page + 3 + 1,
          range = [];
      for (let i = 1; i <= this.response.last_page; i++) {
        if (i >= leftOffset && i < rightOffset) {
          range.push(i);
        }
      }
      return range;
    },*/
  },
  watch: {
    page: {
      handler: 'load',
    },
  },
  methods: {
    previous() {
      this.page -= 1;
    },
    next() {
      this.page += 1;
    },
    /*setPage(number) {
      this.page = number;
    },*/
    load() {
      this.$emit('load', this.page);
    },
  },
};
</script>

<style scoped>

</style>
