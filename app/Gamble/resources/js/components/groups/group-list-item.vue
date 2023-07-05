<template>
  <tr class="hover:bg-gray-100">
    <td class="cursor-pointer px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'groups.show', params: {id: group.id}}"
        class="text-sm font-thin"
        v-text="`#${group.id}`"
      ></router-link>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <router-link
        :to="{name: 'groups.show', params: {id: group.id}}"
        class="font-normal"
        v-text="group.name"
      ></router-link>
    </td>
  </tr>
</template>

<script>
export default {
  name: 'group-list-item',
  props: {
    group: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style scoped>

</style>
