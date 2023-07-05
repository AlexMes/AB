<template>
  <modal
    height="auto"
    name="channels-form"
    @before-open="getChannel"
  >
    <div class="m-4 flex flex-col">
      <form
        class="mt-3"
        @submit.prevent="save"
      >
        <div class="flex flex-col">
          <label
            class="flex w-full text-gray-700 font-medium"
          >Название</label>
          <input
            id="channelName"
            v-model="channel.name"
            type="text"
            class="w-full border-b text-gray-700 placeholder-gray-400 my-2 px-1 py-2 "
            placeholder="Новое название канала"
            maxlength="40"
            minlength="3"
            required
            @input="errors.clear('name')"
          />
          <span
            v-if="errors.has('name')"
            class="text-red-600 text-sm mt-1"
            v-text="errors.get('name')"
          ></span>
        </div>
        <div class="flex flex-col mt-3">
          <label
            class="flex w-full text-gray-700 font-medium"
          >Тематика
          </label>
          <select
            v-model="channel.subject_id"
            class="w-full border-b text-gray-700 placeholder-gray-400 my-2 px-1 py-2 "
          >
            <option :value="null">
              Без тематики
            </option>
            <option
              v-for="subject in subjects"
              :key="subject.id"
              :value="subject.id"
              v-text="subject.name"
            ></option>
          </select>
          <span
            v-if="errors.has('subject_id')"
            class="text-red-600 text-sm mt-1"
            v-text="errors.get('name')"
          ></span>
        </div>
        <div class="w-full flex justify-end mt-5">
          <button
            type="submit"
            class="button btn-primary mx-2"
            @click.prevent="save"
          >
            Сохранить
          </button>
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click.prevent="flush"
          >
            Отмена
          </button>
        </div>
      </form>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'channel-form-modal',
  data: () => ({
    channel: {
      name: null,
      subject_id: null,
    },
    errors: new ErrorBag(),
    subjects: [],
  }),
  computed: {
    isUpdating() {
      return this.channel.id;
    },
  },
  created() {
    axios
      .get('/api/telegram/subjects')
      .then(response => (this.subjects = response.data));
  },
  methods: {
    save() {
      this.isUpdating ? this.update() : this.create();
    },
    getChannel(event) {
      try {
        this.channel = event.params.channel;
      }catch (e) {
        // do nothing
      }
    },
    create() {
      axios
        .post('/api/telegram/channels', this.channel)
        .then(response => {
          this.flush();
          this.$toast.success({
            title: 'OK',
            message: 'Канал добавлен',
          });
        })
        .catch(error => this.errors.fromResponse(error));
    },
    update() {
      axios
        .put(`/api/telegram/channels/${this.channel.id}`, this.channel)
        .then(response => {
          this.flush();
          this.$toast.success({
            title: 'OK',
            message: 'Канал обновлен',
          });
        })
        .catch(error => this.errors.fromResponse(error));
    },
    flush() {
      this.channel = {
        name: null,
        subject_id: null,
      };
      this.$modal.hide('channels-form');
    },
  },
};
</script>
