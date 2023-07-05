<div class="relative flex pb-4 overflow-x-auto">
    @if($accounts->count() > 0)
        <div class="flex-shrink-0 w-full py-2 mx-auto -my-2 overflow-x-auto max-w-8xl">
            <div
                class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                <table class="min-w-full">
                    <thead>
                    <tr>
                        @if(Request::is('accounts*'))
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            </th>
                        @endif
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            ID
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Название
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Account ID
                        </th>
                        @if(auth('web')->user()->isAdmin())
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                Байер
                            </th>
                        @endif
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Spend
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Статус
                        </th>
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Статус модерации
                        </th>
                        @if(Request::is('pours*'))
                            <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            </th>
                        @endif
                        <th class="px-5 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                            Создан
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    @foreach($accounts as $account)
                        <tr class="cursor-pointer hover:bg-gray-100"
                            @if(Request::is('pours*'))
                                x-data="{
                                    status:'{{ $account->pivot->status }}',
                                    moderation_status:'{{ $account->pivot->moderation_status }}',
                                }"
                            @endif
                        >
                            @if(Request::is('accounts*'))
                                <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <input type="checkbox"
                                           class="mr-2"
                                           x-model="accountsToPour"
                                           name="accounts[]"
                                           value="{{ $account->account_id }}"
                                           @if(in_array($account->account_id, old('accounts', []))) checked @endif
                                           @click.stop="" />
                                </td>
                            @endif
                            <td class="px-5 py-5 text-sm font-thin text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'">
                                {{ $account->id }}
                            </td>
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'">
                                {{ $account->name }}
                            </td>
                            <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'">
                                {{ $account->account_id }}
                            </td>
                            @if(auth('web')->user()->isAdmin())
                                <td class="px-5 py-5 text-sm leading-5 text-gray-700 whitespace-no-wrap border-b border-gray-200"
                                    onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'">
                                    {{ optional($account->user)->name }}
                                </td>
                            @endif
                            <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'">
                                {{ $account->spend ?? 0 }} $
                            </td>
                            <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200"
                                @if(!Request::is('pours*')) onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'" @endif>
                                @if(Request::is('pours*'))
                                    <select
                                        x-model="status"
                                        class="form-select block w-auto transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                        <option value="active">Активный</option>
                                        <option value="blocked">Заблокирован</option>
                                    </select>
                                @else
                                    @php $color = $account->status=='active' ? 'green' : 'red' @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $account->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-5 text-sm font-medium text-gray-600 whitespace-no-wrap border-b border-gray-200"
                                @if(!Request::is('pours*')) onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'" @endif>
                                @if(Request::is('pours*'))
                                    <select
                                        x-model="moderation_status"
                                        class="form-select block w-auto transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                        <option value="review">На рассмотрении</option>
                                        <option value="approved">Аппрув</option>
                                        <option value="disapproved">Дизаппрув</option>
                                    </select>
                                @else
                                    @if($account->moderation_status=='review')
                                        @php $color = 'gray' @endphp
                                    @elseif($account->moderation_status=='approved')
                                        @php $color = 'green' @endphp
                                    @else
                                        @php $color = 'red' @endphp
                                    @endif
                                    <span
                                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $account->moderation_status }}
                                    </span>
                                @endif
                            </td>
                            @if(Request::is('pours*'))
                                    <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200">
                                    <form method="post" action="{{ route('deluge.account-pour.update', $account->pivot) }}">
                                        @method('put')
                                        @csrf
                                        <input name="status" type="hidden" :value="status">
                                        <input name="moderation_status" type="hidden" :value="moderation_status">
                                        <button type="submit"
                                                class="inline-flex justify-center items-center py-2 px-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            @endif
                            <td class="px-5 py-5 text-sm leading-5 text-gray-500 whitespace-no-wrap border-b border-gray-200"
                                onclick="window.location = '{{ route('deluge.accounts.campaigns.index', $account->id) }}'">
                                {{ $account->created_at->format('M d, H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <nav class="flex items-center justify-between px-4 py-3 bg-white border-gray-200 sm:px-6">
                    <div class="hidden sm:block">
                        <p class="text-sm leading-5 text-gray-700">
                            От
                            <span class="font-medium">{{ $accounts->firstItem() }}</span>
                            до
                            <span class="font-medium">{{ $accounts->lastItem() }}</span>
                            из
                            <span class="font-medium">{{ $accounts->total() }}</span>
                            аккаунтов
                        </p>
                    </div>
                    <div class="flex justify-between flex-1 sm:justify-end">
                        @if($accounts->previousPageUrl())
                            <a href="{{ $accounts->appends(request(['user', 'moderation_status', 'group']))->previousPageUrl() }}"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700">
                                Предыдущая
                            </a>
                        @endif
                        @if($accounts->nextPageUrl())
                            <a href="{{ $accounts->appends(request(['user', 'moderation_status', 'group']))->nextPageUrl() }}"
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
            <p>Аккаунтов не найдено</p>
        </div>
    @endif
</div>
