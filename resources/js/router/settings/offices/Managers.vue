<template>
  <div class="w-full">
    <div class="shadow">
      <div class="flex w-full bg-white p-3 flex justify-end border-b">
        <span class="inline-flex rounded-md shadow-sm">
          <span
            class="cursor-pointer relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800"
            @click="$modal.show('add-office-manager-modal')"
          >
            <fa-icon
              :icon="['far', 'plus']"
              class="-ml-1 mr-2 h-5 w-5 text-gray-400"
              fixed-width
            ></fa-icon>
            <span>
              Добавить
            </span>
          </span>
        </span>
      </div>
    </div>
    <div
      v-if="hasManagers"
    >
      <div class="shadow">
        <manager-list-item
          v-for="manager in managers"
          :key="manager.id"
          class="mt-0 border-b shadow-none hover:shadow-none"
          :manager="manager"
        ></manager-list-item>
      </div>
      <pagination
        :response="response"
        @load="load"
      ></pagination>
    </div>
    <div
      v-else
      class="bg-white shadow px-4 py-5 border-b border-gray-200 sm:px-6"
    >
      <p>Менеджеров не найдено</p>
    </div>
    <add-office-manager-modal :office-id="id"></add-office-manager-modal>
    <delete-office-manager-modal :office-id="id"></delete-office-manager-modal>
    <change-manager-office-modal @office-changed="remove"></change-manager-office-modal>
  </div>
</template>

<script>
import AddOfficeManagerModal from '../../../components/managers/add-office-manager-modal';
import DeleteOfficeManagerModal from '../../../components/managers/delete-office-manager-modal';
import ChangeManagerOfficeModal from '../../../components/managers/change-manager-office-modal';
export default {
  name: 'offices-managers',
  components: {ChangeManagerOfficeModal, DeleteOfficeManagerModal, AddOfficeManagerModal},
  props: {
    id: {
      type: [Number, String],
      required: true,
    },
  },
  data: () => ({
    managers: [],
    response: null,
    manager: {
      name: null,
      email: null,
    },
  }),
  computed:{
    hasManagers() {
      return this.managers.length > 0;
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(page = 1){
      axios
        .get(`/api/offices/${this.id}/managers`, {params: {page, paginate: true}})
        .then(response => {
          this.managers = response.data.data;
          this.response = response.data;
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Unable to load managers',
          }),
        );
    },
    remove(event) {
      const index = this.managers.findIndex(manager => manager.id === event.manager.id);
      if (index !== -1) {
        this.managers.splice(index, 1);
      }
    },
  },
};
</script>
