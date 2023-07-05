<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Каналы
      </h1>
      <button
        class="button btn-primary text-white"
        @click="showForm"
      >
        <fa-icon
          :icon="['far', 'plus']"
          class="fill-current mr-2"
        ></fa-icon>
        Добавить
      </button>
    </div>
    <search-field
      class="mb-8"
      @search="search"
    ></search-field>

    <div
      v-if="hasChannels"
      class="w-full shadow rounded"
    >
      <channel-list-item
        v-for="channel in channels"
        :key="channel.id"
        :channel="channel"
      ></channel-list-item>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
    <channel-form></channel-form>
  </div>
</template>

<script>
export default {
  name: 'index-channels',
  data: () => ({
    needle: null,
    response: null,
    channels: [],
  }),
  computed: {
    hasChannels() {
      return this.channels.length > 0;
    },
  },
  watch: {
    needle() {
      this.load();
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios
        .get('/api/telegram/channels', {
          params: { search: this.needle, page: page },
        })
        .then(r => {
          this.response = r.data;
          this.channels = r.data.data;
        });
    },
    search(needle) {
      this.needle = needle;
    },
    showForm() {
      this.$modal.show('channels-form');
    },
  },
};
</script>

<style scoped></style>
