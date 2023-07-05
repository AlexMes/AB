<template>
  <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8 max-w-7xl mx-auto">
    <div
      v-if="server"
      class="px-4 py-5 border-b border-gray-200 flex justify-between items-center sm:px-6"
    >
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        <span># {{ server.id }} {{ server.name }}</span>
      </h3>
    </div>
    <div
      v-if="server"
      class="px-4 py-5 sm:p-0"
    >
      <dl>
        <div
          class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-gray-200 sm:px-6 sm:py-5"
        >
          <dt class="text-sm leading-5 font-medium text-gray-500">
            Поставщик
          </dt>
          <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
            <span class="inline-block">{{ server.provider }}</span>
          </dd>
        </div>
        <div
          class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
        >
          <dt class="text-sm leading-5 font-medium text-gray-500">
            IP Address
          </dt>
          <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
            <span class="inline-block">{{ server.ip_address }}</span>
          </dd>
        </div>
        <div
          class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
        >
          <dt class="text-sm leading-5 font-medium text-gray-500">
            Регион
          </dt>
          <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
            <span class="inline-block">{{ server.region }}</span>
          </dd>
        </div>
        <div
          class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
        >
          <dt class="text-sm leading-5 font-medium text-gray-500">
            Аккаунт
          </dt>
          <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
            <span class="inline-block">{{ server.credential_id }}</span>
          </dd>
        </div>
        <div
          class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5"
        >
          <dt class="text-sm leading-5 font-medium text-gray-500">
            Статус
          </dt>
          <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
            <span
              v-if="server.is_ready"
              class="inline-block"
            >Готов</span>
            <span
              v-else
              class="inline-block"
            >Не готов</span>
          </dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<script>
export default {
  name: 'server-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      server: null,
    };
  },
  beforeRouteEnter(from, to, next) {
    next(vm => vm.load());
  },
  methods: {
    load() {
      axios
        .get(`/api/servers/${this.id}`)
        .then(r => (this.server = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить сервер.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
