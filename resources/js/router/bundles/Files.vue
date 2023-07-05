<template>
  <div>
    <div v-if="hasFiles">
      <div class="w-full bg-white p-4 border-b flex">
        <div class="w-11/12 ml-2 text-left flex items-center">
          <a
            href="#"
            @click.prevent="download(file)"
            v-text="file.file_name"
          ></a>
        </div>

        <div
          class="flex w-1/12 items-center justify-center p-4"
        >
          <fa-icon
            :icon="['far','times-circle']"
            class="fill-current ml-2 text-gray-700 hover:text-teal-700 cursor-pointer"
            fixed-width
            @click.prevent="remove(file.id)"
          ></fa-icon>
        </div>
      </div>
    </div>
    <div v-else>
      Файлов не найдено
    </div>

    <div class="flex mt-4">
      <button
        class="button btn-gray-secondary mx-4 cursor-default flex flex-row items-center"
        @click="open"
      >
        <a class="font-icons icon-clip stroke-current mr-2"></a>
        <input
          ref="file"
          type="file"
          class="hidden"
          v-bind="$attrs"
          @change="upload"
        />
        <div>Прикрепить файл</div>
      </button>
    </div>
  </div>
</template>

<script>
import downloadLink from '../../utilities/helpers/downloadLink';

export default {
  name: 'bundle-files',

  props: {
    id: {
      type: [String,Number],
      required: true,
      default: null,
    },
  },

  data() {
    return {
      files: [],
    };
  },

  computed: {
    hasFiles() {
      return this.files.length > 0;
    },
    file() {
      return this.hasFiles ? this.files[0] : null;
    },
  },

  created(){
    this.boot();
  },

  methods: {
    boot(){
      this.load();
    },
    load() {
      axios.get(`/api/bundles/${this.id}/files`)
        .then(r => {
          this.files = r.data.data;
        })
        .catch(e => {
          this.$toast.error({title: 'Не удалось загрузить файлы.', message: e.response.data.message});
        });
    },

    open() {
      this.$refs.file.click();
    },

    upload(event) {
      let file = event.target.files[0];

      let formData = new FormData();
      formData.append('file', file);

      axios
        .post(`/api/bundles/${this.id}/files`, formData)
        .then( response => {
          this.files.shift();
          this.files.unshift(response.data);
          this.$toast.success('Файл успешно добавлен.');
        })
        .catch(e => {
          this.$toast.error(e.response.data.message);
        });
    },

    download(file) {
      axios({
        url: `/api/bundles/${this.id}/files/${file.id}`,
        method: 'GET',
        responseType: 'blob',
      })
        .then(response => {
          downloadLink(response.data, file.file_name);
        })
        .catch(e => {
          this.$toast.error(e.response.data.message);
        });
    },

    remove(id){
      axios
        .delete(`/api/bundles/${this.id}/files/${id}`)
        .then( ({data}) => {
          const index = this.files.findIndex((file => file.id === id));
          if (index === -1) return;

          this.files.splice(index, 1);
          this.$toast.success('Файл успешно удален.');
        })
        .catch(e => {
          this.$toast.error(e.response.data.message);
        });
    },
  },
};
</script>
