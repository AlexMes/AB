<template>
  <div class="w-full">
    <div class="flex flex-col bg-white shadow text-gray-700 flex-1 no-last-border">
      <div class="p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Системный ID</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-text="profile.id"
          ></span>
        </div>
      </div>
      <div class="p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Имя профиля</strong>
        </div>
        <div class="flex w-3/4">
          <span
            v-text="profile.name"
          ></span>
        </div>
      </div>
      <div class="p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Назначеный баер</strong>
        </div>
        <div
          class="flex w-3/4"
        >
          <div
            v-if="isEditing"
            class="flex mx-4"
          >
            <multiselect
              v-model="profile.user"
              :options="buyers"
              :allow-empty="true"
              :multiple="false"
              track-by="id"
              label="name"
              :show-labels="false"
            ></multiselect>
          </div>
          <div v-else>
            <span
              v-if="hasUser"
              v-text="profile.user.name"
            ></span>
            <span
              v-else
              class="text-gray-600"
            >
              Отсутствует
            </span>
          </div>
        </div>
      </div>
      <div class="p-3 flex flex-row border-b">
        <div class="flex w-1/4">
          <strong>Группа</strong>
        </div>
        <div
          class="flex w-3/4"
        >
          <div
            v-if="isEditing"
            class="flex mx-4"
          >
            <multiselect
              v-model="profile.group"
              :options="groups"
              :show-labels="false"
              :allow-empty="true"
              label="name"
              placeholder="Выберите группу"
              track-by="id"
            ></multiselect>
          </div>
          <div v-else>
            <span
              v-if="hasGroup"
              v-text="profile.group.name"
            ></span>
            <span
              v-else
              class="text-gray-600"
            >
              Отсутствует
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="flex text-gray-700 justify-end mt-4">
      <div v-if="!isEditing">
        <button
          class="button btn-secondary"
          @click="isEditing = !isEditing"
        >
          Редактировать
        </button>
      </div>
      <div v-else>
        <button
          class="button btn-primary"
          @click="save"
        >
          Сохранить
        </button>
        <button
          class="button btn-secondary"
          @click="reset"
        >
          Отмена
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import 'vue-multiselect/dist/vue-multiselect.min.css';
import Multiselect from 'vue-multiselect';

export default {
  name: 'general-info',
  components: {
    Multiselect,
  },
  props:{
    id:{
      type:Number,
      required:true,
    },
  },
  data: () => ({
    profile:{},
    buyers:[],
    groups: [],
    isEditing: false,
    original:{},
  }),
  computed:{
    hasUser(){
      return this.profile.user !== undefined && this.profile.user !== null;
    },
    hasGroup(){
      return this.profile.group !== undefined && this.profile.group !== null;
    },
  },
  watch:{
    'profile.user'(value){
      this.profile.user_id = value ? value.id : null;
    },
    'profile.group'(value){
      this.profile.group_id = value ? value.id : null;
    },
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.load();
      vm.getUsers();
      vm.getGroups();
      vm.listen();
    });
  },
  methods:{
    save(){
      axios.patch(`/api/profiles/${this.id}`, this.profile)
        .then(response => {this.load(); this.isEditing = false;})
        .catch(error => {
          this.$toast.error({title: 'Не удалось обновить профиль.', message: error.response.data.message});
        });
    },
    load(){
      axios.get(`/api/profiles/${this.id}`)
        .then(response => {
          this.original = response.data;
          this.profile = {...this.original};
        })
        .catch(err => {
          this.$toast.error({title: 'Не удалось загрузить профиль.', message: err.response.data.message});
        });
    },
    listen(){
      Echo.private('profiles')
        .listen('Facebook.Events.Profiles.ProfileUpdated', event => {
          this.profile = event.profile;
        });
    },
    reset() {
      this.profile = {...this.original};
      this.isEditing = false;
    },
    getUsers() {
      axios.get('/api/users',{params:{all:true}})
        .then(response => this.buyers = response.data)
        .catch(error => {
          this.$toast.error({title: 'Не удалось загрузить баеров.', message: error.response.data.message});
        });
    },
    getGroups() {
      axios.get('/api/groups', {params: {all:true}})
        .then(r => this.groups = r.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить группы.', message: e.response.data.message});
        });
    },
  },
};
</script>
