<template>
  <div>
    <h3>Google two-factor auth</h3>
    <form>
      <div
        class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
      >
        <label
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px"
        >
          Secret
        </label>
        <div class="mt-3 sm:mt-1 sm:col-span-2">
          <div class="flex justify-between">
            <span
              class="w-5/6 break-all"
              v-text="data.google_tfa_secret"
            ></span>
            <div class="w-1/6 ml-1 flex justify-end">
              <fa-icon
                :icon="['far', 'copy']"
                class="text-gray-500 fill-current hover:text-teal-700 cursor-pointer"
                @click="$clipboard(data.google_tfa_secret)"
              >
              </fa-icon>
              <fa-icon
                :icon="['far', 'sync']"
                class="ml-1 text-gray-500 fill-current hover:text-teal-700 cursor-pointer"
                @click="generateSecret"
              >
              </fa-icon>
              <fa-icon
                :icon="['far', 'times-circle']"
                class="ml-1 text-gray-500 fill-current hover:text-red-700 cursor-pointer"
                @click="destroy"
              >
              </fa-icon>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="data.google_tfa_secret"
        class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
      >
        <label
          class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px"
        >
          QR code
        </label>
        <div class="sm:col-span-2 -mt-6 sm:-mt-5 -ml-4">
          <div class="max-w-lg">
            <img :src="data.qr_code" />
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'google-tfa',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    data: {},
  }),
  created() {
    axios.get(`/api/users/${this.id}/google-tfa-secret`)
      .then(response => this.data = response.data)
      .catch(err => this.$toast.error({title: 'Unable to load user\'s secret.', message: err.response.data.message}));
  },
  methods: {
    generateSecret() {
      if (confirm('Уверены? Старый код будет неактивен.')) {
        axios.post(`/api/users/${this.id}/google-tfa-secret`)
          .then(response => this.data = response.data)
          .catch(err => this.$toast.error({title: 'Unable to generate user\'s secret.', message: err.response.data.message}));
      }
    },
    destroy() {
      if (confirm('Уверены? Двухфакторная аутентификация будет отключена у данного пользователя.')) {
        axios.delete(`/api/users/${this.id}/google-tfa-secret`)
          .then(response => this.data = {})
          .catch(err => this.$toast.error({title: 'Unable to delete user\'s secret.', message: err.response.data.message}));
      }
    },
  },
};
</script>

<style scoped>

</style>
