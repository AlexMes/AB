<template>
  <div class="flex w-full bg-white p-4 flex items-center border-b">
    <div
      class="px-4 text-lg text-justify text-gray-500"
      v-text="team.id"
    ></div>
    <div class="flex flex-col justify-center">
      <router-link
        :to="{name:'teams.users', params: {id: team.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="team.name"
      ></router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'team-list-item',
  props:{
    team:{
      required:true,
      type:Object,
    },
  },
};
</script>
