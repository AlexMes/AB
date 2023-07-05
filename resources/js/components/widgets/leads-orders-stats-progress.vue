<template>
  <div class="mb-6">
    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div>
          <dl class="px-4 py-5 pb-4">
            <dt
              class="text-sm leading-5 font-medium text-gray-500 truncate"
            >
              Заказано лидов
            </dt>
            <dd
              class="mt-1 text-3xl leading-9 font-semibold text-gray-900"
              v-text="ordered"
            ></dd>
          </dl>
          <ul class="text-sm text-gray-700">
            <li
              v-for="(progress, index) in progresses"
              :key="index"
              class="border-b border-px p-2 flex justify-between"
            >
              <span v-text="progress.name"></span>
              <span
                v-if="progress.ordered"
                v-text="progress.ordered.toLocaleString()"
              ></span>
              <span v-else>0</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div>
          <dl class="px-4 py-5 pb-4">
            <dt
              class="text-sm leading-5 font-medium text-gray-500 truncate"
            >
              Выдано лидов
            </dt>
            <dd
              class="mt-1 text-3xl leading-9 font-semibold text-gray-900"
              v-text="received"
            ></dd>
          </dl>
          <ul class="text-sm text-gray-700">
            <li
              is="router-link"
              v-for="(progress, index) in progresses"
              :key="index"
              class="border-b border-px p-2 flex justify-between"
              :to="{
                name: 'leads.index',
                params: { offer_id: progress.id, received: progress.received > 0, date: date }
              }"
            >
              <span v-text="progress.name"></span>
              <span
                v-if="progress.received"
                v-text="progress.received.toLocaleString() || 0"
              ></span>
              <span v-else>0</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div>
          <dl class="px-4 py-5 pb-4">
            <dt
              class="text-sm leading-5 font-medium text-gray-500 truncate"
            >
              Получено лидов
            </dt>
            <dd
              class="mt-1 text-3xl leading-9 font-semibold text-gray-900"
              v-text="accepted"
            ></dd>
          </dl>
          <ul class="text-sm text-gray-700">
            <li
              is="router-link"
              v-for="(progress, index) in progresses"
              :key="index"
              class="border-b border-px p-2 flex justify-between"
              :to="{
                name: 'leads.index',
                params: { offer_id: progress.id, accepted: progress.accepted > 0, date: date }
              }"
            >
              <span v-text="progress.name"></span>
              <span
                v-text="
                  progress.accepted.toLocaleString() || 0
                "
              ></span>
            </li>
          </ul>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div>
          <dl class="px-4 py-5 pb-4">
            <dt
              class="text-sm leading-5 items-center font-medium flex justify-between text-gray-500 truncate"
            >
              Текущий остаток
              <div>
                <span v-if="$root.user.role === 'admin' || $root.user.role === 'support' && $root.user.branch_id === 19">
                  <fa-icon
                    v-if="isBusy"
                    :icon="spinnerIcon"
                    class="text-lg cursor-wait"
                    spin
                  ></fa-icon>
                  <fa-icon
                    v-else
                    :icon="['far','exchange']"
                    class="text-lg cursor-pointer"
                    :disabled="isBusy"
                    @click.prevent="$modal.show('leftovers-change-offer')"
                  ></fa-icon>
                </span>

                <span v-if="[19, 16, 20, 26, 28].includes($root.user.branch_id)">
                  <fa-icon
                    v-if="isBusy"
                    :icon="spinnerIcon"
                    class="text-lg cursor-wait"
                    spin
                  ></fa-icon>
                  <fa-icon
                    v-else
                    :icon="runSmoothSplitterIcon"
                    class="text-lg cursor-pointer"
                    :disabled="isBusy"
                    @click.prevent="$modal.show('leftovers-smooth-splitter')"
                  ></fa-icon>
                </span>

                <span v-if="$root.user.role !== 'sales'">
                  <fa-icon
                    v-if="isBusy"
                    :icon="spinnerIcon"
                    class="text-lg cursor-wait"
                    spin
                  ></fa-icon>
                  <fa-icon
                    v-else
                    :icon="runSplitterIcon"
                    class="text-lg cursor-pointer"
                    :disabled="isBusy"
                    @click.prevent="$modal.show('leftovers-splitter')"
                  ></fa-icon>
                </span>
              </div>
            </dt>
            <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
              <span v-text="leftover"></span>
              <span
                v-if="$root.user.branch_id === 19"
                class="text-sm leading-3 font-medium"
                v-text="`Без _CD: ${leftover - leftoverCD}`"
              ></span>
            </dd>
          </dl>
          <ul class="text-sm text-gray-700">
            <li
              is="router-link"
              v-for="(progress, index) in progresses"
              :key="index"
              class="border-b border-px p-2 flex justify-between"
              :to="{
                name: 'leads.index',
                params: { offer_id: progress.id, leftovers: progress.leftover > 0, date: date }
              }"
            >
              <span v-text="progress.name"></span>
              <span v-text="progress.leftover || 0"></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <leftovers-splitter :date-period="date"></leftovers-splitter>
    <leftovers-smooth-splitter :date-period="date"></leftovers-smooth-splitter>
    <leftovers-change-offer></leftovers-change-offer>
  </div>
</template>

<script>
import {faRepeat, faSpinnerThird, faReplyAll} from '@fortawesome/pro-regular-svg-icons';
import LeftoversSplitter from '../../components/leads-orders/leftovers-splitter';
import LeftoversSmoothSplitter from '../leads-orders/leftovers-smooth-splitter';
import moment from 'moment';
import LeftoversChangeOffer from '../leads-orders/leftovers-change-offer';

export default {
  name: 'leads-orders-stats-progress',
  components: {
    LeftoversChangeOffer,
    LeftoversSmoothSplitter,
    LeftoversSplitter,
  },
  props: {
    date:{
      type:Object,
      default: ()=>({
        since: moment().format('YYYY-MM-DD'),
        until: moment().format('YYYY-MM-DD'),
      }),
    },
  },
  data:()=>({
    progresses: null,
    isBusy: false,
  }),
  computed:{
    runSmoothSplitterIcon() {
      return faReplyAll;
    },
    runSplitterIcon() {
      return faRepeat;
    },
    spinnerIcon() {
      return faSpinnerThird;
    },
    hasProgresses() {
      return this.progresses !== null;
    },
    ordered() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.ordered || 0)
          .reduce((a, b) => parseInt(a) + parseInt(b), 0);
      }
      return 0;
    },
    received() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.received || 0)
          .reduce((a, b) => parseInt(a) + parseInt(b), 0);
      }
      return 0;
    },
    accepted() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.accepted)
          .reduce((a, b) => parseInt(a) + parseInt(b), 0);
      }
      return 0;
    },
    leftover() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.leftover)
          .reduce((a, b) => parseInt(a || 0) + parseInt(b || 0), 0);
      }
      return 0;
    },
    leftoverCD() {
      if (this.hasProgresses) {
        return this.progresses
          .map(item => item.name.endsWith('_CD') ? item.leftover : 0)
          .reduce((a, b) => parseInt(a || 0) + parseInt(b || 0), 0);
      }
      return 0;
    },
  },
  watch: {
    date() {
      this.load();
    },
  },
  created() {
    this.load();
  },
  methods:{
    load(){
      axios
        .get('/api/leads-orders-stats/progress', {
          params: { date: this.date },
        })
        .then(({ data }) => {
          this.progresses = data.filter(row => row.leftover !== 0 || row.accepted !== 0 || row.ordered !== 0 || row.received !== 0);
        })
        .catch(err =>
          this.$toast.error({
            title: 'Error',
            message: 'Failed to load progress',
          }),
        );
    },
  },
};
</script>
