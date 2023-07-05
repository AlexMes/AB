<template>
  <div class="flex w-full px-3 py-3 items-center border-b">
    <div class="w-2/5 flex justify-between">
      <div
        class="font-semibold mr-4 text-gray-700"
        v-text="`# ${access.id}`"
      ></div>
      <div>
        <span
          class="text-sm mr-2"
          v-text="access.received_at"
        ></span>
        <a
          :href="access.facebook_url"
          target="_blank"
          rel="noreferrer noopener"
          v-text="access.name || 'Профиль'"
        ></a>
      </div>
      <span class="mx-2">
        {{ accountp }}
        <fa-icon
          :icon="['far','eye']"
          class="fill-current mx-2 text-gray-600 cursor-pointer hover:text-teal-700"
          fixed-width
          @click="toggleAP"
        ></fa-icon>
        <fa-icon
          :icon="['far','copy']"
          class="fill-current mr-2 text-gray-600 cursor-pointer hover:text-teal-700"
          fixed-width
          @click="copyAP"
        ></fa-icon>
      </span>
    </div>

    <div class="w-2/5 flex">
      <span
        class="w-1/3"
        v-text="accessType"
      ></span>
      <span
        class="w-1/3"
        v-text="access.supplier.name"
      ></span>
      <router-link
        v-if="access.user"
        class="font-medium hover:text-teal-700"
        :to="{name:'users.show', params:{id:access.user_id}}"
        v-text="access.user.name"
      ></router-link>
      <span
        v-else
        class="text-gray-600"
      >Нет баера</span>
    </div>

    <div class="w-2/5 flex">
      <span
        class="w-1/2"
        v-text="access.email"
      ></span>
      <span>
        {{ emailp }}
        <fa-icon
          :icon="['far','eye']"
          class="fill-current mx-2 text-gray-600 cursor-pointer hover:text-teal-700"
          fixed-width
          @click="toggleEP"
        ></fa-icon>
        <fa-icon
          :icon="['far','copy']"
          class="fill-current mr-2 text-gray-600 cursor-pointer hover:text-teal-700"
          fixed-width
          @click="copyEP"
        ></fa-icon>
      </span>
    </div>
    <div class="flex w-1/6 justify-between">
      <router-link
        class="mx-2"
        :to="{name:'accesses.update', params: {id: access.id}}"
      >
        <fa-icon
          :icon="['far','edit']"
          class="mx-2 fill-current text-gray-700 hover:text-teal-700"
          fixed-width
        ></fa-icon>
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'accesses-list-item',
  props: {
    access: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    ap: null,
    ep: null,
    accessTypes: [
      { id: 'farm', label: 'Фарм' },
      { id: 'brut', label: 'Брут' },
      { id: 'own', label: 'Личный' },
    ],
  }),
  computed: {
    accountp() {
      return this.ap === null ? '***********' : this.ap;
    },
    emailp() {
      return this.ep === null ? '***********' : this.ep;
    },
    accessType() {
      return this.accessTypes.find(type => type.id === this.access.type).label;
    },
  },
  methods: {
    toggleAP() {
      if (this.ap == null) {
        axios
          .get(`/api/accesses/${this.access.id}/ap`)
          .then(response => (this.ap = response.data))
          .catch(err =>
            this.$toast.error({
              title: 'Не удалось показать пароль почты',
              message: error.response.data.message,
            }),
          );
        return;
      }
      this.ap = null;
    },
    copyAP() {
      axios
        .get(`/api/accesses/${this.access.id}/ap`)
        .then(response => this.$clipboard(response.data))
        .catch(err =>
          this.$toast.error({
            title: 'Не удалось показать пароль почты',
            message: error.response.data.message,
          }),
        );
    },
    toggleEP() {
      if (this.ep == null) {
        axios
          .get(`/api/accesses/${this.access.id}/ep`)
          .then(response => (this.ep = response.data))
          .catch(err =>
            this.$toast.error({
              title: 'Не удалось показать пароль почты',
              message: error.response.data.message,
            }),
          );
        return;
      }
      this.ep = null;
    },
    copyEP() {
      axios
        .get(`/api/accesses/${this.access.id}/ep`)
        .then(response => this.$clipboard(response.data))
        .catch(err =>
          this.$toast.error({
            title: 'Не удалось показать пароль почты',
            message: error.response.data.message,
          }),
        );
    },
  },
};
</script>

