<template>
  <div class="mb-6">
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div>
          <dl class="px-4 py-5 pb-4">
            <dt
              class="text-sm leading-5 font-medium text-gray-500 truncate"
            >
              Заказано лидов
            </dt>
            <dd
              class="mt-1 text-3xl leading-9 font-semibold text-gray-900"
              v-text="ordered"
            ></dd>
          </dl>
          <ul class="text-sm text-gray-700">
            <li
              v-for="(progress, index) in progresses"
              :key="index"
              class="border-b border-px p-2 flex justify-between"
            >
              <span v-text="progress.name"></span>
              <span
                v-if="progress.resell_ordered"
                v-text="progress.resell_ordered.toLocaleString()"
              ></span>
              <span v-else>0</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div>
          <dl class="px-4 py-5 pb-4">
            <dt
              class="text-sm leading-5 font-medium text-gray-500 truncate"
            >
              Выдано лидов
            </dt>
            <dd
              class="mt-1 text-3xl leading-9 font-semibold text-gray-900"
              v-text="received"
            ></dd>
          </dl>
          <ul class="text-sm text-gray-700">
            <li
              is="router-link"
              v-for="(progress, index) in progresses"
              :key="index"
              class="border-b border-px p-2 flex justify-between"
              :to="{
                name: 'leads.index',
                params: { offer_id: progress.id, resell_received: progress.resell_received > 0/*, date: date*/ }
              }"
            >
              <span v-text="progress.name"></span>
              <span
                v-if="progress.resell_received"
                v-text="progress.resell_received.toLocaleString() || 0"
              ></span>
              <span v-else>0</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'resell-batches-stats-progress',
  props: {
    /*date:{
      type:Object,
      default: ()=>({
        since: moment().format('YYYY-MM-DD'),
        until: moment().format('YYYY-MM-DD'),
      }),
    },*/
  },
  data: ()=>({
    progresses: null,
    isBusy: false,
  }),
  computed: {
    hasProgresses() {
      return this.progresses !== null;
    },
    ordered() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.resell_ordered || 0)
          .reduce((a, b) => parseInt(a) + parseInt(b), 0);
      }
      return 0;
    },
    received() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.resell_received || 0)
          .reduce((a, b) => parseInt(a) + parseInt(b), 0);
      }
      return 0;
    },
  },
  /*watch: {
    date() {
      this.load();
    },
  },*/
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get('/api/resell-batches/progress-stats', {
          /*params: { date: this.date },*/
        })
        .then(({ data }) => (this.progresses = data))
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Failed to load progress',
          }),
        );
    },
  },
};
</script>
