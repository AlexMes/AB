<template>
  <tr
    class="w-full items-center border-b px-3 hover:bg-gray-100 bg-white"
  >
    <td class="px-2 py-3 pl-5">
      <router-link
        :to="{name:'bundles.general', params:{id:bundle.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="`# ${bundle.id}`"
      >
      </router-link>
    </td>

    <td
      v-if="bundle.title"
      v-text="bundle.title"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.offer"
      v-text="bundle.offer.name"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.utm_campaign"
      v-text="bundle.utm_campaign"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.device"
      v-text="bundle.device"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.platform"
      v-text="bundle.platform"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.placements"
      v-text="placements"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.ad"
      v-text="bundle.ad"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.prelend_link"
      v-text="bundle.prelend_link"
    >
    </td>
    <td v-else>
      -
    </td>

    <td
      v-if="bundle.lend_link"
      v-text="bundle.lend_link"
    >
    </td>
    <td v-else>
      -
    </td>
  </tr>
</template>

<script>

export default {
  name: 'bundle-list-item',
  props: {
    bundle: {
      type: Object,
      required: true,
    },
  },
  computed: {
    placements() {
      return this.bundle.placements.map(p => p.name).join(', ');
    },
  },
};
</script>

<style scoped>
    td{
        @apply py-4;
        @apply px-2;
        @apply text-gray-800;
        @apply text-base;
    }
</style>
