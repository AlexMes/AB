<template>
  <tr
    :class="background"
    @click="isSelected = !isSelected"
  >
    <td
      v-for="(cell,index) in cells"
      :key="index"
      v-text="cell"
    ></td>
  </tr>
</template>

<script>
export default {
  name: 'report-table-row',
  props:{
    cells:{
      type:[Array,Object],
      required:true,
    },
  },
  data: () => ({
    isSelected:false,
  }),
  computed:{
    background() {
      if(this.isSelected){
        return 'bg-orange-500';
      }

      if(this.cells.roi === undefined){
        return;
      }
      const ROI = Number.parseFloat(this.cells.roi);
      if(ROI > 150.0){
        return 'bg-teal-500 text-white';
      }
      if(ROI > 0.0 ){
        return 'bg-green-200';
      }

      return 'bg-red-300';
    },
  },
};
</script>
