<template>
  <div class="flex w-full bg-white p-4 flex justify-between border-b">
    <div class="flex w-full items-center">
      <div
        class="px-4 text-lg text-justify text-gray-500"
        v-text="offer.id"
      ></div>
      <div class="w-1/2">
        <router-link
          :to="{name: 'offers.allowed-users', params: {id: offer.id}}"
          class="text-gray-700 hover:text-teal-700 font-semibold"
          v-text="offer.name"
        ></router-link>
      </div>
      <div
        class="w-1/4"
        v-text="offer.vertical"
      ></div>
    </div>
    <div class="flex">
      <fa-icon
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'play']"
        fixed-width
        @click.prevent="startOrder"
      ></fa-icon>
      <fa-icon
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'pause']"
        fixed-width
        @click.prevent="pauseOrder"
      ></fa-icon>
      <fa-icon
        class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
        :icon="['far', 'stop']"
        fixed-width
        @click.prevent="stopOrder"
      ></fa-icon>
    </div>
  </div>
</template>

<script>
export default {
  name: 'offers-list-item',
  props:{
    offer:{
      required:true,
      type:Object,
    },
  },
  methods: {
    startOrder() {
      axios.post(`/api/offers/${this.offer.id}/start-lead-order-routes`)
        .then(r => this.$toast.success({title: 'Successful', message: 'Offer\'s routes have been started.'}))
        .catch(e => this.$toast.error({title: 'Couldn\'t start offer\'s routes.', message: e.response.data.message}));
    },
    pauseOrder() {
      axios.post(`/api/offers/${this.offer.id}/pause-lead-order-routes`)
        .then(r => this.$toast.success({title: 'Successful', message: 'Offer\'s routes have been paused.'}))
        .catch(e => this.$toast.error({title: 'Couldn\'t pause offer\'s routes.', message: e.response.data.message}));
    },
    stopOrder() {
      axios.post(`/api/offers/${this.offer.id}/stop-lead-order-routes`)
        .then(r => this.$toast.success({title: 'Successful', message: 'Offer\'s routes have been stopped.'}))
        .catch(e => this.$toast.error({title: 'Couldn\'t stop offer\'s routes.', message: e.response.data.message}));
    },
  },
};
</script>
