<template>
  <div class="flex">
    <span class="w-full flex items-center border-b border-grey-500 bg-transparent text-grey-700">
      <fa-icon
        class="text-gray-600 mr-2 stroke-current"
        :icon="icon"
        fixed-width
      ></fa-icon>
      <input
        id="search"
        type="search"
        maxlength="50"
        class="w-full pr-2 py-2 bg-transparent text-gray-700"
        :placeholder="placeholder"
        @input="emit"
      />
    </span>
  </div>
</template>

<script>
import { faSearch } from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'search-field',
  props: {
    placeholder: {
      type: String,
      default: 'Поиск',
    },
  },
  data: () => ({
    icon: faSearch,
    value: null,
  }),

  methods:{
    emit(event){
      this.$emit('search',event.target.value);
    },
  },
};
</script>

