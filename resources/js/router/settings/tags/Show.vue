<template>
  <div>
    <div class="w-full h-auto bg-white rounded shadow mb-8">
      <div
        v-if="tag"
        class="w-full h-auto mb-8"
      >
        <div class="px-4 py-2 flex flex-row justify-between border-b items-center">
          <div
            class="text-2xl font-bold p-3 text-gray-700"
            v-text="tag.name"
          ></div>
          <div>
            <router-link
              :to="{name:'tags.update'}"
              class="button btn-primary"
            >
              Редактировать
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <tag-ads :id="id"></tag-ads>
  </div>
</template>

<script>
import TagAds from './Ads';

export default {
  name: 'tags-show',

  components: {TagAds},

  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },

  data() {
    return {
      isLoading: false,
      tag: {},
    };
  },

  created(){
    this.load();
  },

  methods: {
    load() {
      axios.get(`/api/tags/${this.id}`)
        .then(r => this.tag = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить подход.', message: e.response.data.message}));
    },
  },
};
</script>
