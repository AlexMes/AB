<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Добавление статистики
      </h1>
    </div>
    <div class="w-full rounded flex flex-col">
      <form class="flex flex-col">
        <div class="flex w-full mb-6">
          <div class="flex flex-col">
            <label class="mb-2">Дата</label>
            <date-picker
              id="statDate"
              v-model="date"
              class="w-full px-1 py-2 border rounded text-gray-600"
              :config="pickerConfig"
              placeholder="Выберите дату"
            ></date-picker>
          </div>
        </div>
        <div
          v-if="hasChannels"
          class="bg-white rounded shadow"
        >
          <div class="flex flex-col items-center border-b">
            <table class="table table-auto w-full">
              <thead>
                <tr>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
                  >
                    channel
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
                  >
                    Date
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
                  >
                    Cost
                  </th>
                  <th
                    class="px-6 py-3 border-b border-gray-200 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider"
                  >
                    Impressions
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="channel in channels"
                  :key="channel.id"
                >
                  <td
                    class="px-6 py-3 "
                    v-text="channel.name"
                  ></td>
                  <td
                    class="px-6 py-3 "
                    v-text="date"
                  ></td>
                  <td class="px-3">
                    <input
                      v-model="channel.cost"
                      class="border-b px-3 py-2"
                      type="number"
                      placeholder="0.00"
                    />
                  </td>
                  <td class="px-3">
                    <input
                      v-model="channel.impressions"
                      class="border-b px-3 py-2"
                      type="number"
                      placeholder="0"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="w-full flex justify-end p-4">
              <button
                type="submit"
                class="button btn-primary mx-3"
                @click.prevent="save"
              >
                Сохранить
              </button>
              <button
                class="button btn-secondary mx-3"
                @click="$router.go(-1)"
              >
                Отмена
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'create-stats',
  components: {
    DatePicker,
  },
  data: () => ({
    date: moment().format('YYYY-MM-DD'),
    channels: [],
    pickerConfig: {
      defaultDate: moment().format('YYYY-MM-DD'),
      maxDate: moment().format('YYYY-MM-DD'),
    },
  }),
  computed: {
    hasChannels() {
      return this.channels.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      axios
        .get('/api/telegram/channels', { params: { all: true } })
        .then(response => {
          this.channels = response.data;
        })
        .catch(error =>
          this.$toast.error({
            title: 'Error',
            message: 'Cant load channels',
          }),
        );
    },
    save() {
      this.isBusy = true;
      axios
        .post('/api/telegram/statistics-bulk', { date:this.date, channels:this.channels } )
        .then(() => {
          this.$toast.success({ title: 'OK', message: 'Saved' });
          this.$router.push({name:'telegram.stats.index'});
        })
        .catch(() =>
          this.$toast.error({
            title: 'Error',
            message: 'Failed to save',
          }),
        )
        .finally(() => (this.isBusy = false));
    },
  },
};
</script>

<style scoped></style>
