<template>
  <div class="container mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-gray-700">
        Старые лиды
      </h1>
      <div
        v-if="$root.user.role === 'admin' || $root.user.role === 'support'"
        class="flex items-center"
      >
        <div class="flex flex-col ml-4">
          <button
            class="button btn-primary"
            @click.prevent="$modal.show('distribute-leftovers-modal')"
          >
            <fa-icon
              class="fill-current cursor-pointer"
              :icon="distributeIcon"
              fixed-width
            ></fa-icon> Выдать
          </button>
        </div>
        <div class="flex flex-col ml-4">
          <button
            class="button btn-primary"
            @click.prevent="$modal.show('pack-leftovers-modal')"
          >
            <fa-icon
              class="fill-current cursor-pointer"
              :icon="['far','exchange']"
              fixed-width
            ></fa-icon> Упаковать в LO
          </button>
        </div>
        <div class="flex flex-col ml-4">
          <button
            class="button btn-primary"
            @click.prevent="$modal.show('unpack-leftovers-modal')"
          >
            <fa-icon
              class="fill-current cursor-pointer"
              :icon="['far','exchange']"
              fixed-width
            ></fa-icon> Вернуть из LO
          </button>
        </div>
      </div>
    </div>

    <div class="flex w-full bg-white shadow">
      <table class="w-full table table-auto relative">
        <thead
          class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky"
        >
          <tr class="px-3">
            <th class="px-2 py-3 pl-5">
              #
            </th>
            <th class="px-2 py-3">
              Offer
            </th>
            <th class="px-2 py-3">
              Leads
            </th>
            <th class="px-2 py-3">
              Assigned
            </th>
            <th class="px-2 py-3">
              Left
            </th>
            <th>
            </th>
          </tr>
        </thead>
        <tbody
          v-if="offers !== null"
          class="w-full"
        >
          <tr
            v-for="offer in offers"
            :key="offer.id"
          >
            <td v-text="offer.id"></td>
            <td v-text="offer.name"></td>
            <td v-text="offer.accepted"></td>
            <td v-text="offer.received || 0"></td>
            <td v-text="offer.leftover"></td>
            <td>
              <fa-icon
                v-if="offer.leftover > 0 && ($root.user.role === 'admin' || $root.user.role === 'support')"
                class="fill-current cursor-pointer text-gray-700 text-lg hover:text-teal-700"
                :icon="distributeIcon"
                fixed-width
                @click.prevent="$modal.show('distribute-leftovers-modal', {offer: offer})"
              ></fa-icon>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <pack-leftovers-modal></pack-leftovers-modal>
    <unpack-leftovers-modal></unpack-leftovers-modal>
    <distribute-leftovers-modal></distribute-leftovers-modal>
  </div>
</template>

<script>
import {faRepeat} from '@fortawesome/pro-regular-svg-icons';
import PackLeftoversModal from '../../components/leads-orders/pack-leftovers-modal';
import DistributeLeftoversModal from '../../components/leads-orders/distribute-leftovers-modal';
import UnpackLeftoversModal from '../../components/leads-orders/unpack-leftovers-modal';

export default  {
  name: 'left-overs-stats',
  components: {
    UnpackLeftoversModal,
    DistributeLeftoversModal,
    PackLeftoversModal,
  },
  data:() => ({
    offers: null,
  }),
  computed: {
    distributeIcon() {
      return faRepeat;
    },
  },
  created() {
    this.load();
    this.listen();
  },
  methods: {
    load() {
      axios.get('/api/leads-orders/leftovers-stats')
        .then(({data}) => this.offers = data)
        .catch(err => this.$toast.error({title: 'Error', message: err.statusText}));
    },
    listen() {
      this.$eventHub.$on('leftovers-packed', this.load);
      this.$eventHub.$on('leftovers-unpacked', this.load);
    },
  },
};
</script>
