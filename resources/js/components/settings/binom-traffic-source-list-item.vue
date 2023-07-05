<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td class="pr-2 py-3 pl-5">
      {{ binomTrafficSource.id }}
    </td>
    <td class="pr-2 py-3 pl-5">
      <b v-text="binomTrafficSource.ts_id"></b>
    </td>
    <td class="pr-2 py-3 pl-5">
      <b v-text="binomTrafficSource.name"></b>
    </td>
    <td
      v-if="!editing"
      v-text="innerTSText"
    >
    </td>
    <td v-else>
      <select v-model="binomTrafficSource.traffic_source_id">
        <option :value="null">
          Не выбран
        </option>
        <option
          v-for="trafficSource in innerTrafficSources"
          :key="trafficSource.id"
          :value="trafficSource.id"
          v-text="trafficSource.name"
        ></option>
      </select>
    </td>
    <td>
      <fa-icon
        v-if="(isAdmin || isDeveloper) && !editing"
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
  name:'binom-traffic-source-list-item',
  props:{
    binomTrafficSource:{
      required:true,
      type:Object,
    },
    innerTrafficSources: {
      required: true,
      type: Array,
    },
  },
  data:() => ({
    editing:false,
  }),
  computed:{
    isAdmin() {
      return this.$root.user && this.$root.user.role === 'admin';
    },
    isDeveloper() {
      return this.$root.user && this.$root.user.role === 'developer';
    },
    innerTSText() {
      if (!this.binomTrafficSource.inner_traffic_source) {
        return '';
      }

      return this.binomTrafficSource.inner_traffic_source.name;
    },
  },
  methods:{
    toggle(){
      this.editing = !this.editing;
    },
    save() {
      axios.put(`/api/binom-traffic-sources/${this.binomTrafficSource.id}/traffic-source`, {
        traffic_source_id: this.binomTrafficSource.traffic_source_id,
      })
        .then(r => {
          this.$emit('traffic-source-updated', {binomTrafficSource: r.data});
          this.toggle();
        })
        .catch(err => this.$toast.error({title: 'Не удалось обновить источник трафика.', message: err.response.data.message}));
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
