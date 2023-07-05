<template>
  <div
    class="h-screen -my-2 overflow-scroll"
    :style="{maxHeight: `${rootHeight}px`}"
  >
    <div
      class="inline-block min-w-full align-middle border-b border-gray-200 shadow sm:rounded-lg"
    >
      <table
        ref="table"
        class="relative table w-full table-auto"
      >
        <thead v-if="hasHeader">
          <tr>
            <th
              v-for="(header, thIndex) in headerRow"
              :key="thIndex"
              class="sticky top-0 px-3 py-2 text-xs font-medium leading-4 text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
            >
              <div
                class="flex items-center cursor-pointer"
                @click="toggleSort(thIndex)"
              >
                <span v-text="header"></span>
                <div class="ml-1">
                  <a class="text-gray-400">
                    <svg
                      aria-hidden="true"
                      width="12"
                      height="12"
                      focusable="false"
                      data-prefix="fas"
                      data-icon="sort-up"
                      class="-mb-2 svg-inline--fa fa-sort-up fa-w-10 hover:text-indigo-500"
                      :class="{'text-indigo-600': thIndex === columnIndex && isAsc}"
                      role="img"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 320 512"
                    >
                      <path
                        fill="currentColor"
                        d="M279 224H41c-21.4 0-32.1-25.9-17-41L143 64c9.4-9.4 24.6-9.4 33.9 0l119 119c15.2 15.1 4.5 41-16.9 41z"
                      />
                    </svg>
                    <svg
                      aria-hidden="true"
                      width="12"
                      height="12"
                      focusable="false"
                      data-prefix="fas"
                      data-icon="sort-down"
                      class="-mt-2 svg-inline--fa fa-sort-down fa-w-10 hover:text-indigo-500"
                      :class="{'text-indigo-600': thIndex === columnIndex && !isAsc}"
                      role="img"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 320 512"
                    >
                      <path
                        fill="currentColor"
                        d="M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z"
                      />
                    </svg>
                  </a>
                </div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody
          v-if="hasRows"
          class="bg-white"
        >
          <tr
            v-for="(cells, trIndex) in sortedData"
            :key="trIndex"
            class="hover:bg-gray-100"
          >
            <td
              v-for="(value, tdKey) in cells"
              :key="tdKey"
              class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
              v-text="value"
            ></td>
          </tr>
        </tbody>
        <tfoot v-if="hasTotal">
          <tr class="">
            <td
              v-for="(value, tdKey) in report.summary"
              :key="tdKey"
              class="sticky bottom-0 px-3 py-2 text-sm font-semibold leading-5 text-gray-600 whitespace-no-wrap bg-gray-200 border-b border-gray-200"
              v-text="value"
            ></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script>
import {orderBy} from 'lodash-es';

export default {
  name: 'report-table',
  props: {
    report: {
      type: Object,
      required: true,
      default: () => ({
        headers: [],
        rows: [],
        summary: [],
      }),
    },
    limitHeight: {
      type: Number,
      default: null,
    },
    autoHeight: {
      type:Boolean,
      default:true,
    },
  },
  data: () => ({
    column: null,
    columnIndex: null,
    isAsc: false,
    rootHeight: 400,
  }),
  computed: {
    hasHeader() {
      return this.report.headers !== undefined && Object.values(this.report.headers).length > 0;
    },
    hasRows() {
      return this.report.rows !== undefined && Object.values(this.report.rows).length > 0;
    },
    hasTotal() {
      return this.report.summary !== undefined && Object.values(this.report.summary).length > 0;
    },
    firstRow() {
      if (Array.isArray(this.report.rows)) {
        return this.report.rows[0];
      }
      return Object.values(this.report.rows)[0];
    },
    properHeader() {
      return Object.keys(this.firstRow);
    },
    headerRow() {
      if (Array.isArray(this.report.headers)) {
        return this.report.headers;
      }
      return Object.values(this.report.headers);
    },
    sortedData() {
      if (this.column !== undefined && this.column !== null) {
        return this.sortByColumn(this.column, this.isAsc ? 'asc' : 'desc');
      }
      return this.report.rows;
    },
  },
  watch: {
    report() {
      this.setSize();
    },
    limitHeight() {
      this.setSize();
    },
  },
  mounted() {
    this.subscribe();
  },
  beforeDestroy() {
    this.unsubscribe();
  },
  methods: {
    subscribe() {
      window.addEventListener('resize', this.setSize);
      window.addEventListener('rotate', this.setSize);
    },
    unsubscribe() {
      window.removeEventListener('resize', this.setSize);
      window.removeEventListener('rotate', this.setSize);
    },
    setSize() {
      this.$nextTick(() => {
        this.width = this.$refs.table.offsetWidth;
        this.height = this.$refs.table.offsetHeight;
        this.rootHeight = this.limitHeight || this.calculateHeight();
      });
    },
    calculateHeight() {
      if (this.autoHeight) {
        const windowHeight = window.innerHeight;
        const sibling = this.$el.previousSibling && this.$el.previousSibling.previousSibling ?
          this.$el.previousSibling.previousSibling :
          null;
        const siblingHeight = sibling ? sibling.offsetHeight : 400;
        const height = windowHeight - siblingHeight - 150;
        return height > 400 ? height : 400;
      }
      return 400;
    },
    sortByColumn(column, direction = 'asc') {
      const self = this;
      return orderBy(this.report.rows, [function (row) {
        const converted = self.convert(row[column]);
        return isNaN(converted) ? row[column] : converted;
      }], [direction]);
    },
    convert(value) {
      let converted = parseFloat(value);

      if (isNaN(converted)) {
        converted = value ? parseFloat(value.split(' ')[1]) : '';
      }

      return converted;
    },
    toggleSort(index) {
      this.isAsc = !this.isAsc;
      this.columnIndex = index;
      this.column = this.properHeader[index];
    },
  },
};
</script>

<style scoped>

</style>
