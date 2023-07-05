<template>
    <div class="container flex flex-col mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="mb-8 text-gray-700">
                Рабочий стол
            </h1>
            <span></span>
        </div>
        <office-stats v-if="$root.user.role === 'admin'"></office-stats>
        <leads-orders-stats-progress
            v-if="
                $root.user.role === 'support' ||
                    $root.user.role === 'subsupport'
            "
        ></leads-orders-stats-progress>
        <leads-orders
            v-if="
                $root.user.role === 'support' ||
                    $root.user.role === 'subsupport'
            "
        ></leads-orders>
        <!-- <buyer-stats v-if="$root.user.role === 'teamlead'"></buyer-stats> -->
        <!-- <our-assignments
            v-if="$root.user.role === 'teamlead'"
        ></our-assignments> -->
    </div>
</template>

<script>
export default {
    name: "dashboard"
};
</script>
