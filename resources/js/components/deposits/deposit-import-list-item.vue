<template>
  <tr class="text-sm">
    <td class="px-2 py-3">
      <span v-text="deposit.date"></span>
    </td>
    <td class="px-2 py-3">
      <span
        v-if="deposit.office"
        v-text="deposit.office"
      ></span>
      <span v-else>-</span>
    </td>
    <td class="px-2 py-3">
      <span v-text="deposit.phone_number"></span>
    </td>
    <td class="px-2 py-3">
      <span v-text="deposit.click_id"></span>
    </td>
    <td class="px-2 py-3">
      <span v-text="`$ ${deposit.sum}`"></span>
    </td>
  </tr>
</template>

<script>
export default {
  name:'deposit-import-list-item',
  props:{
    deposit:{
      required:true,
      type:Object,
    },
  },
};
</script>
