<template>
    <div class="relative">
        <div v-if="open" class="fixed inset-0" @click="open = false"></div>
        <div @click="open = !open">
            <a
                class="flex items-center h-16 px-1 pt-1 text-sm font-medium leading-5 transition border-b-2 cursor-pointer focus:outline-none"
                :class="
                    active
                        ? 'border-teal-500 text-teal-700 focus:border-teal-700'
                        : 'border-transparent text-gray-700 hover:text-teal-700 hover:border-gray-300 focus:text-teal-700 focus:border-gray-300'
                "
            >
                <span v-text="heading"></span>
                <svg
                    class="w-4 h-4 ml-1"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </a>
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
                v-show="open"
                class="absolute right-0 z-50 w-48 mt-2 origin-top-right rounded-md shadow-lg"
                style="display: none;"
                @click="open = false"
            >
                <div
                    class="py-1 bg-white rounded-md ring-1 ring-black ring-opacity-5"
                >
                    <slot></slot>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    name: "menu-dropdown",
    props: {
        heading: {
            type: String,
            required: true,
            default: "Menu"
        },
        active: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    data: () => ({
        open: false
    }),
    watch: {
        open(value) {
            this.$eventHub.$emit("toggle-menu", { el: this, value: value });
        }
    },
    created() {
        this.$eventHub.$on("toggle-menu", this.shouldClose);
    },
    beforeDestroy() {
        this.$eventHub.$off("toggle-menu", this.shouldClose);
    },
    methods: {
        shouldClose(event) {
            if (this !== event.el && event.value === true) {
                this.open = false;
            }
        }
    }
};
</script>
