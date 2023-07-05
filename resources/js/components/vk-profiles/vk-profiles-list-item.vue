<template>
  <div
    class="flex flex-col items-center w-full p-4 tracking-normal bg-white border-b"
  >
    <div class="flex items-center justify-between w-full">
      <div class="flex items-center w-1/3">
        <router-link
          class="mr-3 text-lg font-medium text-gray-700 hover:text-teal-700"
          :to="{
            name: 'vk-profiles.general',
            params: { id: profile.id }
          }"
          v-text="profile.name"
        >
        </router-link>
      </div>
      <div class="flex w-1/5">
        <div
          v-if="profile.user"
          class="flex items-center"
        >
          <div class="mr-3">
            <img
              :src="
                `https://eu.ui-avatars.com/api/?name=${profile.user.name}&background=2C7A7B&color=F7FAFC`
              "
              alt="AdsBoard avatar"
              class="w-8 h-8 rounded-full"
            />
          </div>
          <div class="w-1/5">
            <router-link
              :to="{
                name: 'users.show',
                params: { id: profile.user_id }
              }"
              class="font-semibold text-gray-700 hover:text-teal-700"
              v-text="profile.user.name"
            ></router-link>
          </div>
        </div>
      </div>
      <div class="flex w-1/3">
        <span v-if="profile.issues_info">
          <fa-icon
            :icon="['far', 'exclamation-circle']"
            class="mr-2 text-red-700 fill-current"
            fixed-width
          ></fa-icon>
          <span
            class="text-xs"
            v-text="profile.issues_info"
          ></span>
        </span>
      </div>
      <div class="w-1/12 mx-2 text-right">
        <fa-icon
          :icon="['far', 'sync']"
          class="text-gray-500 fill-current hover:text-teal-700 cursor-pointer"
          :spin="isBusy"
          @click="runSync"
        >
        </fa-icon>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'vk-profiles-list-item',
  props: {
    profile: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    isBusy: false,
  }),
  beforeDestroy() {
    Echo.leaveChannel(`App.VK.Models.Profile.${this.profile.id}`);
  },
  created() {
    Echo.private(`App.VK.Models.Profile.${this.profile.id}`).listen(
      '.Updated',
      event => this.$emit('updated', event),
    );
  },
  methods: {
    runSync() {
      alert('Not ready yet...');
      return;
      this.isBusy = true;
      axios
        .post(`/api/vk-profiles/${this.profile.id}/sync`)
        .then(response =>
          this.$toast.success({
            title: 'Синхронизация запланирована',
            message: 'whatever',
          }),
        )
        .catch(error =>
          this.$toast.error({
            title: 'Не удалось запустить синхронизацию',
            message: error.statusText,
          }),
        )
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>
