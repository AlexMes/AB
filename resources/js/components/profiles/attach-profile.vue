<template>
  <div
    v-if="hasApps"
    class="flex relative"
  >
    <div class="flex rounded overflow-hidden">
      <a
        v-if="$root.user.role != 'verifier'"
        class="button btn-primary text-white"
        :href="`/facebook/connect?app_id=${primary.id}`"
        target="_blank"
        rel="noopener"
      > Прикрепить ({{ primary.name }}) </a>
        <a
            v-else
            class="button btn-primary text-white"
            :href="`/facebook/connect?app_id=${primary.id}`"
            target="_blank"
            rel="noopener"
        > Прикрепить </a>
      <button
        v-if="shouldShowOtherApps"
        class="w-12 h-full border-l border-white relative button btn-primary text-white flex items-center focus:outline-none"
        @click.stop="open = !open"
      >
        <svg
          class="h-5 w-5 fill-current"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
        >
          <path
            d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"
          />
        </svg>
      </button>
    </div>
    <transition
      enter-class="opacity-0 scale-90"
      enter-active-class="ease-out transition-fastest"
      enter-to-class="opacity-100 scale-100"
      leave-class="opacity-100 scale-100"
      leave-active-class="ease-in transition-fastest"
      leave-to-class="opacity-0 scale-90"
    >
      <div
        v-if="open"
        class="origin-top-right absolute right-0 flex justify-center mt-2 w-64 bg-white rounded-lg border shadow-md py-2"
        style="top: 100%"
        @click="open = !open"
      >
        <ul>
          <li
            v-for="app in others"
            :key="app.id"
          >
            <a
                v-if="$root.user.role != 'verifier'"
              :href="`/facebook/connect?app_id=${app.id}`"
              target="_blank"
              rel="noopener"
            > Прикрепить ({{ app.name }}) </a>
              <a
                  v-else
                  :href="`/facebook/connect?app_id=${app.id}`"
                  target="_blank"
                  rel="noopener"
              > Прикрепить </a>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'attach-profile',
  data: () => ({
    apps: [],
    open: false,
    defaultIndex: 0,
    errors: new ErrorBag(),
  }),
  computed: {
    hasApps() {
      return this.activeApps.length > 0;
    },
    shouldShowOtherApps() {
      return this.activeApps.length > 1;
    },
    activeApps() {
      return this.apps.filter(app => app.order !== null);
    },
    primary() {
      return this.activeApps[this.defaultIndex];
    },
    others() {
      return this.activeApps.filter((app, index) => index !== this.defaultIndex);
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(){
      axios
        .get('/api/facebook/apps', {params:{all: true}})
        .then(r => {
          this.apps = r.data;
        })
        .catch(e => this.$toast.error({title: 'Не удалось загрузить приложения', message:this.errors.fromResponse(e).getMessage()}));
    },
  },
};
</script>

<style scoped>
    .button {
        @apply font-medium;
        @apply text-center;
        @apply px-3 py-2;
        @apply shadow;
        @apply cursor-pointer;
        border-radius: 0;
    }
</style>
