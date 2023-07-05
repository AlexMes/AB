<div class="relative flex pb-4 overflow-x-auto">
    @if($campaigns->count() > 0)
        <div class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto max-w-8xl">
            <div
                class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Кампания
                            </th>
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Приложение
                            </th>
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Организация
                            </th>
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Цель
                            </th>
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Активна
                            </th>
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Создан
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($campaigns as $campaign)
                            <tr class="cursor-pointer hover:bg-gray-100"
                                onclick="window.location = '{{ route('deluge.unity.campaigns.show', $campaign->id) }}'">
                                <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <div class="font-semibold">{{ $campaign->name }}</div>
                                    <div class="text-xs text-gray-600">{{ $campaign->id }}</div>
                                </td>
                                <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <div class="font-semibold">{{ $campaign->app->name }}</div>
                                    <div class="text-xs text-gray-600">{{ $campaign->app_id }}</div>
                                </td>
                                <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ $campaign->organization->name }}
                                </td>
                                <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ $campaign->goal }}
                                </td>
                                <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <span
                                        class="inline-flex px-2 py-2 rounded-full @if($campaign->enabled) bg-green-500 @else bg-red-500 @endif"
                                    ></span>
                                </td>
                                <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    {{ $campaign->created_at->format('M d, H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                    <div class="hidden sm:block">
                        <p class="text-sm leading-5 text-gray-700">
                            От
                            <span class="font-medium">{{ $campaigns->firstItem() }}</span>
                            до
                            <span class="font-medium">{{ $campaigns->lastItem() }}</span>
                            из
                            <span class="font-medium">{{ $campaigns->total() }}</span>
                            кампаний
                        </p>
                    </div>
                    <div class="flex justify-between flex-1 sm:justify-end">
                        @if($campaigns->previousPageUrl())
                            <a href="{{ $campaigns->appends(request(['organization', 'app']))->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                Предыдущая
                            </a>
                        @endif
                        @if($campaigns->nextPageUrl())
                            <a href="{{ $campaigns->appends(request(['organization', 'app']))->nextPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                Следующая
                            </a>
                        @endif
                    </div>
                </nav>
            </div>
        </div>
    @else
        <div class="flex items-center justify-center flex-shrink-0 w-full p-6 text-center bg-white rounded shadow">
            <p>Кампаний не найдено</p>
        </div>
    @endif
</div>
