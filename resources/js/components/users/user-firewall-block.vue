<template>
  <div class="flex flex-col h-full justify-between">
    <div class="flex flex-col my-2">
      <div
        v-for="permission in firewall"
        :key="permission.id"
        class="flex my-2"
      >
        <div
          class="w-4/5"
          v-text="permission.ip"
        ></div>
        <div
          class="w-1/5 mx-2"
          @click="removePermission(permission.id)"
        >
          <fa-icon
            :icon="deleteIcon"
            class="fill-current text-red-700 cursor-pointer hover:text-red-700"
            fixed-width
          ></fa-icon>
        </div>
      </div>
    </div>
    <div class="flex mt-2">
      <div class="flex flex-col w-4/5">
        <input
          v-model="ip"
          class="form-input w-full border rounded-md transition duration-150 sm:text-sm sm:leading-5"
          @input="errors.clear('ip')"
        />
        <span
          v-if="errors.has('ip')"
          class="text-red-600 text-sm mt-2"
          v-text="errors.get('ip')"
        ></span>
      </div>
      <div
        class="w-1/5 button btn-primary flex items-center self-start mt-1 ml-3"
        @click.prevent="addPermission"
      >
        <fa-icon
          :icon="['far','plus']"
          class="w-full fill-current mr-2"
        ></fa-icon> Добавить
      </div>
    </div>
  </div>
</template>

<script>
import {faTimes} from '@fortawesome/pro-regular-svg-icons';
import ErrorBag from '../../utilities/ErrorBag';

export default {
  name: 'user-firewall-block',
  props: {
    userId: {
      type: [Number, String],
      required: true,
    },
    firewall: {
      type: Array,
      required: true,
    },
    isAllowed: {
      type: Boolean,
      default: true,
    },
  },
  data: () => ({
    ip: '',
    errors: new ErrorBag(),
  }),

  computed: {
    deleteIcon() {
      return faTimes;
    },
  },

  methods: {
    addPermission() {
      axios.post(
        `/api/users/${this.userId}/firewall`,
        {
          ip: this.ip,
          is_allowed: this.isAllowed ? true : null,
          is_blocked: !this.isAllowed ? true : null,
        },
      )
        .then(response => {
          this.ip = '';
          this.$emit('changed', response.data);
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title: 'Не удалось добавить доступ.', message: e.response.data.message});
        });
    },
    removePermission(id) {
      axios.delete(`/api/users/${this.userId}/firewall/${id}`)
        .then(response => {
          this.$emit('changed', response.data);
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось удалить доступ.', message: e.response.data.message});
        });
    },
  },
};
</script>
