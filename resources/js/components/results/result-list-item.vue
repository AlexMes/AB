<template>
  <tr class="w-full bg-white items-center border-b px-3 hover:bg-gray-200">
    <td class="px-2 py-3 pl-5">
      <router-link
        :to="{name:'results.show', params:{id:result.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold w-1/12"
        v-text="`# ${result.id}`"
      >
      </router-link>
    </td>
    <td
      class="py-4 px-2"
      v-text="result.date"
    >
    </td>
    <td
      class="py-4 px-2 font-medium uppercase"
      v-text="result.offer.name"
    >
    </td>
    <td
      class="py-4 px-2 font-medium uppercase"
      v-text="result.office.name"
    >
    </td>
    <td
      class="py-4 px-2"
    >
      <span v-text="result.leads_count || 0"></span>
    </td>
    <td
      class="py-4 px-2"
    >
      <span v-text="result.no_answer_count || 0"></span>
      <span
        class="ml-2 text-xs text-gray-600"
        v-text="percentage(result.no_answer_count)"
      ></span>
    </td>
    <td
      class="py-4 px-2"
    >
      <span v-text="result.reject_count || 0"></span>
      <span
        class="ml-2 text-xs text-gray-600"
        v-text="percentage(result.reject_count)"
      ></span>
    </td>
    <td
      class="py-4 px-2"
    >
      <span v-text="result.wrong_answer_count || 0"></span>
      <span
        class="ml-2 text-xs text-gray-600"
        v-text="percentage(result.wrong_answer_count)"
      ></span>
    </td>
    <td
      class="py-4 px-2"
    >
      <span v-text="result.demo_count || 0"></span>
      <span
        class="ml-2 text-xs text-gray-600"
        v-text="percentage(result.demo_count)"
      ></span>
    </td>
    <td
      class="py-4 px-2"
    >
      <span v-text="result.ftd_count || 0"></span>
      <span
        class="ml-2 text-xs text-gray-600"
        v-text="percentage(result.ftd_count)"
      ></span>
    </td>
    <td
      class="py-4 px-2"
    >
      <router-link
        v-if="result.can_update"
        :to="{name:'results.update', params:{id:result.id}}"
        class="text-gray-700 hover:text-teal-700 font-semibold w-1/12"
      >
        <fa-icon
          :icon="['far','pencil-alt']"
          class="fill-current"
          fixed-width
        ></fa-icon>
      </router-link>
    </td>
    <td
      class="py-4 px-2 items-center"
    >
      <fa-icon
        v-if="result.can_delete"
        :icon="['far','times-circle']"
        class="text-gray-700 hover:text-red-700 font-semibold fill-current cursor-pointer"
        fixed-width
        @click="remove"
      ></fa-icon>
    </td>
  </tr>
</template>

<script>
export default {
  name: 'result-list-item',
  props:{
    result:{
      required:true,
      type:Object,
    },
  },
  methods:{
    percentage(value = 0){
      if(this.result.leads_count === 0){
        return 0;
      }
      return ((Number(value) / Number.parseInt(this.result.leads_count)) * 100).toFixed(2) + '%';
    },
    remove(){
      this.$modal.show('dialog', {
        title: 'Подтвердите действие!',
        text: 'Вы действительно хотите удалить результат?',
        buttons: [
          {
            title: 'Удалить',
            default: true,
            handler:() => axios.delete(`/api/results/${this.result.id}`)
              .then(response => {
                this.$toast.success({title: 'OK', message:'Результат удален'});
                this.$emit('gone');
              })
              .catch(err => this.$toast.error({
                title:'Не удалось удалить результат',
                message: err.response.statusText,
              }))
              .finally(() => this.$modal.hide('dialog')),
          },
          {
            title: 'Отмена',
          },
        ],
      });
    },
  },

};
</script>
