<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td v-text="payload.id"></td>
    <td v-text="payload.created_at"></td>
    <td
      v-if="payload.offer"
      v-text="payload.offer.name"
    ></td>
    <td
      v-else
    >
      -
    </td>
    <td
      v-if="payload.landing"
      v-text="payload.landing.url"
    ></td>
    <td
      v-else
    >
      -
    </td>
    <td v-if="payload.lead">
      <router-link
        :to="{name:'leads.show', params:{id:payload.lead_id}}"
        class="w-1/6 flex"
        v-text="`#${payload.lead_id}`"
      ></router-link>
      <span
        v-if="payload.lead"
        class="block"
        v-text="payload.lead.phone"
      ></span>
    </td>
    <td
      v-else
    >
      -
    </td>
    <td v-text="payload.external_lead_id"></td>
    <td v-text="payload.status"></td>
  </tr>
</template>

<script>
export default {
  name:'payloads-list-item',
  props:{
    payload:{
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
