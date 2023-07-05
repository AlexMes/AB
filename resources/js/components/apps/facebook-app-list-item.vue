<template>
  <tr class="w-full bg-white p-4">
    <td class="px-6 py-3">
      <p
        class="font-medium py-1.5 text-gray-700 truncate"
        v-text="application.name"
      ></p>
      <span
        class="text-gray-600 text-xs mt-1"
        v-text="application.domain"
      ></span>
    </td>
    <td class="px-6 py-3">
      <button
        class="mr-2"
        @click="forget"
      >
        <fa-icon
          :icon="cacheIcon"
          class="items-center text-gray-600 fill-curren hover:text-teal-600 cursor-pointer"
        ></fa-icon>
        Сбросить кеш
      </button>
      <router-link :to="{name:'facebook.apps.edit', params:{id:application.id}}">
        <fa-icon
          :icon="editIcon"
          class="items-center text-gray-600 fill-curren hover:text-teal-600 cursor-pointer"
        ></fa-icon>
      </router-link>
    </td>
  </tr>
</template>

<script>
import {faSync, faEdit} from '@fortawesome/pro-regular-svg-icons';

export default {
  name:'facebook-app-list-item',
  props:{
    app:{
      type: Object,
      required: true,
    },
  },
  data:() => ({
    application:{},
  }),
  computed:{
    cacheIcon: () => faSync,
    editIcon: () => faEdit,
  },
  created() {
    this.application = {...this.app};
  },
  methods:{
    forget(){
      axios.delete(`/api/facebook/apps/${this.application.id}/cache`)
        .then(() => this.$toast.success({title:'OK', message:'Application cache dropped'}))
        .catch(err => this.$toast.error({title:'Error',message:err.response.data.message}));
    },
  },
};
</script>
