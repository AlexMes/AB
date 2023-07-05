<div class="relative flex pb-4 overflow-x-auto">
    @if($organizations->count() > 0)
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
                            Название
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Organization core ID
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Organization ID
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Создан
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    @foreach($organizations as $organization)
                        <tr class="cursor-pointer hover:bg-gray-100">
                            <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.unity.organizations.show', $organization->id) }}'">
                                {{ $organization->id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.unity.organizations.show', $organization->id) }}'">
                                {{ $organization->name }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.unity.organizations.show', $organization->id) }}'">
                                {{ $organization->organization_core_id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.unity.organizations.show', $organization->id) }}'">
                                {{ $organization->organization_id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.unity.organizations.show', $organization->id) }}'">
                                {{ $organization->created_at->format('M d, H:i:s') }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                @if($organization->has_issues)
                                    <div class="flex">
                                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        @can('update', $organization)
                                            <form action="{{ route('deluge.unity.organizations.refresh', $organization) }}" method='post'>
                                                @csrf
                                                <button type="submit" class="focus:outline-none">
                                                    <svg class="w-6 h-6 text-green-500 hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                    <div class="hidden sm:block">
                        <p class="text-sm leading-5 text-gray-700">
                            От
                            <span class="font-medium">{{ $organizations->firstItem() }}</span>
                            до
                            <span class="font-medium">{{ $organizations->lastItem() }}</span>
                            из
                            <span class="font-medium">{{ $organizations->total() }}</span>
                            организаций
                        </p>
                    </div>
                    <div class="flex justify-between flex-1 sm:justify-end">
                        @if($organizations->previousPageUrl())
                            <a href="{{ $organizations->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                Предыдущая
                            </a>
                        @endif
                        @if($organizations->nextPageUrl())
                            <a href="{{ $organizations->nextPageUrl() }}"
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
            <p>Организаций не найдено</p>
        </div>
    @endif
</div>
