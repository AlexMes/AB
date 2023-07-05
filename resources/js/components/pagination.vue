<template>
  <div
    v-if="shouldPaginate"
    class="w-full mt-12 flex flex-row justify-between"
  >
    <div>
      Страница
      <span
        class="font-semibold"
        v-text="currentPage"
      ></span> из
      <span
        class="font-semibold"
        v-text="lastPage"
      ></span>
    </div>
    <div class="flex flex-row text-lg justify-around">
      <div
        v-if="hasPreviousPage"
        class="mx-2"
      >
        <a
          class="hover:font-medium cursor-pointer text-sm"
          rel="prev"
          @click.prevent="previous"
        >
          &leftarrow; Предыдущая
        </a>
      </div>
      <div>
        <ul class="list-reset flex">
          <li
            v-for="page in pages"
            :class="{ 'text-red-dark font-semibold' : page===currentPage}"
            class="font-medium inline-block mx-1 cursor-pointer"
            @click.prevent="setPage(page)"
            v-text="page"
          ></li>
          <li v-if="currentPage !== lastPage">
            ...
          </li>
          <li
            v-if="currentPage !== lastPage"
            :class="{ 'text-red-dark font-semibold' : page===currentPage}"
            class="font-medium inline-block mx-1 cursor-pointer"
            @click.prevent="setPage(lastPage)"
            v-text="lastPage"
          ></li>
        </ul>
      </div>
      <div
        v-if="hasNextPage"
        class="mx-2"
      >
        <a
          class="hover:font-medium cursor-pointer text-sm"
          rel="next"
          @click.prevent="next"
        >
          Следующая &rightarrow;
        </a>
      </div>
    </div>
    <div v-show="metaTotal > 0">
      <span
        class="font-semibold"
        v-text="metaFrom"
      ></span>
      <span
        v-show="metaTotal > 1"
        class="font-semibold"
        v-text="` - ${metaTo}`"
      ></span>
      <span class="font-weight-normal">из</span>
      <span
        class="font-semibold"
        v-text="metaTotal"
      ></span>
      <span class=""></span>
    </div>
  </div>
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
    currentPage(){
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
    },
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
    pages() {
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
    },
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
    setPage(number) {
      this.page = number;
    },
    load() {
      this.$emit('load', this.page);
    },
  },
};
</script>
