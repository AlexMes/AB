<template>
  <div>
    <div class="flex flex-col bg-white shadow text-gray-700 flex-1 no-last-border">
      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Название оффера</strong>
        </div>
        <div class="flex w-2/3">
          <span
            v-if="bundle.offer"
            v-text="bundle.offer.name"
          ></span>
          <span v-else>-</span>
        </div>
        <div
          v-if="bundle.can_update"
          class="flex justify-end w-1/12"
        >
          <router-link
            :to="{name:'bundles.update'}"
          >
            <fa-icon
              :icon="['far','edit']"
              class="ml-4 fill-current text-gray-700 hover:text-teal-700"
              fixed-width
            ></fa-icon>
          </router-link>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Название связки (UTM campaign)</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.utm_campaign"
            v-text="bundle.utm_campaign"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Примеры наименования кампейнов и адсетов</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.examples"
            v-text="bundle.examples"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Гео</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.geo"
            v-text="bundle.geo"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Возраст</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.age"
            v-text="bundle.age"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Пол</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.gender"
            v-text="bundle.gender"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Список интересов</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.interests"
            v-text="bundle.interests"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Устройство</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.device"
            v-text="bundle.device"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Платформа</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.platform"
            v-text="bundle.platform"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Плейсменты</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.placements"
            v-text="placements"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Ссылка на креативы</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.ad"
            v-text="bundle.ad"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Ссылка на преленд</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.prelend_link"
            v-text="bundle.prelend_link"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Ссылка на ленд</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.lend_link"
            v-text="bundle.lend_link"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>UTM source</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.utm_source"
            v-text="bundle.utm_source"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>UTM content</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.utm_content"
            v-text="bundle.utm_content"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>UTM term</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.utm_term"
            v-text="bundle.utm_term"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>UTM medium</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.utm_medium"
            v-text="bundle.utm_medium"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Основной текст</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.text"
            v-text="bundle.text"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Описание</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.description"
            v-text="bundle.description"
          ></span>
          <span v-else>-</span>
        </div>
      </div>

      <div
        class="p-3 flex flex-row border-b"
      >
        <div class="flex w-1/4">
          <strong>Заголовок</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-if="bundle.title"
            v-text="bundle.title"
          ></span>
          <span v-else>-</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'bundle-general',
  props: {
    id: {
      type: [String,Number],
      required: true,
      default: null,
    },
  },
  data() {
    return {
      bundle:{},
    };
  },
  computed: {
    placements() {
      return this.bundle.placements.map(p => p.name).join(', ');
    },
  },
  created(){
    this.boot();
  },
  methods: {
    boot(){
      this.loadBundle();
    },
    loadBundle() {
      axios.get(`/api/bundles/${this.id}`)
        .then(r => this.bundle = r.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить связку.', message: e.response.data.message});
        });
    },
  },
};
</script>
