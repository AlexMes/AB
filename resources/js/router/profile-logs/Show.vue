<template>
  <div class="container mx-auto">
    <div class="w-full h-auto bg-white rounded shadow mb-8">
      <div
        v-if="profileLog"
        class="w-full h-auto mb-8"
      >
        <div
          class="px-4 py-2 flex flex-row justify-end border-b items-center"
        >
          <router-link
            :to="{ name: 'profile-logs.update', params: { id: id } }"
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
        </div>
        <div
          class="h-auto justify-between items-end py-6 flex flex-row"
        >
          <div class="block align-text-bottom font-bold px-8">
            <div class="mb-4 text-gray-700">
              Профиль:
              <span
                v-if="profileLog.profile"
                class="font-normal"
                v-text="profileLog.profile.name"
              ></span>
            </div>
            <div class="mb-4 text-gray-700">
              Ссылка:
              <a
                target="_blank"
                :href="profileLog.link"
                class="text-gray-700 hover:text-teal-700"
                rel="noreferrer noopener"
                v-text="profileLog.link"
              ></a>
            </div>
            <div class="mb-4 text-gray-700">
              Длительность запуска:
              <span
                class="font-normal"
                v-text="profileLog.duration"
              ></span>
            </div>
            <div class="mb-4 text-gray-700">
              Миниатюра:
              <span
                class="font-normal"
                v-text="profileLog.miniature"
              ></span>
            </div>
            <div class="mb-4 text-gray-700">
              Креатив:
              <span
                class="font-normal"
                v-text="profileLog.creative"
              ></span>
            </div>
            <div class="mb-4 text-gray-700">
              Текст:
              <span
                class="font-normal"
                v-text="profileLog.text"
              ></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {faPencilAlt} from '@fortawesome/pro-regular-svg-icons';

export default {
  name: 'profile-log-show',
  props: {
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      profileLog: {},
      editIcon: faPencilAlt,
    };
  },
  created() {
    this.boot();
  },
  methods: {
    boot() {
      axios
        .get(`/api/profile-logs/${this.id}`)
        .then(r => (this.profileLog = r.data))
        .catch(e => {
          this.$toast.error({
            title: 'Не удалось загрузить запись.',
            message: e.response.data.message,
          });
        });
    },
  },
};
</script>
