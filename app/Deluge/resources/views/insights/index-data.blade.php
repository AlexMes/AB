<div class="relative flex pb-4 overflow-x-auto">
    @if($insights->count() > 0)
        <div class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto max-w-8xl">
            <div
                class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                <table class="min-w-full">
                    <thead>
                    <tr>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            ID
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Дата
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Аккаунт
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Кампания
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Показы
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Клики
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Кост
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Лиды
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    @foreach($insights as $insight)
                        <tr class="cursor-pointer hover:bg-gray-100"
                            onclick="window.location = '{{ route('deluge.insights.show', $insight->id) }}'">
                            <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                {{ $insight->id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                {{ $insight->date->format('d.m.Y') }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                <div class="font-semibold">{{ optional($insight->account)->name }}</div>
                                <div class="text-xs text-gray-600">{{ $insight->account_id }}</div>
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                <div class="font-semibold">{{ optional($insight->campaign)->name }}</div>
                                <div class="text-xs text-gray-600">{{ $insight->campaign_id }}</div>
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                {{ $insight->impressions }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                {{ $insight->clicks }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                {{ $insight->spend }} $
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200">
                                {{ $insight->leads_cnt }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                    <div class="hidden sm:block">
                        <p class="text-sm leading-5 text-gray-700">
                            От
                            <span class="font-medium">{{ $insights->firstItem() }}</span>
                            до
                            <span class="font-medium">{{ $insights->lastItem() }}</span>
                            из
                            <span class="font-medium">{{ $insights->total() }}</span>
                            записей
                        </p>
                    </div>
                    <div class="flex justify-between flex-1 sm:justify-end">
                        @if($insights->previousPageUrl())
                            <a href="{{ $insights->appends(request(['account']))->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                Предыдущая
                            </a>
                        @endif
                        @if($insights->nextPageUrl())
                            <a href="{{ $insights->appends(request(['account']))->nextPageUrl() }}"
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
            <p>Записей не найдено</p>
        </div>
    @endif
</div>
