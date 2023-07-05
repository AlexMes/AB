<template>
  <div class="flex w-full bg-white p-3 flex items-center border-b">
    <div
      class="w-1/12"
      v-text="manager.id"
    ></div>
    <div
      class="w-3/12 px-4 text-sm text-justify text-gray-700"
      v-text="manager.name"
    >
    </div>
    <div
      class="w-3/12 px-4 text-sm text-justify text-gray-700"
      v-text="manager.email"
    ></div>
    <div class="flex justify-end w-5/12 px-4">
      <a
        v-if="manager.spreadsheet_id"
        target="_blank"
        rel="noopener"
        :href="
          `https://docs.google.com/spreadsheets/d/${manager.spreadsheet_id}`
        "
      ><fa-icon
        :icon="sheetLinkIcon"
        fixed-width
      ></fa-icon></a>
      <router-link
        :to="{name: 'managers.update', params: {id: manager.id}}"
        class="cursor-pointer"
      >
        <fa-icon
          :icon="['far', 'pencil-alt']"
          class="fill-current text-gray-700 hover:text-teal-700"
          fixed-width
        ></fa-icon>
      </router-link>
      <fa-icon
        :icon="officeIcon"
        class="fill-current cursor-pointer text-gray-700 hover:text-teal-700"
        fixed-width
        @click="$modal.show('change-manager-office-modal', {manager: manager})"
      ></fa-icon>
      <fa-icon
        :icon="['far', 'times-circle']"
        class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
        fixed-width
        @click="$modal.show('delete-office-manager-modal', {manager: manager})"
      ></fa-icon>
    </div>
  </div>
</template>

<script>

import {faFileExcel} from '@fortawesome/pro-regular-svg-icons';
import {faBuilding} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'manager-list-item',
  props: {
    manager: {
      type: Object,
      required: true,
    },
  },
  computed: {
    sheetLinkIcon() {
      return faFileExcel;
    },
    officeIcon() {
      return faBuilding;
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
