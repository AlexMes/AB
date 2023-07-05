<template>
  <div class="flex flex-col w-full shadow bg-white border-b">
    <div
      v-if="noRules"
      class="flex text-center p-4 text-lg"
    >
      no rules for now
    </div>
    <ul v-else>
      <li
        v-for="rule in firewall"
        :key="rule.id"
        class="px-3 py-4 flex items-center"
      >
        <span v-text="rule.ip"></span>
        <span
          class="cursor-pointer ml-3 text-red-500"
          @click="remove(rule)"
        >
          <fa-icon :icon="['far','times-circle']"></fa-icon>
        </span>
      </li>
    </ul>
    <form
      class="flex items-center justify-between my-3 mx-5"
      @submit.prevent="save"
    >
      <input
        v-model="rule.ip"
        type="text"
        placeholder="New IP address"
        class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
      />
      <button
        type="submit"
        class="button btn-primary mx-2"
        @click.prevent="save"
      >
        Добавить
      </button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'users-firewall',
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    firewall: [],
    rule:{
      ip:null,
    },
  }),
  computed:{
    noRules() {
      return this.firewall.length < 1;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios
        .get(`/api/users/${this.id}/firewall`)
        .then(({data}) => {
          this.firewall = data;
        })
        .catch(e =>
          this.$toast.error({
            title: 'Не удалось загрузить доступы.',
            message: e.response.data.message,
          }),
        );
    },
    save(){
      axios.post(`/api/users/${this.id}/firewall`,this.rule)
        .then(() => {this.load(); this.rule.ip = null;})
        .catch(() => this.$toast.error({title:'Failure'}));
    },
    remove(rule){
      if(confirm('Sure?')){
        axios.delete(`/api/users/${this.id}/firewall/${rule.id}`)
          .then(() => this.load())
          .catch(() => this.$toast.error({title:'Failure'}));
      }
    },
    change(firewall) {
      this.firewall = firewall;
    },
  },
};
</script>
