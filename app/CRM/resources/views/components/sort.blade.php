<div>
    <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'desc' => request('sort', $defaultKey) === $key && !request('desc', false)]) }}" class="text-gray-400 inline-flex">
        <span class="text-gray-500 font-medium mr-1">{{ $header }}</span>
        <span>
            <svg aria-hidden="true"
                 width="14"
                 height="14"
                 focusable="false"
                 data-prefix="fas"
                 data-icon="sort-up"
                 class="-mb-3 svg-inline--fa fa-sort-up fa-w-10 hover:text-teal-500 {{ request('sort', $defaultKey) === $key && !request('desc', false) ? 'text-teal-600' : ''}}"
                 role="img"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 320 512"
            >
                <path fill="currentColor" d="M279 224H41c-21.4 0-32.1-25.9-17-41L143 64c9.4-9.4 24.6-9.4 33.9 0l119 119c15.2 15.1 4.5 41-16.9 41z"></path>
            </svg>
            <svg aria-hidden="true"
                 width="14"
                 height="14"
                 focusable="false"
                 data-prefix="fas"
                 data-icon="sort-down"
                 class="-mt-3 svg-inline--fa fa-sort-down fa-w-10 hover:text-teal-500 {{ request('sort', $defaultKey) === $key && request('desc', false) ? 'text-teal-600' : ''}}"
                 role="img"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 320 512"
            >
                <path fill="currentColor" d="M41 288h238c21.4 0 32.1 25.9 17 41L177 448c-9.4 9.4-24.6 9.4-33.9 0L24 329c-15.1-15.1-4.4-41 17-41z"></path>
            </svg>
        </span>
    </a>
</div>
