<template>
  <tr
    class="w-full bg-white items-center border-b px-3 hover:bg-gray-200"
  >
    <td class="px-2 py-3 pl-5">
      <router-link
        :to="{name:'sms.campaigns.show', params:{id:campaign.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold"
        v-text="`# ${campaign.id}`"
      >
      </router-link>
    </td>
    <td>
      <span
        v-if="!isEditing"
        v-text="campaign.title"
      ></span>
      <div v-else>
        <div class="max-w-lg rounded-md shadow-sm">
          <input
            id="title"
            v-model="localCampaign.title"
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
    </td>
    <td>
      <router-link
        v-if="campaign.landing"
        :to="{name:'domains.show',params:{id:campaign.landing.id}}"
        v-text="campaign.landing.url"
      ></router-link>
      <span v-else> - </span>
    </td>
    <td>
      <span
        v-if="!isEditing"
        v-text="campaignType"
      ></span>
      <div v-else>
        <div class="max-w-xs rounded-md shadow-sm">
          <select
            id="type"
            v-model="localCampaign.type"
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
    </td>
    <td>
      <span
        v-if="!isEditing"
        v-text="campaignBranch"
      ></span>
      <div v-else>
        <div class="max-w-xs rounded-md shadow-sm">
          <select
            id="branch_id"
            v-model="localCampaign.branch_id"
            class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
            @change="errors.clear('branch_id')"
          >
            <option :value="null">
              Не выбран
            </option>
            <option
              v-for="branch in branches"
              :key="`branch-${branch.id}`"
              :value="branch.id"
              v-text="branch.name"
            ></option>
          </select>
        </div>
        <span
          v-if="errors.has('branch_id')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('branch_id')"
        ></span>
      </div>
    </td>
    <td
      v-text="campaign.messages_count"
    >
    </td>
    <td>
      <div v-if="!isEditing">
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
      <div v-else>
        <div class="max-w-lg">
          <toggle v-model="localCampaign.status"></toggle>
        </div>
        <span
          v-if="errors.has('status')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('status')"
        ></span>
      </div>
    </td>
    <td v-if="inlineEditing">
      <span
        v-if="!isEditing"
        v-text="campaign.text"
      ></span>
      <div v-else>
        <div class="max-w-lg rounded-md shadow-sm">
          <input
            v-model="localCampaign.text"
            type="text"
            placeholder=""
            class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
            required
            @input="errors.clear('text')"
          />
        </div>
        <span
          v-if="errors.has('text')"
          class="block text-red-600 text-sm mt-1"
          v-text="errors.get('text')"
        ></span>
      </div>
    </td>
    <td v-if="inlineEditing">
      <span
        v-if="!isEditing"
        v-text="campaign.after_minutes"
      ></span>
      <div v-else-if="localCampaign.type === 'delayed'">
        <div class="max-w-lg rounded-md shadow-sm">
          <input
            v-model="localCampaign.after_minutes"
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
    </td>
    <td v-if="inlineEditing">
      <fa-icon
        v-if="!isEditing"
        :icon="['far', 'pencil-alt']"
        class="fill-current text-gray-700 cursor-pointer hover:text-teal-700"
        fixed-width
        @click="toggleEditing"
      ></fa-icon>
      <fa-icon
        v-if="isEditing"
        :icon="['far', 'check-circle']"
        class="fill-current text-gray-700 cursor-pointer hover:text-teal-700"
        fixed-width
        @click="save"
      ></fa-icon>
      <fa-icon
        v-if="isEditing"
        :icon="['far', 'times-circle']"
        class="fill-current text-gray-700 cursor-pointer hover:text-red-700"
        fixed-width
        @click="cancel"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>
import ErrorBag from '../../utilities/ErrorBag';
import {faCheckCircle, faTimesCircle } from '@fortawesome/pro-regular-svg-icons';

export default {
  name:'sms-campaigns-list-item',
  props:{
    campaign:{
      required:true,
      type:Object,
    },
    inlineEditing: {
      type: Boolean,
      required: false,
      default: false,
    },
    branches: {
      type: Array,
      required: true,
    },
  },
  data: () => {
    return {
      localCampaign: {},
      isEditing: false,
      types:[
        {id:'instant', name: 'Сразу'},
        {id:'delayed', name: 'Отложенная'},
      ],
      errors: new ErrorBag(),
    };
  },
  computed:{
    campaignType() {
      return this.campaign.type === 'instant' ? 'Мгновенно':'С задержкой';
    },
    statusIcon() {
      return this.campaign.status
        ? faCheckCircle
        : faTimesCircle;
    },
    campaignBranch() {
      return !!this.campaign.branch ? this.campaign.branch.name : '-';
    },
  },
  methods: {
    toggleEditing() {
      this.resetForm();
      this.isEditing = !this.isEditing;
    },
    save() {
      this.isBusy = true;
      axios
        .put(`/api/domains/${this.campaign.landing_id}/sms-campaigns/${this.campaign.id}`, this.localCampaign)
        .then(response => {
          this.$toast.success({title: 'Success', message: 'Campaign has been updated.'});
          this.$emit('updated', {campaign: response.data});
          this.toggleEditing();
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
    cancel() {
      this.toggleEditing();
    },
    resetForm() {
      this.localCampaign = {
        title: this.campaign.title,
        text: this.campaign.text,
        type: this.campaign.type,
        after_minutes: this.campaign.after_minutes,
        status: this.campaign.status,
        branch_id: this.campaign.branch_id,
      };

      this.errors = new ErrorBag();
    },
  },
};
</script>

<style>
  td{
      @apply py-4;
      @apply px-2;
      @apply align-middle;
  }
</style>
