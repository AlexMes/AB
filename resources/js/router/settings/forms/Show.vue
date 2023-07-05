<template>
  <div class="w-full h-auto mb-8">
    <div class="w-full h-auto bg-white rounded shadow mb-8">
      <div
        v-if="form"
        class="w-full h-auto"
      >
        <div class="px-4 py-2 flex flex-row justify-between border-b items-center">
          <div
            class="text-2xl font-bold p-3 text-gray-700"
            v-text="form.name"
          ></div>
          <div>
            <router-link
              :to="{name:'forms.update'}"
              class="button btn-primary"
            >
              Редактировать
            </router-link>
          </div>
        </div>
      </div>
      <div class="flex">
        <div class="flex flex-col bg-white shadow text-gray-700 flex-1 no-last-border">
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Название</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.name"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>URL</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.url"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Status</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.status ? 'Active' : 'Disabled'"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Provider</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.provider"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Phone prefix</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.phone_prefix"
              ></span>
            </div>
          </div>
        </div>
        <div class="flex flex-col bg-white shadow text-gray-700 flex-1 no-last-border">
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Method</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.method"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>FORM_ID</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.form_id"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Landing</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-if="form.landing"
                v-text="form.landing.url"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Ext. affiliate id</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.external_affiliate_id"
              ></span>
            </div>
          </div>
          <div class="p-3 flex flex-row border-b">
            <div class="flex w-1/4">
              <strong>Ext. offer id</strong>
            </div>
            <div class="flex w-3/4">
              <span
                v-text="form.external_offer_id"
              ></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="hasPayloads"
    >
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-gray-700">
          Лог запросов
        </h1>
      </div>
      <div class="overflow-x-auto overflow-y-hidden flex w-full">
        <table class="w-full table table-auto relative shadow">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr class="px-3">
              <th class="px-2 py-3 pl-5">
                #
              </th>
              <th>Дата</th>
              <th>Offer</th>
              <th>Landing</th>
              <th>Lead</th>
              <th>External ID</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <payloads-list-item
              v-for="payload in payloads.data"
              :key="payload.id"
              :payload="payload"
            ></payloads-list-item>
          </tbody>
        </table>
      </div>
      <pagination
        :response="payloads"
        @load="loadPayloads"
      ></pagination>
    </div>
  </div>
</template>

<script>
export default {
  name: 'forms-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      form: {},
      payloads: {},
    };
  },
  computed:{
    hasPayloads(){
      if (this.payloads.data) {
        return this.payloads.data.length > 0;
      }
      return this.payloads.length > 0;
    },
  },
  created(){
    this.load();
    this.loadPayloads();
  },
  methods: {
    load(){
      axios.get(`/api/integrations/forms/${this.id}`)
        .then(r => this.form = r.data)
        .catch(e => this.$toast.error({title: 'Не удалось загрузить форму.', message: e.response.data.message}));
    },
    loadPayloads(page = 1){
      axios.get('/api/integrations/payloads', {params: {page: page, form_id: this.id}})
        .then(response => {
          this.payloads = response.data;
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить запросы.', message: err.response.data.message});
        });
    },
  },
};
</script>
