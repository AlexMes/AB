<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="office.id"
    ></div>
    <div class="flex flex-col justify-center">
      <router-link
        :to="path"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="office.name"
      ></router-link>
      <!--      <div class="text-xs leading-6 font-medium text-gray-400">
        CPL: {{ office.cpl ? office.cpl : '-' }} / CPA: {{ office.cpa ? office.cpa : '-' }}
      </div>-->
    </div>
  </div>
</template>

<script>
export default {
  name: 'office-list-item',
  props:{
    office:{
      required:true,
      type:Object,
    },
  },
  computed: {
    path() {
      if (this.$root.user.role === 'support') {
        return {name:'offices.managers', params: {id: this.office.id}};
      }

      return {name:'offices.users', params: {id: this.office.id}};
    },
  },
};
</script>
