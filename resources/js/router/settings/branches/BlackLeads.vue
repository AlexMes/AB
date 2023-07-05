<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <div class="w-1/4">
          <div class="max-w-lg rounded-md shadow-sm">
            <input
              v-model="form.phone"
              class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              type="text"
              @input="errors.clear('phone')"
            />
          </div>
          <span
            v-if="errors.has('phone')"
            class="block text-red-600 text-sm mt-1"
            v-text="errors.get('phone')"
          ></span>
        </div>
        <div class="w-auto flex items-center">
          <button
            class="button btn-primary flex items-center ml-3"
            @click="add"
          >
            <fa-icon
              :icon="['far','plus']"
              class="fill-current mr-2"
            ></fa-icon> Добавить
          </button>
        </div>
      </div>
    </div>

    <div v-if="hasBlackLeads">
      <div class="shadow">
        <black-lead-list-item
          v-for="item in black_leads"
          :key="item.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :black-lead="item"
          @deleted="remove"
        ></black-lead-list-item>
        <pagination
          :response="response"
          @load="load"
        ></pagination>
      </div>
    </div>

    <div
      v-else
      class="px-4 py-5 bg-white border-b border-gray-200 shadow sm:px-6"
    >
      <p>Лидов не найдено</p>
    </div>
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';
import BlackLeadListItem from '../../../components/settings/black-lead-list-item';
export default {
  name: 'black-leads',
  components: {BlackLeadListItem},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    black_leads: [],
    form: {
      phone: null,
    },
    response: null,
    errors: new ErrorBag(),
  }),
  computed: {
    hasBlackLeads() {
      return this.black_leads.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods: {
    load(page = 1) {
      axios
        .get('/api/black-leads', { params: {page, branch_id: this.id} })
        .then(({ data }) => {
          this.black_leads = data.data;
          this.response = data;
        })
        .catch(() =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load black leads',
          }),
        );
    },
    add() {
      if (this.form.phone !== null) {
        axios
          .post('/api/black-leads', {branch_id: this.id, phone: this.form.phone})
          .then(response => {
            if (this.black_leads.findIndex(item => item.id === response.data.id) === -1) {
              this.black_leads.push(response.data);
            }
            this.form.phone = null;
            this.$toast.success({title: 'Ok', message: 'Phone was added to blacklist successfully.'});
          })
          .catch(err => {
            if (err.response.status === 422) {
              return this.errors.fromResponse(err);
            }
            this.$toast.error({title: 'Unable to add phone to blacklist.', message: err.response.data.message});
          });
      }
    },
    remove(event) {
      const index = this.black_leads.findIndex(item => item.id === event.black_lead.id);
      if (index !== -1) {
        this.black_leads.splice(index, 1);
      }
    },
  },
};
</script>
