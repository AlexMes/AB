<template>
    <div class="hidden lg:ml-2 lg:flex lg:items-center">
        <router-link
            :to="{
                name: 'notifications.index',
                props: { id: user.id }
            }"
            class="inline-flex items-center bg-white p-0.5 rounded-full text-gray-400 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
        >
            <span class="sr-only">View notifications</span>
            <span
                v-if="!!notifications"
                class="mr-1 text-red-600"
                v-text="notifications"
            >
            </span>
            <!-- Heroicon name: outline/bell -->
            <svg
                class="w-6 h-6 mr-3"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>
        </router-link>

        <!-- Profile dropdown -->
        <div class="relative">
            <div v-if="open" class="fixed inset-0" @click="open = false"></div>
            <div>
                <button
                    id="user-menu-button"
                    type="button"
                    class="flex items-center space-x-1 text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                    aria-expanded="false"
                    aria-haspopup="true"
                    @click="open = !open"
                >
                    <span class="sr-only">Open user menu</span>
                    <img
                        class="w-8 h-8 rounded-full"
                        :src="
                            `https://eu.ui-avatars.com/api/?name=${user.name}&background=2C7A7B&color=F7FAFC`
                        "
                        alt="User's avatar"
                    />
                </button>
            </div>

            <transition
                enter-class="transform scale-90 opacity-0"
                enter-active-class="transition duration-200 ease-out"
                enter-to-class="transform scale-100 opacity-100"
                leave-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-to-class="transform scale-90 opacity-0"
            >
                <div
                    v-if="open"
                    class="absolute right-0 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    tabindex="-1"
                >
                    <!-- Active: "bg-gray-100", Not Active: "" -->
                    <router-link
                        v-if="
                            user.role !== 'farmer' &&
                                user.role !== 'financier' &&
                                user.role !== 'support' &&
                                user.role !== 'verifier'
                        "
                        id="profile-dropdown-item-0"
                        role="menuitem"
                        tabindex="-1"
                        :to="{ name: 'users.show', params: { id: user.id } }"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 focus:bg-gray-200"
                        active-class="bg-gray-100"
                        @click.native="open = !open"
                    >
                        Профиль
                    </router-link>
                    <a
                        id="profile-dropdown-item-1"
                        href="/logout"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 focus:bg-gray-200"
                        role="menuitem"
                        tabindex="-1"
                        >Выйти</a
                    >
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
export default {
    name: "profile-dropdown",
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data: () => ({
        open: false,
        notifications: 0
    }),
    watch: {
        open() {
            this.$eventHub.$emit("toggle-menu", this);
        }
    },
    created() {
        this.$eventHub.$on("toggle-menu", this.shouldClose);
        this.$eventHub.$on("db-notification", this.loadNotifications);
        this.$eventHub.$on("db-notification-read-all", this.loadNotifications);
        this.$eventHub.$on("db-notification-read", this.loadNotifications);
        this.loadNotifications();
    },
    beforeDestroy() {
        this.$eventHub.$off("toggle-menu", this.shouldClose);
        this.$eventHub.$off("db-notification", this.loadNotifications);
        this.$eventHub.$off("db-notification-read-all", this.loadNotifications);
        this.$eventHub.$off("db-notification-read", this.loadNotifications);
    },
    methods: {
        shouldClose(event) {
            if (this !== event) {
                this.open = false;
            }
        },
        loadNotifications() {
            axios
                .get(`/api/users/${this.user.id}/notifications`)
                .then(({ data }) => {
                    this.notifications = data.total;
                });
        }
    }
};
</script>
