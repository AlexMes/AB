<template>
    <div>
        <user-telegram-notifications
            :id="id"
            :notifications="telegramNotifications"
            @add="addTelegramNotification"
            @remove="removeTelegramNotification"
        ></user-telegram-notifications>
    </div>
</template>

<script>
export default {
    name: "users-settings",
    props: {
        id: {
            type: [Number, String],
            required: true
        }
    },
    data: () => ({
        telegramNotifications: []
    }),
    created() {
        this.getTelegramNotifications();
    },
    methods: {
        getTelegramNotifications() {
            axios
                .get(`/api/users/${this.id}/denied-telegram-notifications`)
                .then(r => (this.telegramNotifications = r.data))
                .catch(e =>
                    this.$toast.error({
                        title:
                            "Не удалось загрузить настройки телеграм уведомлений.",
                        message: e.response.data.message
                    })
                );
        },
        addTelegramNotification(notification) {
            this.telegramNotifications.push(notification);
        },
        removeTelegramNotification(id) {
            const index = this.telegramNotifications.findIndex(
                notification => notification.id === id
            );
            if (index !== -1) {
                this.telegramNotifications.splice(index, 1);
            }
        }
    }
};
</script>
