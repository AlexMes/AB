<template>
  <div class="w-full bg-white p-4 border-b flex">
    <div class="w-11/12 ml-2 text-left flex items-center">
      <a
        href="#"
        v-text="ad.name"
      ></a>
    </div>

    <div
      class="flex w-1/12 items-center justify-center p-4"
    >
      <fa-icon
        :icon="['far','times-circle']"
        class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
        fixed-width
        @click.prevent="remove"
      ></fa-icon>
    </div>
  </div>
</template>

<script>
export default {
  name: 'tags-ad-list-item',
  props: {
    id: {
      type: [String, Number],
      required: true,
    },
    ad: {
      type: Object,
      required: true,
    },
  },

  methods: {
    remove() {
      axios.delete(`/api/tags/${this.id}/ads/${this.ad.id}`)
        .then(r => {
          this.$emit('deleted', this.ad);
        })
        .catch(e => this.$toast.error(e.response.data.message));
    },
  },
};
</script>

<style scoped>

</style>
