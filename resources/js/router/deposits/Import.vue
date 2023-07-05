<template>
  <div class="container mx-auto">
    <div class="bg-white flex flex-col shadow p-6">
      <div class="flex justify-between">
        <h2 class="text-gray-700 mb-5">
          Новые депозиты
        </h2>
        <div class="flex">
          <label
            class="button btn-primary flex items-center ml-3"
            @click="loadLeads"
          >
            <fa-icon
              :icon="['far','plus']"
              class="fill-current mr-2"
            ></fa-icon> Загрузить
            <!--            <input
              id="file"
              type="file"
              hidden
              @change="handleFile"
            />-->
          </label>
          <label
            v-if="leads"
            class="button btn-primary flex items-center ml-3"
            @click="saveDeposits"
          >
            <fa-icon
              :icon="['far','file']"
              class="fill-current mr-2"
            ></fa-icon> Сохранить
          </label>
        </div>
      </div>
      <div class="my-2 flex items-center">
        <div class="w-full">
          <input
            v-model="search"
            type="text"
            class="w-full border-b text-gray-700 placeholder-gray-400 mt-2 px-1 py-2"
            :placeholder="searchPlaceholder"
          />
        </div>
        <div class="flex align-middle mx-2 my-2 md:my-0 w-56">
          <select v-model="searchField">
            <option
              v-for="field in searchFields"
              :key="field.id"
              :value="field.id"
              v-text="field.name"
            ></option>
          </select>
        </div>
      </div>
      <div
        v-if="leads"
        class="overflow-x-auto overflow-y-hidden flex w-full"
      >
        <table class="w-full table table-auto relative">
          <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
            <tr
              class="px-3"
            >
              <th class="px-2 py-3">
                Дата лида
              </th>
              <th class="px-2 py-3">
                Дата
              </th>
              <th class="px-2 py-3">
                Click ID
              </th>
              <th class="px-2 py-3">
                Офис
              </th>
              <th class="px-2 py-3">
                Оффер
              </th>
              <th class="px-2 py-3">
                Телефон
              </th>
              <th class="px-2 py-3">
                Сумма
              </th>
              <th class="px-2 py-3">
                UTM
              </th>
              <th class="px-2 py-3"></th>
            </tr>
          </thead>
          <tbody class="w-full">
            <tr
              v-for="(lead, index) in leads"
              :key="lead.id"
              class="hover:bg-gray-100 text-black font-normal normal-case"
              :class="mark(lead)"
            >
              <td class="px-2 py-3">
                {{ lead.created_at | date }}
              </td>
              <td
                class="px-2 py-3"
              >
                <date-picker
                  id="datetime"
                  v-model="lead.depDate"
                  class="w-full px-1 py-2 mt-2 border rounded text-gray-600"
                  :config="picker"
                  placeholder="Выберите дату"
                ></date-picker>
              </td>
              <td
                class="px-2 py-3"
              >
                <span
                  class="w-16 h-full truncate flex items-center hover:w-full hover:whitespace-normal"
                  v-text="lead.clickid"
                ></span>
              </td>
              <td class="px-2 py-3">
                <select
                  v-model="lead.office_id"
                >
                  <option
                    v-for="office in offices"
                    :key="office.id"
                    :value="office.id"
                    v-text="office.name"
                  ></option>
                </select>
              </td>
              <td
                v-if="lead.offer"
                class="px-2 py-3"
                v-text="lead.offer.name"
              >
              </td>
              <td
                class="px-2 py-3"
                v-text="lead.phone"
              >
              </td>
              <td class="px-2 py-3">
                <input
                  v-model="lead.sum"
                  type="number"
                  class="w-full border-b text-gray-700 placeholder-gray-400 mt-2 px-1 py-2"
                  placeholder="0"
                />
              </td>
              <td class="px-2 py-3 flex flex-col h-full">
                <span
                  v-text="lead.utm_type"
                ></span>
                <span
                  v-text="lead.utm_source"
                ></span>
                <span
                  v-text="lead.utm_content"
                ></span>
                <span
                  v-text="lead.utm_campaign"
                ></span>
                <span
                  v-text="lead.utm_term"
                ></span>
                <span
                  v-text="lead.utm_medium"
                ></span>
              </td>
              <td class="px-2 py-3">
                <button
                  class="btn-secondary p-2 rounded"
                  @click="remove(index)"
                >
                  Удалить
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--<div
            v-if="file"
            class="overflow-x-auto overflow-y-hidden flex w-full"
        >
            <table class="w-full table table-auto relative">
                <thead class="bg-gray-300 text-gray-700 uppercase font-semibold w-full sticky">
                    <tr
                        class="px-3"
                    >
                        <th
                            v-for="header in headers"
                            :key="header"
                            v-text="header"
                            class="px-2 py-3"
                        ></th>
                    </tr>
                </thead>
                <tbody class="w-full">
                    <deposit-import-list-item
                        v-for="deposit in file"
                        :key="deposit.id"
                        class="bg-white hover:bg-gray-100 text-black font-normal normal-case"
                        :deposit="deposit"
                    >
                    </deposit-import-list-item>
                </tbody>
            </table>
        </div>-->
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import {format, subDays, parseISO} from 'date-fns';

export default {
  name: 'import',
  components: {
    DatePicker,
  },
  filters: {
    date: function (value) {
      return value ? format(parseISO(value), 'yyyy-MM-dd').toString() : '-';
    },
  },
  data: () => ({
    file: null,
    search:'',
    search_prepared: [],
    leads: null,
    offices: null,
    deposits: null,
    picker: {
      enableTime:false,
      dateFormat: 'Y-m-d',
    },
    searchField: 'phone',
    searchFields: [
      {id: 'phone', name: 'Телефон'},
      {id: 'id', name: 'ID'},
      {id: 'email', name: 'E-mail'},
    ],
  }),
  computed: {
    searchPlaceholder() {
      if (this.searchField === 'phone') {
        return 'xxx-xxx-xxx, xxx-xxx-xxx';
      }
      if (this.searchField === 'id') {
        return 'id1, id2...';
      }
      if (this.searchField === 'email') {
        return 'email1, email2...';
      }
      return '???';
    },
  },
  methods: {
    remove(index) {
      this.leads.splice(index, 1);
    },
    mark(lead) {
      if (lead.deposits && lead.deposits.length > 0) {
        return 'bg-red-200';
      } else if (this.isDuplicate(lead)) {
        return 'bg-orange-200';
      }
      return 'bg-white';
    },
    isDuplicate(searchableLead) {
      return this.leads
        .filter(lead => lead.id != searchableLead.id)
        .some(lead => lead.phone == searchableLead.phone);
    },
    loadLeads() {
      this.getOffices();
      this.prepareClickIds();
      axios.get('/api/imports/deposits/leads', {
        params:{
          search:this.search_prepared,
          searchField: this.searchField,
        },
      })
        .then(response => {
          this.leads = response.data.map(lead => {
            return {
              ...lead,
              depDate: format(subDays(new Date(), 1), 'yyyy-MM-dd').toString(),
            };
          });
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить лиды', message: e.response.data.message});
        });
    },
    prepareClickIds() {
      this.search_prepared = this.search.split(/,\s*/);
    },
    getOffices() {
      axios.get('/api/offices', {params:{all:true}})
        .then(response => this.offices = response.data)
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить офисы', message: e.response.data.message});
        });
    },
    saveDeposits() {
      this.prepareDeposits();
      axios
        .post('/api/imports/deposits', {deposits: this.deposits })
        .then(r => {
          this.leads = null;
          this.$toast.success({title: 'Импорт депозитов начат.'});
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось импортировать депозиты.', message: e.response.data.message});
        });
    },
    prepareDeposits() {
      this.deposits = this.leads.map(function (lead) {
        return {
          lead_id: lead.id,
          office_id: lead.office_id,
          sum: lead.sum ? lead.sum: 0,
          date: lead.depDate ? lead.depDate: null,
          lead_return_date: format(parseISO(lead.created_at), 'yyyy-MM-dd').toString(),
          phone: lead.phone,
          offer_id: lead.offer_id,
          account_id: lead.account_id,
          user_id: lead.user_id,
        };
      });
    },
    handleFile(event) {
      const files = event.target.files || event.dataTransfer.files;
      if (!files.length) return;
      const targetsFile = new FormData();
      targetsFile.append('deposits', files[0]);
      this.file = targetsFile;
      axios
        .post('/api/imports/deposits', this.file)
        .then(r => {
          this.file = r.data.data;
          this.headers = r.data.headers;
        })
        .catch(e => {
          console.log(e);
        });
    },
  },
};
</script>
