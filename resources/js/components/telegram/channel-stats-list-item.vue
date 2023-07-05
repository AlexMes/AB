<template>
  <tr class="bg-white">
    <td v-text="statistic.date"></td>
    <td v-text="statistic.channel.name"></td>
    <td v-text="`${statistic.cost} $` "></td>
    <td v-text="statistic.impressions"></td>
  </tr>
</template>

<script>
export default {
  name: 'channel-stats-list-item',
  props:{
    statistic:{
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped>
    td {
      @apply px-6 py-3;
    }
</style>
