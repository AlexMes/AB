<template>
  <div class="container mx-auto flex flex-col">
    <div
      class="w-full flex flex-col h-auto mb-8 bg-white rounded shadow"
    >
      <div class="flex justify-between border-b px-4 py-3">
        <h2 class="text-gray-700">
          {{ campaign.title }}
        </h2>
        <router-link
          :to="{name:'sms.campaigns.update'}"
          class="button btn-primary"
        >
          Редактировать
        </router-link>
      </div>
      <div class="flex flex-col">
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Лендинг:
          </div>
          <div class="flex w-1/3 text-left">
            <a
              v-if="campaign.landing"
              :href="campaign.landing.url"
              class="font-semibold text-gray-700 hover:text-teal-700"
              v-text="campaign.landing.url"
            ></a>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Тип:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="campaignType"
            ></span>
          </div>
        </div>
        <div
          v-if="campaign.type === 'after_time'"
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Отправлять через Х минут:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="campaign.after_minutes"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Текст:
          </div>
          <div class="flex w-2/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="campaign.text"
            ></span>
          </div>
        </div>

        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Отправленно сообщений:
          </div>
          <div class="flex w-1/3 text-left">
            <span
              class="font-normal text-gray-700"
              v-text="campaign.messages_count"
            ></span>
          </div>
        </div>
        <div
          class="flex border-b p-3 w-full"
        >
          <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
            Статус:
          </div>
          <div class="flex w-1/3 text-left">
            <fa-icon
              :icon="statusIcon"
              class="text-lg mr-2 fill-current"
              :class="{
                'text-green-600' : campaign.status,
                'text-red-600' : !campaign.status,
              }"
            ></fa-icon>
            <span
              class="font-normal text-gray-700"
            >
              {{ campaign.status ? 'Активная' : 'Отключенная' }}
            </span>
          </div>
        </div>
      </div>
    </div>
    <div
      class="w-full bg-white rounded shadow"
    >
      <div class="flex justify-between border-b px-4 py-3">
        <h2 class="text-gray-700">
          Сообщения
        </h2>
      </div>
      <div v-if="hasMessages">
        <table class="table table-auto w-full">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full">
            <tr class="px-3">
              <th>ID</th>
              <th>Телефон</th>
              <th>Текст</th>
              <th>Дата и время</th>
            </tr>
          </thead>
          <tbody>
            <sms-messages-list-item
              v-for="message in messages"
              :key="message.id"
              :message="message"
            >
            </sms-messages-list-item>
          </tbody>
        </table>
      </div>
      <div
        v-else
        class="flex p-6 w-full text-center"
      >
        <span class="text-lg">Ещё нет сообщений</span>
      </div>
    </div>
    <pagination
      :response="response"
      @load="getMessages"
    ></pagination>
  </div>
</template>


<script>
import {faCheckCircle, faTimesCircle } from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'sms-campaign-show',
  props: {
    id: {
      type: [Number,String],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      campaign:{},
      messages: [],
      response: {},
    };
  },
  computed:{
    hasMessages() {
      return this.messages.length > 0;
    },
    campaignType(){
      if(this.campaign.type ==='instant'){
        return 'Мгновенно';
      }

      return 'С задержкой';
    },
    statusIcon(){
      return this.campaign.status
        ? faCheckCircle
        : faTimesCircle;
    },
  },
  created(){
    this.boot();
  },
  methods: {
    boot(){
      this.getCampaign();
      this.getMessages();
    },
    getCampaign() {
      axios
        .get(`/api/sms/campaigns/${this.id}`)
        .then(r => this.campaign = r.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить.', message: e.response.data.message});
        });
    },
    getMessages(page = 1){
      axios
        .get('/api/sms/messages', {
          params: {
            page: page,
            campaign_id: this.id,
          },
        })
        .then(response => {
          this.response = response.data;
          this.messages = response.data.data;
        })
        .catch(err => {
          this.$toast.error({
            title: 'Не удалось загрузить.',
            message: err.response.data.message,
          });
        });
    },
  },
};
</script>

<style>
    th{
        @apply text-left;
        @apply px-2;
        @apply py-3;
    }
    select{
        @apply block;
        @apply appearance-none;
        @apply w-full;
        @apply bg-white;
        @apply border;
        @apply border-gray-400;
        @apply px-3;
        @apply py-2;
        @apply pr-4;
        @apply rounded;
    }
</style>
