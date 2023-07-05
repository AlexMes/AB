<template>
  <div
    class="w-full h-full relative overflow-x-auto overflow-y-auto"
    :style="{maxHeight: `${rootHeight}px`}"
  >
    <div
      ref="fake"
      class="relative"
      :style="{minWidth: `${width}px`, minHeight: `${height}px`}"
    >
      <table
        ref="table"
        class="w-full table table-auto relative"
      >
        <thead
          v-if="hasHeader"
          class="text-gray-700 uppercase font-semibold w-full top-0"
        >
          <tr class="uppercase">
            <th
              v-for="(header,thIndex) in headerRow"
              :key="thIndex"
              class="sticky top-0 bg-gray-300"
            >
              <label
                class="flex items-center cursor-pointer select-none"
                @click="toggleSort(thIndex)"
              >
                <span v-text="header"></span>
                <span class="ml-2 inline-flex flex-col">
                  <fa-icon
                    class="hover:text-teal-800 -mb-4"
                    :class="{'text-teal-700':thIndex == columnIndex && isAsc}"
                    :icon="['far', 'sort-up']"
                  ></fa-icon>
                  <fa-icon
                    class="hover:text-teal-800"
                    :class="{'text-teal-700':thIndex == columnIndex && !isAsc}"
                    :icon="['far', 'sort-down']"
                  ></fa-icon>
                </span>
              </label>
            </th>
          </tr>
        </thead>
        <tbody
          v-if="hasRows"
          class="items-center text-sm border-b px-3"
        >
          <report-table-row
            v-for="(tableRow, trIndex) in sortedData"
            :key="trIndex"
            :cells="tableRow"
          >
          </report-table-row>
        </tbody>
        <tfoot
          v-if="hasLate"
          class="w-full bg-gray-300"
        >
          <tr class="font-semibold text-lg">
            <td
              v-for="(cell,index) in report.late"
              :key="index"
              class="bg-gray-200 sticky bottom-0"
              v-text="cell"
            >
            </td>
          </tr>
        </tfoot>
        <tfoot
          v-if="hasTotal"
          class="w-full bg-gray-300"
        >
          <tr class="font-semibold text-lg">
            <td
              v-for="(cell,index) in report.summary"
              :key="index"
              class="bg-gray-200 sticky"
              :class="hasLate ? 'bottom-28' : 'bottom-0'"
              v-text="cell"
            >
            </td>
          </tr>
        </tfoot>
        <tfoot
          v-if="hasReturned"
          class="w-full bg-gray-300"
        >
          <tr class="font-semibold text-lg">
            <td
              v-for="(cell,index) in report.returned"
              :key="index"
              class="bg-gray-200 sticky bottom-12 whitespace-pre-line"
              v-html="cell"
            >
            </td>
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
  props:{
    report:{
      type:Object,
      required:true,
      default: () => ({
        headers:[],
        rows:[],
        summary:[],
      }),
    },
    highlightRoi:{
      type:Boolean,
      default:true,
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
    width: 0,
    height: 0,
    rootHeight: 400,
    selectedRows: [],
  }),
  computed:{
    hasHeader(){
      return this.report.headers !== undefined && Object.values(this.report.headers).length > 0;
    },
    hasRows(){
      return this.report.rows !== undefined && Object.values(this.report.rows).length > 0;
    },
    hasTotal(){
      return this.report.summary !== undefined && Object.values(this.report.summary).length > 0;
    },
    hasLate() {
      return this.report.late !== undefined && Object.values(this.report.late).length > 0;
    },
    hasReturned() {
      return this.report.returned !== undefined && Object.values(this.report.returned).length > 0;
    },
    sortedData() {
      if (this.column !== undefined && this.column !== null) {
        return this.sortByColumn(this.column, this.isAsc ? 'asc' : 'desc');
      }
      return this.report.rows;
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
    hasLate(){
      return this.report.late !== undefined && Object.values(this.report.late).length > 0;
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
  methods:{
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


