<span x-data="{openFilterButton: false}" class="relative z-10 inline-flex shadow-sm rounded-md">
    <button type="submit"
            class="relative inline-flex items-center px-3 py-2 text-sm font-medium leading-5 rounded-l-md text-gray-50 bg-indigo-600 border border-white hover:text-white hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700">
        {{ $slot }}
    </button>
    <span @click.away="openFilterButton = false" class="-ml-px relative block">
        <button
            type="button"
            class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 rounded-r-md text-gray-50 bg-indigo-600 border border-white focus:z-10 hover:text-white hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150"
            aria-label="Expand"
            @click.prevent="openFilterButton = !openFilterButton"
        >
            <svg
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
            <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
            />
            </svg>
        </button>

        <div
            class="origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg"
            x-show="openFilterButton"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-90"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-90"
        >
            <div class="rounded-md bg-indigo-600 shadow-xs">
                <div class="py-1">
                    {{ $options }}
                </div>
            </div>
        </div>
    </span>
</span>
