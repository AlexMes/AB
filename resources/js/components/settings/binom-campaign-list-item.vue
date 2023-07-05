<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td class="pr-2 py-3 pl-5">
      {{ campaign.id }}
    </td>
    <td class="pr-2 py-3 pl-5">
      <b v-text="campaign.campaign_id"></b>
    </td>
    <td class="pr-2 py-3 pl-5">
      <b v-text="campaign.name"></b>
    </td>
    <td
      v-if="campaign.offer && ! editing"
      class="flex items-center h-auto border-b-0"
    >
      <div>
        <router-link
          :to="{name:'offers.allowed-users',params:{id:campaign.offer_id}}"
          class="text-gray-700 hover:text-teal-700 font-semibold"
          v-text="campaign.offer.name"
        ></router-link>
      </div>
    </td>
    <td v-else>
      <div v-if="editing && (isAdmin || isDeveloper)">
        <select v-model="campaign.offer_id">
          <option
            v-for="offer in offers"
            :key="offer.id"
            :value="offer.id"
            v-text="offer.name"
          ></option>
        </select>
      </div>
    </td>
    <td
      v-if="campaign.binom"
      v-text="campaign.binom.name"
    ></td>
    <td v-else>
      -
    </td>
    <td>
      <fa-icon
        v-if="(isAdmin || isDeveloper) && ! inlineEditing"
        :icon="['far','pencil-alt']"
        class="text-gray-700 hover:text-teal-700 font-semibold cursor-pointer"
        fixed-width
        @click="toggle"
      ></fa-icon>
      <fa-icon
        v-if="editing"
        :icon="['far','check']"
        class="text-gray-700 hover:text-teal-700 font-semibold cursor-pointer"
        fixed-width
        @click="save"
      ></fa-icon>
      <fa-icon
        v-if="editing"
        :icon="['far','times-circle']"
        class="ml-2 text-gray-700 hover:text-teal-700 font-semibold cursor-pointer"
        fixed-width
        @click="toggle"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>
export default {
  name:'binom-campaign-list-item',
  props:{
    campaign:{
      required:true,
      type:Object,
    },
  },
  data:() => ({
    inlineEditing:false,
    offers:[],
  }),
  computed:{
    isAdmin() {
      return this.$root.user && this.$root.user.role === 'admin';
    },
    isDeveloper() {
      return this.$root.user && this.$root.user.role === 'developer';
    },
    editing(){
      return (this.isAdmin || this.isDeveloper) && this.inlineEditing;
    },
  },
  methods:{
    toggle(){
      if(!this.inlineEditing){
        axios.get('/api/offers',{params:{all:true}})
          .then(r => this.offers = r.data);
      }
      this.inlineEditing =! this.inlineEditing;
    },
    save(){
      axios.put(`/api/binom/campaigns/${this.campaign.id}`,this.campaign)
        .then(r => { this.$emit('binom-campaign-updated', {campaign: r.data}); this.toggle();})
        .catch(err => this.$toast.error({title:'Error',message:err.response.data.message}));
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
