<template>
  <div class="">
    <div class="flex flex-col md:flex-row items-center bg-white rounded-lg pl-4">
      <div class="my-5 sm:col-span-3">
        <span class="inline-flex rounded-md shadow-sm">
          <a
            class="cursor-pointer inline-flex items-center px-3 py-2 -mb-px text-sm font-medium leading-4 text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700"
            @click="$modal.show('add-application-link-modal')"
          >
            <svg
              viewBox="0 0 128 128"
              width="16px"
              height="16px"
              fill="white"
              class="mr-2"
            >
              <path
                d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"
              /><path
                d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"
              />
            </svg>
            Добавить
          </a>
        </span>
      </div>
    </div>

    <div class="relative flex pb-4 overflow-x-auto">
      <div
        v-if="hasLinks"
        class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto max-w-8xl"
      >
        <div
          class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg"
        >
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                  ID
                </th>
                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                  Buyer
                </th>
                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                  URL
                </th>
                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                  GEO
                </th>
                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                  Active
                </th>
                <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                </th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <link-list-item
                v-for="link in links"
                :key="link.id"
                :link="link"
                :countries="countries"
                :users="users"
                :inline-editing="true"
                @link-updated="linkUpdated"
              ></link-list-item>
            </tbody>
          </table>
          <pagination
            :response="response"
            @load="load"
          ></pagination>
        </div>
      </div>
      <div
        v-else
        class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow"
      >
        <p>Ссылок не найдено</p>
      </div>
    </div>

    <add-application-link-modal
      :application-id="id"
      :countries="countries"
      :users="users"
    ></add-application-link-modal>
  </div>
</template>

<script>
import Pagination from '../../components/pagination';
import AddApplicationLinkModal from '../../components/links/add-application-link-modal';
import LinkListItem from '../../components/links/link-list-item';
export default {
  name: 'applications-links',
  components: {
    LinkListItem,
    Pagination,
    AddApplicationLinkModal,
  },
  props: {
    id: {
      type: [String, Number],
      required: true,
    },
  },
  data: () => {
    return {
      links: [],
      response: {},
      countries: [],
      users: [],
    };
  },
  computed: {
    hasLinks() {
      return this.links.length > 0;
    },
  },
  created() {
    this.load();
    this.loadCountries();
    this.loadUsers();
    this.listen();
  },
  methods: {
    load(page = 1) {
      axios.get(`/api/applications/${this.id}/links`, {
        params: {
          page,
        },
      })
        .then(({data}) => {
          this.links = data.data;
          this.response = data;
        })
        .catch(err => this.$toast.error({title: 'Unable to load links.', message: err.response.data.message}));
    },
    loadCountries() {
      axios.get('/api/countries')
        .then(({data}) => this.countries = data)
        .catch(err => this.$toast.error({title: 'Unable to load countries.', message: err.response.data.message}));
    },
    loadUsers() {
      axios.get('/api/users')
        .then(({data}) => this.users = data)
        .catch(err => this.$toast.error({title: 'Unable to load buyers.', message: err.response.data.message}));
    },
    listen() {
      this.$eventHub.$on('link-created', event => {
        this.links.unshift(event.link);
      });
    },
    linkUpdated(event) {
      const index = this.links.findIndex(link => link.id === event.link.id);
      if (index !== -1) {
        this.$set(this.links, index, event.link);
      }
    },
  },
};
</script>

<style scoped>

</style>
