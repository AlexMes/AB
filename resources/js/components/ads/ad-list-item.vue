<template>
  <div class="flex bg-white flex-col border-b p-4">
    <div class="flex flex-row w-full items-center text-left">
      <div
        class="w-1/6 flex"
        v-text="ad.name"
      ></div>
      <div
        class="w-1/6 flex text-sm"
        v-text="ad.account_id"
      ></div>
      <div
        class="w-1/6 flex text-sm"
        v-text="ad.campaign_id"
      ></div>
      <div
        class="w-1/6 flex text-sm"
        v-text="ad.adset_id"
      ></div>
      <div
        class="w-1/6 flex text-sm"
        v-text="ad.effective_status"
      ></div>
      <div class="w-1/6 truncate hover:whitespace-pre-line">
        <span v-if="hasIssues">
          <fa-icon
            :icon="['far' ,'exclamation-circle']"
            class="fill-current text-red-700 mr-2"
            fixed-width
          ></fa-icon>
          <span
            class="text-xs"
            v-text="issue_message"
          ></span>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ad-list-item',
  props:{
    ad:{
      type:Object,
      required:true,
    },
  },
  computed: {
    hasIssues() {
      return this.ad.ad_review_feedback !== null;
    },
    issue_message() {
      if (this.hasIssues && this.ad.ad_review_feedback.global) {
        return Object.entries(this.ad.ad_review_feedback.global).reduce((message, [key, reason]) => {
          // eslint-disable-next-line no-param-reassign
          message = `${key} - ${reason} \n`;
          return message;
        }, '');
      }
      return null;
    },
  },
};
</script>
