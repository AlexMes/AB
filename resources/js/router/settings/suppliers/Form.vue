<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="supplier.name"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый поставщик
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Название
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="name"
                v-model="supplier.name"
                type="text"
                placeholder="Название поставщика"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                maxlength="40"
                minlength="3"
                @input="errors.clear('name')"
              />
            </div>
            <span
              v-if="errors.has('name')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('name')"
            ></span>
          </div>
        </div>

        <div class="w-full flex justify-end mt-6 border-t pt-4">
          <button
            type="reset"
            class="button btn-secondary mx-2"
            @click="cancel"
          >
            Отмена
          </button>
          <button
            type="submit"
            class="button btn-primary mx-2"
            :disabled="isBusy"
            @click.prevent="save"
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
  </div>
</template>

<script>
import ErrorBag from '../../../utilities/ErrorBag';

export default {
  name: 'suppliers-form',
  props: {
    id: {
      type: [String, Number],
      required :false,
      default: null,
    },
  },
  data:()=>({
    isBusy: false,
    supplier: {
      name : null,
    },
    errors: new ErrorBag(),
  }),
  computed:{
    isUpdating(){
      return this.$props.id !== null;
    },
  },
  created() {
    this.boot();
  },
  methods:{
    boot(){
      if(this.isUpdating){
        this.load();
      }
    },
    load(){
      axios.get(`/api/suppliers/${this.id}`)
        .then(r => this.supplier = r.data)
        .catch(err => this.$toast.error({title: 'Не удалось загрузить поставщика.', message: err.response.data.message}));
    },
    save(){
      this.isUpdating ? this.update() : this.create();
    },
    cancel() {
      this.isUpdating
        ? this.$router.push({name:'suppliers.show', params:{id: this.id}})
        : this.$router.push({name:'suppliers.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/suppliers/',this.supplier)
        .then(r => {
          this.$toast.success('Поставщик добавлен');
          this.$router.push({name:'suppliers.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось сохранить поставщика.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/suppliers/${this.supplier.id}`,this.supplier)
        .then(r => {
          this.$toast.success('Поставщик обнавлён');
          this.$router.push({name:'suppliers.show',params:{id:r.data.id}});
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Не удалось обновить поставщика.', message: err.response.data.message});
        })
        .finally(() => this.isBusy = false);
    },
  },
};
</script>
