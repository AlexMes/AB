<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td class="pr-2 py-3 pl-5">
      <input
        v-if="selectable"
        type="checkbox"
        :value="domain.id"
        @change="$emit('toggle-selected', {domain_id: domain.id})"
      />
      <router-link
        :to="{name:'domains.show', params:{id:domain.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="`# ${domain.id}`"
      >
      </router-link>
    </td>
    <td v-text="domain.effectiveDate"></td>
    <td>
      <a
        target="_blank"
        :href="domain.url"
        class="w-full block text-gray-700 hover:text-teal-700"
        rel="noreferrer noopener"
        v-text="domain.url"
      ></a>
      <span
        class="text-xs text-gray-600 mt-3"
        v-text="linkType"
      ></span>
    </td>
    <td
      v-if="domain.user && ! editing"
      class="flex items-center h-auto border-b-0"
    >
      <div class="mr-3">
        <img
          :src="`https://eu.ui-avatars.com/api/?name=${domain.user.name}&background=2C7A7B&color=F7FAFC`"
          alt="AdsBoard avatar"
          class="rounded-full h-8 w-8"
        />
      </div>
      <div>
        <router-link
          :to="{name:'users.show',params:{id:domain.user_id}}"
          class="text-gray-700 hover:text-teal-700 font-semibold"
          v-text="domain.user.name"
        ></router-link>
      </div>
    </td>
    <td v-else>
      <div v-if="editing && domain.can_update && (isAdmin || isTeamLead)">
        <select v-model="domain.user_id">
          <option
            v-for="user in users"
            :key="user.id"
            :value="user.id"
            v-text="user.name"
          ></option>
        </select>
      </div>
    </td>
    <td
      v-if="domain.offer"
      :class="{ 'hidden':compact }"
      v-text="domain.offer.name"
    ></td>
    <td
      v-else
      :class="{ 'hidden':compact }"
    >
      -
    </td>
    <td
      v-if="domain.sp"
      :class="{ 'hidden':compact }"
      v-text="domain.sp.name"
    ></td>
    <td
      v-else
      :class="{ 'hidden':compact }"
    >
      -
    </td>
    <td
      v-if="domain.bp"
      :class="{ 'hidden':compact }"
      v-text="domain.bp.name"
    ></td>
    <td
      v-else
      :class="{ 'hidden':compact }"
    >
      -
    </td>
    <td
      v-if="domain.land"
      :class="{ 'hidden':compact }"
      v-text="domain.land.url"
    ></td>
    <td
      v-else
      :class="{ 'hidden':compact }"
    >
      -
    </td>
    <td>
      <span
        class="text-green-500"
        v-text="domain.passed_ads_count"
      ></span>
      <span>/</span>
      <span
        class="text-red-500"
        v-text="domain.rejected_ads_count"
      ></span>
    </td>
    <td v-if="compact">
      <fa-icon
        v-if="domain.can_update && ! inlineEditing"
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
  name:'domain-list-item',
  props:{
    domain:{
      required:true,
      type:Object,
    },
    selectable: {
      required: false,
      type: Boolean,
      default: false,
    },
    compact:{
      type:Boolean,
      required:false,
      default:false,
    },
  },
  data:() => ({
    inlineEditing:false,
    users:[],
  }),
  computed:{
    isAdmin() {
      return this.$root.user && this.$root.user.role === 'admin';
    },
    isTeamLead() {
      return this.$root.user && this.$root.user.role === 'teamlead';
    },
    linkType(){
      if(this.domain.linkType === 'landing') {
        return 'Лендинг';
      }
      if(this.domain.linkType === 'prelanding') {
        return 'Прелендинг';
      }
      if(this.domain.linkType === 'service') {
        return 'Сервис';
      }
      return this.domain.status;
    },
    editing(){
      return this.domain.can_update && this.inlineEditing;
    },
  },
  methods:{
    toggle(){
      if(!this.inlineEditing){
        axios.get('/api/users',{params:{all:true, teammates: this.isTeamLead}})
          .then(r => this.users = r.data);
      }
      this.inlineEditing =! this.inlineEditing;
    },
    save(){
      axios.put(`/api/domains/${this.domain.id}`,this.domain)
        .then(r => { this.$emit('domain-updated', {domain: r.data}); this.toggle();})
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
