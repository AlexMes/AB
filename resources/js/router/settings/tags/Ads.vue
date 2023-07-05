<template>
  <div class="flex flex-col rounded p-4">
    <div class="text-gray-700 w-full">
      <h2>Креативы</h2>
    </div>

    <div class="flex my-4">
      <div class="flex w-1/2 pr-2">
        <div class="flex flex-1">
          <input
            v-model="search"
            type="search"
            class="w-full border-b border-grey-500 bg-transparent pb-1 text-grey-700 outline-none"
            placeholder="Поиск креативов"
            maxlength="50"
          />
        </div>
      </div>

      <div
        class="flex flex-col w-1/2 pl-2"
      >
        <label class="mb-2">Добавить</label>
        <mutiselect
          :show-labels="false"
          :multiple="false"
          :options="available"
          :clear-on-select="false"
          :preserve-search="true"
          placeholder="Выберите креатив, что бы добавить"
          track-by="id"
          label="name"
          @select="addAd"
          @search-change="getAll"
        ></mutiselect>
      </div>
    </div>

    <div
      v-if="hasAds"
      class="flex flex-col w-full bg-white shadow"
    >
      <tags-sub-list-item
        v-for="ad in ads"
        :id="id"
        :key="ad.id"
        :ad="ad"
        @deleted="remove"
      ></tags-sub-list-item>
    </div>
    <div
      v-else
      class="text-center p-4"
    >
      <h2>Креативов не найдено</h2>
    </div>

    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import TagsSubListItem from '../../../components/settings/tags-ad-list-item';

export default {
  name: 'tags-ads',
  components: {TagsSubListItem},
  props: {
    id: {
      type: [String, Number],
      required: true,
    },
  },

  data() {
    return {
      ads: [],
      allCurrentIds: [],
      response: {},
      allAds:[],
      search: '',
    };
  },

  computed: {
    hasAds() {
      return !! this.ads  && this.ads.length > 0;
    },
    available() {
      return this.allAds.filter(ad => this.allCurrentIds.indexOf(ad.id) === -1);
    },
  },

  watch: {
    search() {
      this.load();
    },
  },

  created() {
    this.load();
    this.load('all');
    this.getAll();
  },

  methods: {
    load(page = null) {
      let params = {
        page,
        search: this.search === '' ? null : this.search,
      };
      axios.get(`/api/tags/${this.id}/ads`, {params})
        .then(({data}) => {
          if (page === 'all') {
            this.allCurrentIds = data.map(ad => ad.id);
            return;
          }
          this.ads = data.data;
          this.response = data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить креативы данного подхода.', message: e.response.data.message}));
    },

    add(id) {
      axios.post(`/api/tags/${this.id}/ads`, {id})
        .then(({data}) => {
          this.load();
          this.allCurrentIds.push(id);
          this.$toast.success('Креатив успешно добавлен.');
        })
        .catch(e => this.$toast.error({title: 'Не удалось добавить креатив.', message: e.response.data.message}));
    },

    addAd(selected) {
      this.add(selected.id);
    },

    remove(ad) {
      this.load();

      const index = this.allCurrentIds.findIndex(id => id === ad.id);
      if (index !== -1) this.allCurrentIds.splice(index, 1);

      this.$toast.success('Креатив успешно удален.');
    },

    getAll(search = null) {
      axios.get('/api/ads', {params: {search}})
        .then(r => this.allAds = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить креативы.', message: e.response.data.message}));
    },
  },
};
</script>

<style scoped>

</style>
