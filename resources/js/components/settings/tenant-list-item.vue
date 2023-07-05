<template>
  <tr>
    <td v-text="tenant.id">
    </td>
    <td
      class="font-semibold"
      v-text="tenant.name"
    ></td>
    <td
      v-text="tenant.key"
    ></td>
    <td
      v-text="tenant.url"
    ></td>
    <td>
      <router-link
        :to="{name: 'tenants.show', params: {id: tenant.key}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
      >
        <fa-icon
          :icon="icon"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
    </td>
  </tr>
</template>

<script>
import {faEdit} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'tenant-list-item',
  props:{
    tenant:{
      required:true,
      type:Object,
    },
  },
  computed:{
    icon: () => faEdit,
  },
};
</script>

<style scoped>
td{
    @apply px-6;
    @apply py-4;
    @apply whitespace-no-wrap;
    @apply text-sm;
    @apply leading-5;
    @apply text-gray-500;
}
</style>
