<template>
  <div class="container mx-auto">
    <h1 class="my-6 text-gray-700">
      Настройки
    </h1>
    <div class="flex flex-row flex-no-wrap">
      <div class="flex flex-col w-1/6">
        <router-link
          v-if="$root.user.role !== 'head'"
          active-class="font-semibold text-teal-700"
          :to="{ name: 'offices.index' }"
          class="my-2 text-base tracking-wide "
        >
          Офисы
        </router-link>
        <router-link
          v-if="['admin', 'support', 'head', 'developer'].includes($root.user.role)"
          active-class="font-semibold text-teal-700"
          :to="{ name: 'office-groups.index' }"
          class="my-2 text-base tracking-wide "
        >
          Группы офисов
        </router-link>
        <router-link
          v-if="$root.user.role !== 'head'"
          active-class="font-semibold text-teal-700"
          :to="{ name: 'offers.index' }"
          class="my-2 text-base tracking-wide "
        >
          Офферы
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'forms.index' }"
          class="my-2 text-base tracking-wide "
        >
          Интеграции лидов
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'pages.index' }"
          class="my-2 text-base tracking-wide "
        >
          Страницы
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'users.index' }"
          class="my-2 text-base tracking-wide "
        >
          Пользователи
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'groups.index' }"
          class="my-2 text-base tracking-wide "
        >
          Группы
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'tags.index' }"
          class="my-2 text-base tracking-wide "
        >
          Подходы
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'suppliers.index' }"
          class="my-2 text-base tracking-wide "
        >
          Поставщики
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'affiliates.index' }"
          class="my-2 text-base tracking-wide "
        >
          Партнеры
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'developer' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'traffic-sources.index' }"
          class="my-2 text-base tracking-wide "
        >
          Источник траффика
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'binoms.index' }"
          class="my-2 text-base tracking-wide "
        >
          Binom Settings
        </router-link>
        <router-link
          v-if="
            $root.user.role !== 'support' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'teams.index' }"
          class="my-2 text-base tracking-wide "
        >
          Команды
        </router-link>
        <router-link
          v-if="
            $root.user.role === 'admin' &&
              $root.user.role !== 'subsupport' &&
              $root.user.role !== 'head'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'tenants.index' }"
          class="my-2 text-base tracking-wide "
        >
          Арендаторы
        </router-link>
        <router-link
          v-if="
            $root.user.id === 1 || $root.user.role === 'developer'
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'branches.index' }"
          class="my-2 text-base tracking-wide "
        >
          Филиалы
        </router-link>
        <router-link
          v-if="$root.user.id === 1 || ['support'].includes($root.user.role)"
          active-class="font-semibold text-teal-700"
          :to="{ name: 'leads-destinations.index' }"
          class="my-2 text-base tracking-wide "
        >
          Дестинейшены
        </router-link>
        <router-link
          v-if="
            ['admin', 'support', 'developer'].includes(
              $root.user.role
            )
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'distribution-rules.index' }"
          class="my-2 text-base tracking-wide "
        >
          Правила раздачи
        </router-link>
        <router-link
          v-if="
            ['admin', 'developer', 'support'].includes(
              $root.user.role
            )
          "
          active-class="font-semibold text-teal-700"
          :to="{ name: 'proxies.index' }"
          class="my-2 text-base tracking-wide "
        >
          Прокси
        </router-link>
        <router-link
          v-if="['admin', 'support'].includes($root.user.role)"
          active-class="font-semibold text-teal-700"
          :to="{ name: 'lead-payment-conditions.index' }"
          class="my-2 text-base tracking-wide "
        >
          Условия оплаты лидов
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
  name: 'settings-index',
};
</script>
