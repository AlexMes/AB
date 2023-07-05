<template>
  <div>
    <div class="flex p-5 bg-white shadow text-gray-700 flex-wrap">
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Полное имя</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.fullname"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>UTM Source</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.utm_source"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Дата регистрации</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="date"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>UTM Content</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.utm_content"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Номер телефона</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.phone"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>UTM Campaign</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.utm_campaign"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Email</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.email"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>UTM Term</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.utm_term"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Домен</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.domain"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>UTM Medium</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.utm_medium"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>IP</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.ip"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Оффер</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="lead.offer"
            v-text="lead.offer.name"
          ></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Click ID</strong>
        </div>
        <div class="flex w-3/4">
          <span v-text="lead.clickid"></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Адсет</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="lead.adset"
            v-text="lead.adset.name"
          ></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Аккаунт</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="lead.account"
            v-text="lead.account.name"
          ></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Кампания</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="lead.campaign"
            v-text="lead.campaign.name"
          ></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Баер</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="lead.user"
            v-text="lead.user.name"
          ></span>
        </div>
      </div>
      <div class="w-1/2 p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Affiliate</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="lead.affiliate"
            v-text="lead.affiliate.name"
          ></span>
        </div>
      </div>
    </div>
    <div class="flex space-x-2">
      <div class="flex flex-col w-1/2 bg-white p-4 mt-8">
        <div class="mb-4 border-b">
          <h2 class="text-gray-700 mb-4">
            События лида
          </h2>
        </div>
        <div
          v-if="hasEvents"
          class="relative"
        >
          <div
            class="border-r-2 border-gray-500 absolute h-full top-1"
            style="left: 15px"
          ></div>
          <ul class="list-none m-0 p-0">
            <li
              v-for="event in events"
              :key="event.id"
              class="mb-4"
            >
              <div class="flex items-center mb-1">
                <div class="bg-gray-500 rounded-full h-8 w-8"></div>
                <div
                  class="flex flex-1 ml-4 font-medium"
                >
                  <div class="flex flex-col">
                    <span v-text="dateFormat(event.created_at)"></span>
                    <span
                      class="mt-1 text-sm"
                      v-text="timeFormat(event.created_at)"
                    ></span>
                  </div>
                  <span
                    class="ml-4 text-blue-500"
                    v-text="event.type"
                  ></span>
                  <div class="ml-auto">
                    <span v-if="event.auth_id">
                      <span
                        class="font-normal text-sm"
                        v-text="` (${event.auth_type}:${event.auth_id})`"
                      ></span>
                      <span
                        class="font-bold"
                        v-text="event.auth.name"
                      ></span>
                    </span>
                    <span
                      v-else
                      class="font-bold"
                    >System</span>
                  </div>
                </div>
              </div>
              <div class="flex flex-col ml-12">
                <div
                  v-for="(data, field) in event.changes"
                  :key="field"
                  class="flex py-1 border-b ml-3"
                >
                  <div class="w-2/12">
                    <span v-text="`${field}:`"></span>
                  </div>
                  <div class="w-5/12">
                    <span
                      class="line-through"
                      v-text="data.original"
                    ></span>
                  </div>
                  <div
                    class="w-5/12"
                    v-text="data.changed"
                  ></div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div v-else>
          Событий не найдено
        </div>
      </div>
      <leads-markers
        class="flex flex-col w-1/2 bg-white p-4 mt-8"
        :lead-id="id"
      ></leads-markers>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import LeadsMarkers from './Markers';
export default {
  name: 'show',
  components: {LeadsMarkers},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    lead: {},
    events: [],
  }),
  computed: {
    hasLead() {
      return !!this.lead.id;
    },
    date() {
      return moment(this.lead.created_at).format('YYYY-MM-DD HH:mm');
    },
    hasEvents() {
      return this.events.length > 0;
    },
  },
  created() {
    this.load();
    this.loadEvents();
  },
  methods: {
    load() {
      axios
        .get(`/api/leads/${this.id}`)
        .then(response => (this.lead = response.data))
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить лид.',
            message: err.response.data.message,
          });
        });
    },
    loadEvents() {
      axios.get(`/api/leads/${this.id}/events`)
        .then(response => this.events = response.data)
        .catch(err => this.$toast.error({title: 'Unable to load lead events.', message: err.response.data.message}));
    },
    dateFormat(date) {
      return moment(date).format('YYYY-MM-DD');
    },
    timeFormat(date) {
      return moment(date).format('HH:mm:ss');
    },
  },
};
</script>
