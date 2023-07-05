<template>
  <tr class="w-full bg-white p-4">
    <td class="px-6 py-3">
      <p
        class="font-medium py-1.5 text-gray-700 truncate"
        v-text="application.name"
      ></p>
      <span
        class="text-gray-600 text-xs mt-1"
        v-text="application.market_id"
      ></span>
    </td>
    <td
      class="px-6 py-3"
    >
      <toggle
        v-model="application.enabled"
        @input="toggle"
      ></toggle>
    </td>
    <td
      class="px-6 py-3 text-sm leading-5 text-gray-700 truncate"
      v-text="application.url"
    ></td>
    <td class="px-6 py-3">
      <router-link :to="{name:'apps.edit', params:{id:application.id}}">
        <fa-icon
          :icon="icon"
          class="items-center text-gray-600 fill-curren hover:text-teal-600 cursor-pointer"
        ></fa-icon>
      </router-link>
    </td>
  </tr>
</template>

<script>
import {faEdit} from '@fortawesome/pro-regular-svg-icons';

export default {
  name:'apps-list-item',
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
    icon: () => faEdit,
  },
  created() {
    this.application = {...this.app};
    this.listen();
  },
  beforeDestroy() {
    Echo.leave(`App.GoogleApp.${this.app.id}`);
  },
  methods:{
    listen(){
      Echo.private(`App.GoogleApp.${this.app.id}`)
        .listen('App.Events.GoogleAppUpdated', event => this.application = event.app);
    },
    toggle(state){
      state === true ? this.enable() : this.disable();
    },
    enable(){
      axios.post(`/api/apps/${this.application.id}/status`)
        .then(() => this.$toast.success({title:'OK', message:'Application enabled'}))
        .catch(err => this.$toast.error({title:'Error',message:err.response.data.message}));
    },
    disable(){
      axios.delete(`/api/apps/${this.application.id}/status`)
        .then(() => this.$toast.success({title:'OK', message:'Application disabled'}))
        .catch(err => this.$toast.error({title:'Error',message:err.response.data.message}));
    },
  },
};
</script>
