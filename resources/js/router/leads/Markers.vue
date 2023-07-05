<template>
  <div class="">
    <div class="mb-4 border-b">
      <h2 class="text-gray-700 mb-4">
        Метки
      </h2>
    </div>

    <div
      v-if="hasMarkers"
    >
      <div class="shadow">
        <marker-list-item
          v-for="marker in markers"
          :key="marker.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :marker="marker"
          @deleted="remove"
        ></marker-list-item>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Меток не найдено</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'leads-markers',
  props: {
    leadId: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => {
    return {
      markers: [],
      response: {},
    };
  },
  computed: {
    hasMarkers() {
      return this.markers.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios.get(`/api/leads/${this.leadId}/markers`, {params: {page}})
        .then(response => {
          this.markers = response.data.data;
          this.response = response.data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load lead markers.', message: err.response.data.message}));
    },
    remove(event) {
      const index = this.markers.findIndex(market => market.id === event.marker.id);
      if (index !== -1) {
        this.markers.splice(index, 1);
      }
    },
  },
};
</script>
