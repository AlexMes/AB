<template>
  <modal
    name="leads-import-modal"
    height="auto"
    scrolable
  >
    <div class="flex flex-col p-4 justify-between h-full">
      <div class="mb-4 w-full flex items-center">
        <span
          v-if="origin"
          v-cloak
        >{{ origin }}</span>
        <label
          for="file"
          class="btn-primary w-48 ml-auto mr-0 px-4 py-2 rounded flex justify-center"
        >
          Загрузить файл
          <input
            id="file"
            type="file"
            accept=".xlsx, .xls, .csv"
            hidden
            @change="handleFile"
          />
        </label>
      </div>
      <form
        class="flex flex-col justify-end w-full"
        style="min-height: 50px"
        @submit.prevent="save"
      >
        <div v-if="fields.length > 0 && isCustom">
          <div
            v-for="[key, label] in importFields"
            :key="key"
            class="flex items-center mb-4"
          >
            <label
              :for="key"
              class="w-1/2"
              v-text="label"
            ></label>
            <div class="w-full flex flex-col items-end">
              <select
                :id="key"
                v-model="form[key]"
                :name="key"
                class="w-1/2 mb-1"
                @change="errors.clear(key)"
              >
                <option
                  v-for="field in fields"
                  :key="field"
                  :value="field"
                  v-text="field"
                ></option>
              </select>
              <div
                v-if="errors.has(key)"
                class="text-red-800 text-xs"
                v-text="errors.get(key)"
              ></div>
            </div>
          </div>
        </div>
        <div class="flex justify-between items-center">
          <div>
            <ads-checkbox v-model="isCustom">
              Произвольные поля?
            </ads-checkbox>
          </div>
          <div>
            <button
              type="submit"
              :disabled="!filename"
              class="mr-2 btn-primary px-4 py-2 rounded"
            >
              Сохранить
            </button>
            <button
              type="reset"
              class="btn-secondary px-4 py-2 rounded"
              @click="reset"
            >
              Отмена
            </button>
          </div>
        </div>
      </form>
    </div>
  </modal>
</template>

<script>
import AdsCheckbox from '../ads-checkbox';
import ErrorBag from '../../utilities/ErrorBag';
export default {
  name: 'leads-import-modal',
  components: {AdsCheckbox},
  data: () => ({
    origin: null,
    filename: null,
    fields: [],
    labels: {
      external_id: 'External id',
      status: 'Статус',
      responsible: 'Отвественный',
      comment: 'Комментарий',
      office_id: 'Офис',
      department_id: 'Департамент',
      called_at: 'Время звонка',
    },
    form: {
      external_id: 'user_id',
      status: 'tatu',
      responsible: 'otvetstvennyy',
      comment: 'kommentariymoderator',
      office_id: 'office_id',
      department_id: 'department_id',
      called_at: 'vremzvonka',
    },
    isCustom: false,
    errors: new ErrorBag(),
  }),
  computed: {
    importFields() {
      return Object.entries(this.labels);
    },
  },
  watch: {
    isCustom(oldVal, newVal) {
      newVal ? this.setDefaultForm() : this.form = {};
    },
  },
  methods: {
    handleFile(event) {
      const formData = new FormData();
      formData.append('leads', event.target.files[0]);
      if(event.target.files[0]) {
        this.origin = event.target.files[0].name;
        axios
          .post('/api/imports/leads', formData, {headers: {
            'Content-Type': 'multipart/form-data',
          }})
          .then(r => {
            this.filename = r.data.filename;
            this.fields = r.data.fields;
          })
          .catch(e => this.$toast.error({title: 'Ошибка', message: 'Не удалось загрузить файл'}));
      }
    },
    save() {
      axios
        .put(`/api/imports/leads/${this.filename}`, this.form)
        .then(r => {
          this.$toast.info({title: 'Ок', message: 'Импорт начат'});
          this.clear();
          this.close();
        })
        .catch(e => {
          this.errors.fromResponse(e);
          this.$toast.error({title: 'Ошибка при импорте', message: e.response.data.message});
        });
    },
    close() {
      this.$modal.hide('leads-import-modal');
    },
    clear() {
      this.filename = null;
      this.origin = null;
      this.fields = [];
      this.setDefaultForm();
    },
    setDefaultForm() {
      this.form = {
        external_id: 'id',
        status: 'tatu',
        responsible: 'otvetstvennyy',
        comment: 'kommentariymoderator',
        office_id: 'office_id',
        department_id: 'department_id',
        called_at: 'vremzvonka',
      };
    },
    reset() {
      this.clear();
      this.close();
    },
  },
};
</script>
