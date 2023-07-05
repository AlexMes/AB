<template>
  <modal
    name="insight-budget-modal"
    height="auto"
    @before-open="beforeOpen"
  >
    <div class="flex flex-col w-full p-6">
      <div class="flex flex-col w-full mb-4">
        <label class="flex w-full mb-2 font-semibold">Бюджет для {{ insightful.name }}</label>
        <input
          v-model="budget"
          type="number"
          class="bg-gray-200 rounded text-gray-700 bg-gray-200 placeholder-gray-400 py-2 px-3"
        />
      </div>
      <div class="flex w-full">
        <button
          class="button btn-primary mr-2"
          :disabled="isBusy"
          @click="save"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="$modal.hide('insight-budget-modal')"
        >
          Отмена
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
  name: 'insight-budget-modal',
  props: {
    mode: {type: String, required: true},
  },
  data:() => ({
    isBusy:false,
    insightful: {},
    budget: 0,
    budgetField: null,
    availableModes: [
      'campaign',
      'adset',
    ],
  }),
  computed: {
    endpoint() {
      if (this.availableModes.indexOf(this.mode) === -1 || !this.insightful.id) {
        return null;
      }

      const resource = this.mode === 'campaign' ? 'campaigns' : 'adsets';
      return `/api/facebook/${resource}/${this.insightful.id}`;
    },
  },
  methods:{
    beforeOpen (event) {
      this.insightful = event.params.insightful;
      this.budget = Math.floor(this.insightful.budget);
      this.budgetField = this.insightful.budget_field;
    },
    save(){
      if (this.insightful.budget === this.budget || this.endpoint === null) {
        this.$modal.hide('insight-budget-modal');
        return;
      }

      this.isBusy = true;
      axios.patch(this.endpoint, {
        budget: this.budget,
        budget_field: this.budgetField,
      })
        .then(r => {
          this.insightful.budget = this.budget;
          this.$toast.success('Budget updated');
          this.$modal.hide('insight-budget-modal');
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось обновить бюджет.', message: e.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
