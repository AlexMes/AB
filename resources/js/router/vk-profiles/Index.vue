<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Профили
      </h1>
      <attach-vk-profile></attach-vk-profile>
    </div>
    <search-field
      class="mb-8"
      @search="search"
    ></search-field>
    <div
      v-if="hasProfiles"
      class="shadow"
    >
      <vk-profiles-list-item
        v-for="profile in profiles"
        :key="profile.id"
        :profile="profile"
        @updated="swapProfile"
      >
        <span v-text="profile.name"></span>
        <span v-text="profile.id"></span>
      </vk-profiles-list-item>
    </div>
    <pagination
      :response="response"
      @load="load"
    ></pagination>
  </div>
</template>

<script>
import SearchField from '../../components/SearchField';
import AttachVkProfile from '../../components/vk-profiles/attach-vk-profile';
import VkProfilesListItem from '../../components/vk-profiles/vk-profiles-list-item';
export default {
  name: 'vk-profiles-index',
  components: {VkProfilesListItem, AttachVkProfile, SearchField},
  data: () => ({
    response: {},
    profiles: [],
    needle: null,
  }),
  computed:{
    hasProfiles(){
      return this.profiles.length > 0;
    },
  },
  watch:{
    needle(){
      this.load();
    },
  },
  created() {
    this.load();
    this.listen();
  },
  beforeDestroy() {
    Echo.leave('vk-profiles');
  },
  methods:{
    load(page = 1){
      axios.get('/api/vk-profiles', {params: {search: this.needle, page: page }})
        .then(response => {
          this.response = response.data;
          this.profiles = response.data.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить профили.', message: err.response.data.message});
        });
    },
    search(needle){
      this.needle = needle;
    },
    listen(){
      Echo.private('vk-profiles')
        .listen('.Created',event => {
          this.profiles.unshift(event.profile);
        })
        .listen('.Deleted', event => {
          const index = this.profiles.findIndex(profile => profile.id === event.profile.id);

          if (index !== -1) {
            this.profiles.splice(index, 1);
          }
        });
    },
    swapProfile(event){
      let index = this.profiles.findIndex(profile => profile.id === event.profile.id);
      if (index !== -1) {
        set(this.profiles, index, event.profile);
      }
    },
  },
};
</script>

