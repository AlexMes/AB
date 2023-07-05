<template>
  <tr>
    <td v-if="$route.name === 'distribution-rules.index'">
      <span
        v-if="rule.office"
        v-text="rule.office.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td>
      <span
        v-if="rule.offer"
        v-text="rule.offer.name"
      ></span>
      <span v-else>-</span>
    </td>
    <td v-text="rule.country_name"></td>
    <td>
      <toggle
        v-model="rule.allowed"
        @input="update"
      ></toggle>
    </td>
    <td>
      <router-link
        :to="{name: 'distribution-rules.update', params: {id: rule.id}}"
        class="font-semibold text-gray-700 hover:text-teal-700"
      >
        <fa-icon
          :icon="['far', 'edit']"
          class="text-gray-500 fill-current hover:text-teal-600"
        ></fa-icon>
      </router-link>
      <fa-icon
        :icon="['far', 'times-circle']"
        class="text-gray-500 fill-current cursor-pointer hover:text-red-600"
        fixed-width
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>

export default {
  name: 'distribution-rule-list-item',
  props: {
    rule: {
      type: Object,
      required: true,
    },
  },
  methods: {
    update() {
      axios.put(`/api/distribution-rules/${this.rule.id}`, this.rule)
        .then(response => {
          this.$emit('updated', {rule: response.data});
          this.$toast.success({title: 'OK', message: 'Правило обновлено.'});
        }).catch(err => this.$toast.error({title: 'Не удалось обновить правило.', message: err.response.data.message}));
    },
    remove() {
      axios.delete(`/api/distribution-rules/${this.rule.id}`)
        .then(response => {
          this.$toast.success({title: 'Удалено', message: 'Правило успешно удалено.'});
          this.$emit('deleted', {rule: this.rule});
        })
        .catch(err => this.$toast.error({title: 'Не удалось удалить правило', message: err.response.data.message}));
    },
  },
};
</script>

<style scoped>
    td {
        @apply px-6;
        @apply py-3;
        @apply whitespace-no-wrap;
        @apply text-sm;
        @apply leading-5;
        @apply text-gray-500;
    }
</style>
