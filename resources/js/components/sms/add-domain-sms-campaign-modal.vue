<template>
  <modal
    name="add-domain-sms-campaign-modal"
    height="auto"
  >
    <div class="p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">
        Создание смс кампании
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="addCampaign">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="title"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="title"
                v-model="campaign.title"
                type="text"
                placeholder="http://example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('title')"
              />
            </div>
            <span
              v-if="errors.has('title')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('title')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="type"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Тип
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="type"
                v-model="campaign.type"
                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @change="errors.clear('type')"
              >
                <option
                  v-for="type in types"
                  :key="type.id"
                  :value="type.id"
                  v-text="type.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('type')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('type')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="branch"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Филиал
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-xs rounded-md shadow-sm">
              <select
                id="branch"
                v-model="campaign.branch_id"
                class="block w-full transition duration-150 ease-in-out form-select sm:text-sm sm:leading-5"
              >
                <option :value="null">
                  Не выбран
                </option>
                <option
                  v-for="branch in branches"
                  :key="branch.id"
                  :value="branch.id"
                  v-text="branch.name"
                ></option>
              </select>
            </div>
            <span
              v-if="errors.has('branch_id')"
              class="block mt-1 text-sm text-red-600"
              v-text="errors.get('branch_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="text"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Текст сообщения
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <textarea
                id="text"
                v-model="campaign.text"
                type="text"
                rows="3"
                required
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('text')"
              ></textarea>
            </div>
            <span
              v-if="errors.has('text')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('text')"
            ></span>
          </div>
        </div>

        <div
          v-if="campaign.type === 'delayed'"
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="after_minutes"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Отправлять через Х минут
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="after_minutes"
                v-model="campaign.after_minutes"
                type="number"
                placeholder=""
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('after_minutes')"
              />
            </div>
            <span
              v-if="errors.has('after_minutes')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('after_minutes')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Статус
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg">
              <toggle v-model="campaign.status"></toggle>
            </div>
            <span
              v-if="errors.has('status')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('status')"
            ></span>
          </div>
        </div>

        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="$modal.hide('add-domain-sms-campaign-modal')"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="addCampaign"
          >
            <span v-if="isBusy"> <fa-icon
              :icon="['far','spinner']"
              class="fill-current"
              spin
              fixed-width
            ></fa-icon> Сохранение</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </modal>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'add-domain-sms-campaign-modal',
  props: {
    domainId: {
      type: [String, Number],
      required: true,
    },
    branches: {
      type: Array,
      required: true,
    },
  },
  data: () => ({
    isBusy: false,
    campaign: {
      title: null,
      text: null,
      type: 'instant',
      after_minutes: null,
      status: false,
      branch_id: null,
    },
    types:[
      {id:'instant', name: 'Сразу'},
      {id:'delayed', name: 'Отложенная'},
    ],
    errors: new ErrorBag(),
  }),
  methods: {
    addCampaign() {
      this.isBusy = true;
      axios
        .post(`/api/domains/${this.domainId}/sms-campaigns`, this.campaign)
        .then(response => {
          this.$toast.success({title: 'Success', message: 'Campaign has been added.'});
          this.$modal.hide('add-domain-sms-campaign-modal');
          this.$eventHub.$emit('domain-sms-campaign-added', {campaign: response.data});
          this.resetForm();
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors.fromResponse(error);
          }
          this.$toast.error({
            title: 'Error',
            message: 'Cant add campaign now.',
          });
        })
        .finally(() => this.isBusy = false);
    },
    resetForm() {
      this.campaign = {
        title: null,
        text: null,
        type: 'instant',
        after_minutes: null,
        status: false,
        branch_id: null,
      };

      this.errors = new ErrorBag();
    },
  },
};
</script>

