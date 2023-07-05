<template>
  <div class="container mx-auto">
    <div class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6">
      <h3
        v-if="isUpdating"
        class="text-lg leading-6 font-medium text-gray-900"
        v-text="access.facebook_url"
      ></h3>
      <h3
        v-else
        class="text-lg leading-6 font-medium text-gray-900"
      >
        Новый доступ
      </h3>
      <div
        v-if="errors.hasMessage()"
        class="bg-red-700 text-white rounded p-3 my-4"
      >
        <span v-text="errors.message"></span>
      </div>
      <form @submit.prevent="save">
        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="profile_name"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Имя профиля
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="profile_name"
                v-model="access.profile_name"
                type="text"
                placeholder="Имя профиля"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('profile_name')"
              />
            </div>
            <span
              v-if="errors.has('profile_name')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('profile_name')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="received_at"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата получения
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="received_at"
                v-model="access.received_at"
                class="w-full px-1 py-2 border rounded text-gray-600 sm:text-sm"
                placeholder="Выберите дату"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('received_at')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('received_at')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="birthday"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Дата рождения
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <date-picker
                id="birthday"
                v-model="access.birthday"
                class="w-full px-1 py-2 border rounded text-gray-600 sm:text-sm"
                placeholder="Выберите дату"
              ></date-picker>
            </div>
            <span
              v-if="errors.has('birthday')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('birthday')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="facebook_url"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Профиль (ссылкой)
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="facebook_url"
                v-model="access.facebook_url"
                type="text"
                placeholder="http://facebook.com/123123123123"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('facebook_url')"
              />
            </div>
            <span
              v-if="errors.has('facebook_url')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('facebook_url')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="supplier_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Поставщик
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="supplier_id"
                v-model="access.supplier"
                :options="suppliers"
                track-by="id"
                label="name"
                :show-labels="false"
                placeholder="Выберите поставщика"
              >
              </mutiselect>
            </div>
            <span
              v-if="errors.has('supplier_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('supplier_id')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="user_id"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Баер (не обязательно)
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="user_id"
                v-model="access.user"
                :options="users"
                track-by="id"
                label="name"
                placeholder="Выберите пользователя"
                :allow-empty="true"
                :show-labels="false"
              >
              </mutiselect>
            </div>
            <span
              v-if="errors.has('user_id')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('user_id')"
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
            <div class="max-w-lg rounded-md shadow-sm">
              <mutiselect
                id="type"
                v-model="access.type"
                :options="accessTypes"
                track-by="id"
                label="label"
                :show-labels="false"
                placeholder="Выберите тип"
              >
              </mutiselect>
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
            for="login"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Логин
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="login"
                v-model="access.login"
                autocomplete="off"
                type="text"
                placeholder="Логин"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('login')"
              />
            </div>
            <span
              v-if="errors.has('login')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('login')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="password"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Пароль к профилю
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="password"
                v-model="access.password"
                autocomplete="off"
                type="password"
                placeholder="**********"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                required
                @input="errors.clear('password')"
              />
            </div>
            <span
              v-if="errors.has('password')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('password')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="email"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Почта
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="email"
                v-model="access.email"
                autocomplete="off"
                type="text"
                placeholder="email@example.com"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('email')"
              />
            </div>
            <span
              v-if="errors.has('email')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('email')"
            ></span>
          </div>
        </div>

        <div
          class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5"
        >
          <label
            for="email_password"
            class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2"
          >
            Пароль к почте
          </label>
          <div class="mt-1 sm:mt-0 sm:col-span-2">
            <div class="max-w-lg rounded-md shadow-sm">
              <input
                id="email_password"
                v-model="access.email_password"
                autocomplete="off"
                type="password"
                placeholder="**********"
                class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                @input="errors.clear('email_password')"
              />
            </div>
            <span
              v-if="errors.has('email_password')"
              class="block text-red-600 text-sm mt-1"
              v-text="errors.get('email_password')"
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
import ErrorBag from '../../utilities/ErrorBag';
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
export default {
  name: 'access-form',
  components:{
    DatePicker,
  },
  props:{
    id: {
      type: [String, Number],
      default: null,
    },
  },
  data: () => ({
    isBusy: false,
    access:{
      id: null,
      received_at: null,
      birthday:null,
      user: null,
      type: null,
      facebook_url:null,
      supplier:null,
      password:null,
      login: null,
      email:null,
      email_password:null,
    },
    errors: new ErrorBag(),
    users: [],
    suppliers: [],
    accessTypes: [
      {id:'farm', label:'Фарм'},
      {id:'brut', label:'Брут'},
      {id:'own',  label:'Личный'},
    ],
  }),
  computed:{
    isUpdating(){
      return this.id !== null;
    },
    cleanForm(){
      return {
        id: this.access.id,
        received_at: this.access.received_at,
        birthday:this.access.birthday,
        user_id: this.access.user === null ? null : this.access.user.id,
        type: this.access.type === null ? null : this.access.type.id,
        facebook_url: this.access.facebook_url,
        supplier_id: this.access.supplier === null ? null : this.access.supplier.id,
        password: !this.access.password ? undefined : this.access.password,
        login: this.access.login,
        email:this.access.email,
        email_password: !this.access.email_password ? undefined : this.access.email_password,
        profile_name: this.access.profile_name,
      };
    },
  },
  created() {
    if(this.isUpdating){
      this.find(this.id);
    }
    this.getUsers();
    this.getSuppliers();
  },
  methods:{
    find(id){
      axios.get(`/api/accesses/${id}`)
        .then(response => {
          this.access = response.data;
          this.access.type = this.accessTypes.find(t => t.id === this.access.type);
        })
        .catch(error => this.$toast.error({title:'Не удалось загрузить доступ',message:error.response.data.message}));
    },
    save(){
      this.isUpdating  ? this.update() : this.create();
    },
    cancel() {
      this.$router.push({name:'accesses.index'});
    },
    create(){
      this.isBusy = true;
      axios.post('/api/accesses',this.cleanForm)
        .then(response => this.$router.push({name:'accesses.index'}))
        .catch(error => {
          if (error.response.status === 422) {
            this.errors.fromResponse(error);
          } else {
            this.$toast.error({
              title: 'Не удалось создать доступ',
              message: error.response.data.message,
            });
          }
        })
        .finally(() => this.isBusy = false);
    },
    update(){
      this.isBusy = true;
      axios.put(`/api/accesses/${this.access.id}`,this.cleanForm)
        .then(() => this.$router.push({name:'accesses.index'}))
        .catch(error => {
          if (error.response.status === 422) {
            this.errors.fromResponse(error);
          } else {
            this.$toast.error({
              title: 'Не удалось обновить доступ',
              message: error.response.data.message,
            });
          }
        })
        .finally(() => this.isBusy = false);
    },
    getUsers(){
      axios.get('/api/users', {params:{all:true}})
        .then(response=>this.users=response.data)
        .catch(error => this.$toast.error({title:'Не удалось загрузить пользователей', message:error.response.data.message}));
    },
    getSuppliers(){
      axios.get('/api/suppliers')
        .then(response => this.suppliers = response.data)
        .catch(error => this.$toast.error({title:'Не удалось загрузить поставщиков',message:error.response.data.message}));
    },
  },
};
</script>

<style>
    select{
        @apply inline;
    }
</style>
