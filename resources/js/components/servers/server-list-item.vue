<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td
      class="pl-5"
      v-text="`# ${server.id}`"
    ></td>
    <td>
      <router-link
        :to="{name:'servers.show', params:{id:server.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="server.name"
      >
      </router-link>
    </td>
    <td v-text="server.provider"></td>
    <td v-text="server.ip_address"></td>
    <td v-text="server.credential_id"></td>
    <td v-text="server.domains_count"></td>
  </tr>
</template>

<script>
export default {
  name:'server-list-item',
  props:{
    server:{
      required:true,
      type:Object,
    },
  },
};
</script>
<style>
    td{
        @apply py-4;
        @apply px-2;
    }
</style>
