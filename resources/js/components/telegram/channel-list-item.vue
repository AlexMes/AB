<template>
  <div class="bg-white border-b p-4 flex items-center justify-between">
    <span
      class="w-1/4"
      v-text="channel.name"
    ></span>
    <span
      v-if="channel.subject"
      class="w-1/4"
      v-text="channel.subject.name"
    ></span>
    <span
      v-else
      class="w-1/4"
    > Без тематики</span>
    <button
      @click.prevent="$modal.show('channels-form', { channel: channel })"
    >
      <fa-icon
        :icon="['far','pencil-alt']"
        class="fill-current mx-2"
        fixed-width
      ></fa-icon>
    </button>
  </div>
</template>

<script>
export default {
  name: 'channel-list-item',
  props: {
    channel: {
      type: Object,
      required: true,
    },
  },
};
</script>
