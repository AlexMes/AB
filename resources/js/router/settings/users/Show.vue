<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <div
        class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-no-wrap"
      >
        <div class="ml-4 mt-4">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <img
                class="h-12 w-12 rounded-full"
                :src="
                  `https://eu.ui-avatars.com/api/?name=${user.name}&background=2C7A7B&color=F7FAFC`
                "
                alt=""
              />
            </div>
            <div class="ml-4">
              <h3
                class="text-lg leading-6 font-medium text-gray-900"
                v-text="user.name"
              ></h3>
              <p class="text-sm leading-5 text-gray-500">
                <span v-text="user.email"></span>
              </p>
            </div>
          </div>
        </div>
        <div class="ml-4 mt-4 flex-shrink-0 flex">
          <span class="inline-flex rounded-md shadow-sm">
            <router-link
              :to="{ name: 'users.update', params: { id: id } }"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            >
              <fa-icon
                :icon="editIcon"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Редактировать
              </span>
            </router-link>
          </span>

          <span class="ml-3 inline-flex rounded-md shadow-sm">
            <button
              type="button"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
              @click="remove"
            >
              <fa-icon
                :icon="banIcon"
                class="-ml-1 mr-2 h-5 w-5 text-gray-400"
                fixed-width
              ></fa-icon>
              <span>
                Заблокировать
              </span>
            </button>
          </span>
        </div>
      </div>
    </div>
    <div class="bg-white shadow px-4 border-b border-gray-200 sm:px-6 overflow-x-scroll">
      <div>
        <div>
          <nav class="-mb-px flex">
            <router-link
              v-if="$root.user.role !== 'developer'"
              :to="{
                name: 'users.profiles',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Профили
            </router-link>
            <router-link
              v-if="$root.user.role !== 'developer'"
              :to="{
                name: 'users.accounts',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Кабинеты
            </router-link>
            <router-link
              :to="{
                name: 'users.deposits',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Депозиты
            </router-link>
            <router-link
              :to="{
                name: 'users.leads',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Лиды
            </router-link>
            <router-link
              v-if="$root.user.role !== 'developer'"
              :to="{
                name: 'users.accesses',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Доступы
            </router-link>
            <router-link
              v-if="$root.user.role !== 'developer'"
              :to="{
                name: 'users.pages',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Страницы
            </router-link>
            <router-link
              :to="{
                name: 'users.firewall',
                params: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              IP доступы
            </router-link>
            <router-link
              v-if="
                $root.user.role === 'admin' ||
                  $root.user.role === 'developer'
              "
              :to="{
                name: 'users.allowed-offers',
                props: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Оферы
            </router-link>
            <router-link
              v-if="
                $root.user.role === 'admin' ||
                  $root.user.role === 'developer'
              "
              :to="{
                name: 'users.allowed-branches',
                props: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Филиалы
            </router-link>
            <router-link
              v-if="
                $root.user.role === 'admin' ||
                  $root.user.role === 'head'
              "
              :to="{
                name: 'users.shared-users',
                props: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Общие лиды
            </router-link>
            <router-link
              :to="{
                name: 'users.settings',
                props: { id: id }
              }"
              active-class="border-teal-500 text-teal-600 focus:text-teal-800 focus:border-teal-700"
              class="whitespace-no-wrap mr-8 py-4 px-1 border-b-2 border-transparent font-medium text-sm leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300"
            >
              Настройки
            </router-link>
          </nav>
        </div>
      </div>
    </div>
    <div class="">
      <router-view></router-view>
    </div>
    <div class="my-6"></div>
    <!--    <user-telegram-notifications-->
    <!--      :id="id"-->
    <!--      :notifications="user.denied_telegram_notifications"-->
    <!--      @add="addNotification"-->
    <!--      @remove="removeNotification"-->
    <!--    ></user-telegram-notifications>-->
    <!--    <div class="w-full h-auto bg-white rounded shadow mb-8">-->
    <!--      <h2 class="text-gray-700 border-b p-3">-->
    <!--        Профили-->
    <!--      </h2>-->
    <!--      <div v-if="hasProfiles">-->

    <!--      </div>-->
    <!--      <div-->
    <!--        v-else-->
    <!--        class="p-4 flex text-center text-gray-700"-->
    <!--      >-->
    <!--        Нет разрешенных профилей-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    <div class="w-full h-auto bg-white rounded shadow mb-8">-->
    <!--      <h2 class="text-gray-700 border-b p-3">-->
    <!--        Аккаунты-->
    <!--      </h2>-->
    <!--      <div v-if="hasAccounts">-->
    <!--        <accounts-list-item-->
    <!--          v-for="account in accounts"-->
    <!--          :key="account.id"-->
    <!--          :account="account"-->
    <!--        ></accounts-list-item>-->
    <!--      </div>-->
    <!--      <div-->
    <!--        v-else-->
    <!--        class="p-4 flex text-center text-gray-700"-->
    <!--      >-->
    <!--        Нет аккаунтов-->
    <!--      </div>-->
    <!--&lt;!&ndash;      <account-comment-modal></account-comment-modal>&ndash;&gt;-->
    <!--    </div>-->
  </div>
</template>

<script>
import { set } from 'vue';
import UserTelegramNotifications from '../../../components/users/user-telegram-notifications';
import { faPencilAlt, faLock } from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'users-show',
  components: { UserTelegramNotifications },
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      user: {},
      accounts: [],
      profiles: [],
      editIcon: faPencilAlt,
      banIcon: faLock,
    };
  },
  computed: {
    hasAccounts() {
      return this.accounts.length > 0;
    },
    hasProfiles() {
      return this.profiles.length > 0;
    },
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      axios
        .get(`/api/users/${this.id}`)
        .then(r => (this.user = r.data))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить баера.',
            message: e.response.data.message,
          }),
        );
      /*axios
        .get(`/api/users/${this.id}/profiles`)
        .then(r => (this.profiles = r.data))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить профили.',
            message: e.response.data.message,
          }),
        );
      axios
        .get(`/api/users/${this.id}/accounts`)
        .then(r => (this.accounts = r.data.data))
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить аккаунты.',
            message: e.response.data.message,
          }),
        );*/
    },

    addNotification(notification) {
      this.user.denied_telegram_notifications.push(notification);
    },
    removeNotification(id) {
      const index = this.user.denied_telegram_notifications.findIndex(
        notification => notification.id === id,
      );
      if (index !== -1) {
        this.user.denied_telegram_notifications.splice(index, 1);
      }
    },
    remove() {
      if (confirm('Уверены, что хотите удалить пользователя?')) {
        axios.delete(`/api/users/${this.id}`)
          .then(r => this.$router.push({name:'users.index'}))
          .catch(err => this.$toast.error({title: 'Не удалось заблокировать пользователя.', message: err.response.data.message}));
      }
    },
  },
};
</script>
