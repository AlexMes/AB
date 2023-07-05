<template>
  <div class="container mx-auto">
    <div class="w-full h-auto bg-white rounded shadow mb-8">
      <div
        v-if="result"
        class="w-full h-auto mb-8"
      >
        <div class="flex justify-between border-b px-4 py-3">
          <h2 class="text-gray-700">
            Результат #{{ result.id }}
          </h2>
          <router-link
            :to="{name: 'results.update'}"
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
              Дата:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.date"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Оффер:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                v-if="result.offer"
                class="font-normal text-gray-700"
                v-text="result.offer.name"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Офис:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                v-if="result.office"
                class="font-normal text-gray-700"
                v-text="result.office.name"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Количество лидов:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.leads_count"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Количество без ответа:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.no_answer_count"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Количество отказов:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.reject_count"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Количество неправильных ответов:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.wrong_answer_count"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Количество демо:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.demo_count"
              ></span>
            </div>
          </div>
          <div
            class="flex border-b p-3 w-full"
          >
            <div class="flex w-1/3 text-left font-semibold text-gray-800 pl-5">
              Количество ftd:
            </div>
            <div class="flex w-1/3 text-left">
              <span
                class="font-normal text-gray-700"
                v-text="result.ftd_count"
              ></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'results-show',
  props: {
    id: {
      type: [String,Number],
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      result:{},
    };
  },
  created(){
    this.boot();
  },
  methods: {
    boot(){
      axios.get(`/api/results/${this.id}`)
        .then(r => this.result = r.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить результат.', message: e.response.data.message});
        });
    },
  },
};
</script>
