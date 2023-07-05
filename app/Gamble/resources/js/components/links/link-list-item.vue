<template>
  <tr class="hover:bg-gray-100">
    <td
      class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200"
      v-text="link.id"
    >
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="!isEditing"
        v-text="link.user.name"
      ></span>
      <div
        v-else
        class="mt-1 sm:mt-0 sm:col-span-2"
      >
        <div class="max-w-xs rounded-md shadow-sm">
          <multiselect
            id="user"
            v-model="localLink.user"
            :show-labels="false"
            :multiple="false"
            :options="users"
            placeholder="Выберите байера"
            track-by="id"
            label="name"
            @input="errors.clear('user_id')"
          ></multiselect>
        </div>
        <p
          v-if="errors.has('user_id')"
          class="mt-2 text-sm text-red-600"
          v-text="errors.get('user_id')"
        ></p>
      </div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="!isEditing"
        v-text="link.url"
      ></span>
      <div
        v-else
        class="mt-1 sm:mt-0 sm:col-span-2"
      >
        <div class="max-w-xs rounded-md shadow-sm">
          <input
            v-model="localLink.url"
            class="form-input block w-full sm:text-sm sm:leading-5"
            type="text"
            @input="errors.clear('url')"
          />
        </div>
        <p
          v-if="errors.has('url')"
          class="mt-2 text-sm text-red-600"
          v-text="errors.get('url')"
        ></p>
      </div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="!isEditing"
        v-text="link.geo"
      ></span>
      <div
        v-else
        class="mt-1 sm:mt-0 sm:col-span-2"
      >
        <div class="max-w-xs rounded-md shadow-sm">
          <multiselect
            id="geo"
            v-model="localLink.countries"
            :show-labels="false"
            :multiple="true"
            :options="countries"
            placeholder="Выберите разрешенные гео"
            track-by="code"
            label="name"
            @input="errors.clear('geo')"
          ></multiselect>
        </div>
        <p
          v-if="errors.has('geo')"
          class="mt-2 text-sm text-red-600"
          v-text="errors.get('geo')"
        ></p>
      </div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <span
        v-if="!isEditing"
        class="inline-flex px-2 py-2 rounded-full"
        :class="link.enabled ? 'bg-green-500' : 'bg-red-500'"
      ></span>
      <div
        v-else
        class="mt-1 sm:mt-0 sm:col-span-2"
      >
        <div class="max-w-xs">
          <toggle v-model="localLink.enabled"></toggle>
        </div>
        <p
          v-if="errors.has('enabled')"
          class="mt-2 text-sm text-red-600"
          v-text="errors.get('enabled')"
        ></p>
      </div>
    </td>
    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
      <div v-if="editable && !isEditing">
        <span
          class="cursor-pointer"
          @click="toggleEditing"
        >
          <svg
            class="-ml-1 mr-2 h-5 w-5 text-gray-400"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
            />
          </svg>
        </span>
      </div>
      <div
        v-else
        class="flex"
      >
        <span
          class="cursor-pointer"
          @click="save"
        >
          <svg
            class="w-4 h-4 mr-2 cursor-pointer"
            fill="currentColor"
            viewBox="0 0 20 20"
          ><path
            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
            clip-rule="evenodd"
            fill-rule="evenodd"
          /></svg>
        </span>
        <span
          class="cursor-pointer"
          @click="cancel"
        >
          <svg
            class="w-4 h-4 mr-2 cursor-pointer"
            fill="currentColor"
            viewBox="0 0 20 20"
          ><path
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd"
            fill-rule="evenodd"
          /></svg>
        </span>
      </div>
    </td>
  </tr>
</template>

<script>
import ErrorBag from '../../../../../../resources/js/utilities/ErrorBag';
import Toggle from '../toggle';

export default {
  name: 'link-list-item',
  components: {
    Toggle,
  },
  props: {
    link: {
      type: Object,
      required: true,
    },
    inlineEditing: {
      type: Boolean,
      required: false,
      default: false,
    },
    countries: {
      type: Array,
      required: true,
    },
    users: {
      type: Array,
      required: true,
    },
  },
  data: () => {
    return {
      isEditing: false,
      localLink: {
        user: null,
        url: null,
        geo: null,
        enabled: false,
        countries: [],
      },
      errors: new ErrorBag(),
    };
  },
  computed: {
    editable() {
      return this.inlineEditing;
    },
    cleanForm() {
      return {
        user_id: !this.localLink.user ? null : this.localLink.user.id,
        url: this.localLink.url,
        geo: this.localLink.countries.map(country => country.code).join(','),
        enabled: this.localLink.enabled,
      };
    },
  },
  methods: {
    toggleEditing() {
      this.isEditing = !this.isEditing;
      this.resetLocalLink();
    },
    save() {
      axios.put(`/api/applications/${this.link.app_id}/links/${this.link.id}`, this.cleanForm)
        .then(({data}) => {
          this.$toast.success({title: 'OK', message: 'App link has been successfully updated.'});
          this.$emit('link-updated', {link: data});
          this.toggleEditing();
        })
        .catch(err => {
          if (err.response.status === 422) {
            return this.errors.fromResponse(err);
          }
          this.$toast.error({title: 'Unable to update app link.', message: err.response.data.message});
        });
    },
    cancel() {
      this.toggleEditing();
    },
    resetLocalLink() {
      this.localLink = {
        user: this.link.user,
        url: this.link.url,
        geo: this.link.geo,
        enabled: this.link.enabled,
        countries: this.link.countries,
      };
    },
  },
};
</script>

<style scoped>

</style>
