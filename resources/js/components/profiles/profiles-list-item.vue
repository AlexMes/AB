<template>
  <div
    class="flex flex-col items-center w-full p-4 tracking-normal bg-white border-b"
  >
    <div class="flex items-center justify-between w-full">
      <div class="flex items-center w-1/3">
        <router-link
          class="mr-3 text-lg font-medium text-gray-700 hover:text-teal-700"
          :to="{
            name: 'profile.general',
            params: { id: profile.id }
          }"
          v-text="profile.name"
        >
        </router-link>
        <span
          v-if="profile.app && $root.user.role != 'verifier'"
          class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 border border-gray-700 text-gray-700"
          v-text="profile.app.name"
        ></span>
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
                v-if="$root.user.role != 'verifier'"
              :to="{
                name: 'users.show',
                params: { id: profile.user_id }
              }"
              class="font-semibold text-gray-700 hover:text-teal-700"
              v-text="profile.user.name"
            ></router-link>
              <span
              v-else
              class="font-semibold text-gray-700"
              v-text="profile.user.name"
              ></span>
          </div>
        </div>
      </div>
      <div class="flex w-1/3">
        <span v-if="profile.has_issues">
          <fa-icon
            :icon="['far', 'exclamation-circle']"
            class="mr-2 text-red-700 fill-current"
            fixed-width
          ></fa-icon>
          <span
            class="text-xs"
            v-text="profile.last_issue"
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
        <fa-icon
          v-if="$root.user.role === 'verifier'"
          :icon="['far', 'times-circle']"
          class="text-gray-500 ml-1 fill-current hover:text-teal-700 cursor-pointer"
          :spin="isBusy"
          @click="destroy"
        >
        </fa-icon>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'profiles-list-item',
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
    Echo.leaveChannel(`App.Profile.${this.profile.id}`);
  },
  created() {
    Echo.private(`App.Profile.${this.profile.id}`).listen(
      '.Updated',
      event => this.$emit('updated', event),
    );
  },
  methods: {
    runSync() {
      this.isBusy = true;
      axios
        .post(`/api/profiles/${this.profile.id}/sync`)
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

    destroy() {
      axios.delete(`/api/profiles/${this.profile.id}`)
        .then(response => {
          this.$toast.success({title: 'OK', message: 'Profile has been deleted successfully.'});
        })
        .catch(error => this.$toast.error({title: 'Failed', message: error.response.data.message}));
    },
  },
};
</script>
