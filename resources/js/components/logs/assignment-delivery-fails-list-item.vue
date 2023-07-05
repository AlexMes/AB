<template>
  <div>
    <div class="w-full bg-white p-4 border-b flex">
      <div class="flex flex-col justify-center w-1/12 text-left pr-2">
        <span
          class="text-gray-700"
          v-text="assignment.id"
        ></span>
      </div>
      <div
        class="w-2/12 flex flex-col"
      >
        <span
          v-if="assignment.route.manager_id"
          v-text="assignment.route.manager.name"
        ></span>
        <span v-else>-</span>
      </div>
      <div
        class="w-3/12 flex flex-col"
      >
        <span
          v-if="assignment.destination_id"
          v-text="assignment.destination.name"
        ></span>
        <span v-else>-</span>
      </div>
      <div class="flex justify-between items-center w-5/12">
        <span
          v-text="assignment.delivery_failed"
        ></span>
      </div>
      <div class="flex justify-end w-1/12">
        <fa-icon
          :icon="['far', 'sync']"
          class="text-gray-500 fill-current hover:text-teal-700 cursor-pointer"
          :spin="isBusy"
          @click="resend"
        >
        </fa-icon>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'assignment-delivery-fails-list-item',
  props:{
    assignment:{
      type:Object,
      required:true,
    },
  },
  data: () => {
    return {
      isBusy: false,
    };
  },
  methods: {
    resend() {
      this.isBusy = true;
      axios.post(`/api/resend-assignment-delivery-fail/${this.assignment.id}`)
        .then(response => this.$toast.success({title: 'Ok', message: 'Resending has been started.'}))
        .catch(err => this.$toast.error({title: 'Unable to resend assignment.', message: err.response.data.message}))
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
