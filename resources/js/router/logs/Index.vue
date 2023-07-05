<template>
  <div class="container mx-auto">
    <h1 class="text-gray-700 my-6">
      Логи
    </h1>
    <div class="flex flex-row flex-no-wrap">
      <div class="flex flex-col w-1/6">
        <router-link
          active-class="text-teal-700 font-semibold"
          :to="{ name: 'logs.payment-fails' }"
          class="my-2 text-base tracking-wide "
        >
          Сбои оплат
        </router-link>
        <router-link
          v-if="$root.user.role !== 'financier'"
          active-class="text-teal-700 font-semibold"
          :to="{ name: 'logs.ads-disapprovals' }"
          class="my-2 text-base tracking-wide "
        >
          Отклонения объявлений
        </router-link>
        <router-link
          active-class="text-teal-700 font-semibold"
          :to="{ name: 'logs.stopped-adsets' }"
          class="my-2 text-base tracking-wide "
        >
          Стоппер
        </router-link>
        <router-link
          v-if="$root.user.role === 'admin'"
          active-class="text-teal-700 font-semibold"
          :to="{ name: 'logs.sms-messages' }"
          class="my-2 text-base tracking-wide "
        >
          СМС сообщения
        </router-link>
        <router-link
          v-if="$root.user.role === 'admin'"
          active-class="text-teal-700 font-semibold"
          :to="{ name: 'logs.assignment-delivery-fails' }"
          class="my-2 text-base tracking-wide "
        >
          Ошибки доставки лида
        </router-link>
      </div>
      <div class="w-5/6">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'logs-index',
};
</script>
