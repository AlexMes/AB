<div class="relative flex pb-4 overflow-x-auto">
    @if($apps->count() > 0)
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
                            Ссылка
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Статус
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Чаты
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Создан
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    @foreach($apps as $app)
                        <tr class="cursor-pointer hover:bg-gray-100">
                            <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.apps.show', $app->id) }}'">
                                {{ $app->id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.apps.show', $app->id) }}'">
                                {{ $app->link }}
                            </td>
                            <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.apps.show', $app->id) }}'">
                                @if($app->status === 'new')
                                    @php $color = 'gray' @endphp
                                @elseif($app->status === 'published')
                                    @php $color = 'green' @endphp
                                @else
                                    @php $color = 'red' @endphp
                                @endif
                                <span
                                    class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.apps.show', $app->id) }}'">
                                {{ $app->chat_id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.apps.show', $app->id) }}'">
                                {{ $app->created_at->format('M d, H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                    <div class="hidden sm:block">
                        <p class="text-sm leading-5 text-gray-700">
                            От
                            <span class="font-medium">{{ $apps->firstItem() }}</span>
                            до
                            <span class="font-medium">{{ $apps->lastItem() }}</span>
                            из
                            <span class="font-medium">{{ $apps->total() }}</span>
                            приложений
                        </p>
                    </div>
                    <div class="flex justify-between flex-1 sm:justify-end">
                        @if($apps->previousPageUrl())
                            <a href="{{ $apps->appends(request(['status']))->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                Предыдущая
                            </a>
                        @endif
                        @if($apps->nextPageUrl())
                            <a href="{{ $apps->appends(request(['status']))->nextPageUrl() }}"
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
            <p>Приложений не найдено</p>
        </div>
    @endif
</div>
