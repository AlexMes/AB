<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <div
        class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap"
      >
        <div class="ml-4 mt-4">
          <div class="flex flex-col justify-center">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
            >
              <span
                v-if="domain.dawn_since"
                class="w-4 h-4 inline-block bg-red-500 rounded-full"
              ></span>
              <span
                v-else
                class="w-4 h-4 inline-block bg-green-500 rounded-full"
              ></span>
              {{ domain.url }}
            </h3>
            <div class="text-sm leading-6 font-medium text-gray-400">
              Passed: <span
                class="text-green-500"
                v-text="domain.passed_ads_count"
              ></span> / Rejected: <span
                class="text-red-500"
                v-text="domain.rejected_ads_count"
              ></span>
            </div>
          </div>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0 flex">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{ name: 'domains.update', params: { id: id } }"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            >
              <fa-icon
                :icon="['far', 'pencil-alt']"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Редактировать
              </span>
            </router-link>
          </span>
        </div>
      </div>
    </div>

    <div class="bg-white shadow px-4 py-5 pb-2 border-b border-gray-200 sm:px-6">
      <div class="flex flex-wrap text-sm leading-6 font-medium text-gray-400">
        <div
          v-if="domain.date"
          class="w-1/4 mb-4"
        >
          <span class="text-gray-700">Дата:</span>
          <span
            class="font-normal"
            v-text="domain.effectiveDate"
          ></span>
        </div>
        <div
          v-if="domain.safe_page"
          class="w-1/4 mb-4 "
        >
          <span class="text-gray-700">СП:</span>
          <span
            class="font-normal"
            v-text="domain.safe_page"
          ></span>
        </div>
        <div
          v-if="domain.user"
          class="w-1/4 mb-4"
        >
          <span class="text-gray-700">Баер:</span>
          <span
            class="font-normal"
            v-text="domain.user.name"
          ></span>
        </div>
        <div
          v-if="domain.order"
          class="w-1/4 mb-4"
        >
          <span class="text-gray-700">Заказ:</span>
          <span
            class="font-normal"
            v-text="`#${domain.order.id}`"
          ></span>
        </div>
        <div class="w-1/4 mb-4">
          <span class="text-gray-700">Статус:</span>
          <span
            v-if="domain.status == 'development'"
            class="font-normal"
          >В работе</span>
          <span
            v-else-if="domain.status == 'scheduled'"
            class="font-normal"
          >Запланирован</span>
          <span
            v-else-if="domain.status == 'ready'"
            class="font-normal"
          >Готов</span>
          <span
            v-else-if="domain.status == 'worked_out'"
            class="font-normal"
          >Отработан</span>
        </div>
        <div
          v-if="domain.linkType"
          class="w-1/4 mb-4"
        >
          <span class="text-gray-700">Тип сайта:</span>
          <span
            v-if="domain.linkType == 'landing'"
            class="font-normal"
          >Ленд</span>
          <span
            v-else-if="domain.linkType == 'prelanding'"
            class="font-normal"
          >Преленд</span>
          <span
            v-else-if="domain.linkType == 'service'"
            class="font-normal"
          >Сервис</span>
        </div>
        <div class="w-1/4 mb-4">
          <span class="text-gray-700">Статус прохождения:</span>
          <span
            v-if="domain.reach_status === null"
            class="font-normal"
          >Не выбран</span>
          <span
            v-else-if="domain.reach_status == 'passed'"
            class="font-normal"
          >Прошел</span>
          <span
            v-else-if="domain.reach_status == 'missed'"
            class="font-normal"
          >Не прошел</span>
          <span
            v-else-if="domain.reach_status == 'banned'"
            class="font-normal"
          >Бан</span>
        </div>
        <div class="w-1/4 mb-4">
          <span class="text-gray-700">Разрешить дубли </span>
          <span
            v-if="domain.allow_duplicates"
            class="w-4 h-4 -mb-1 inline-block bg-green-500 rounded-full"
          ></span>
          <span
            v-else
            class="w-4 h-4 -mb-1 inline-block bg-red-500 rounded-full"
          ></span>
        </div>
      </div>
    </div>

    <div class="bg-white shadow px-4 border-b border-gray-200 sm:px-6">
      <div>
        <div>
          <nav class="-mb-px flex">
            <router-link
              :to="{
                name: 'domains.ads',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Объявления
            </router-link>
            <router-link
              :to="{
                name: 'domains.comments',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap ml-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Комментарии
            </router-link>
            <router-link
              :to="{
                name: 'domains.sms-campaigns',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap ml-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              СМС кампании
            </router-link>
          </nav>
        </div>
      </div>
    </div>
    <div class="">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: 'domains-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      domain: {},
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get(`/api/domains/${this.id}`)
        .then(r => (this.domain = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить домен.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
