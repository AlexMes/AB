@if($errors->any())
    <div class="rounded-md bg-red-100 mb-4 p-4 max-w-7xl mx-auto sticky">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                          clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 font-medium text-red-800">
                    Ошибка валидации данных. Проверьте информацию, и попробуйте ещё раз.
                </p>
            </div>
        </div>
    </div>
@endif

<div x-data="{openTransfer: @if($errors->has('manager_id')) true @else false @endif}" class="min-w-screen-75 relative bg-white shadow overflow-hidden sm:rounded-lg max-w-7xl mx-auto">
    <div class="px-4 py-5 border-b border-gray-200 flex justify-between items-center sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ sprintf('[#%d] %s ',$assignment->lead_id, $assignment->lead->fullname) }}
            @component('crm::components.status-badge', ['status' => $assignment->status]) @endcomponent
        </h3>

        <div>
            @can('transfer', $assignment)
                <a href="#" @click.prevent="openTransfer = true"
                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none"
                         stroke-width="1.3" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21.883 12l-7.527 6.235.644.765 9-7.521-9-7.479-.645.764 7.529 6.236h-21.884v1h21.883z"/>
                    </svg>
                    <span>
                            Передать
                        </span>
                </a>
            @endcan

            @can('update', $assignment)
                <button x-data="CardEditor()" @click="edit"
                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-50 active:text-gray-800">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke-linecap="round"
                         stroke-linejoin="round"
                         stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>
                            Редактировать
                        </span>
                </button>
            @endcan

            <span x-data="CardCloser()" class="absolute top-0 left-0 cursor-pointer text-white hover:text-teal-600" @click="closeCard">
                <svg viewBox="0 0 20 20" fill="currentColor" class="x-circle w-6 h-6"><path class="stroke-gray-400" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            </span>
        </div>
    </div>
    <div class="px-4 py-5 sm:p-0">
        <dl>
            <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-gray-200 sm:px-6 sm:py-5">
                <dt class="text-sm leading-5 font-medium text-gray-500">
                    Номер телефона
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                    <span class="inline-block">{{ $assignment->lead->formatted_phone }}</span>
                    @auth('crm')
                        <a href="{{ route('crm.assignment.call', $assignment) }}"
                           class="inline-block cursor-pointer">
                            <svg class="ml-2 -mb-1 h-4 w-4 text-gray-500 hover:text-teal-600" fill="currentColor"
                                 viewBox="0 0 20 20">
                                <path
                                    d="M17.924 2.617a.997.997 0 00-.215-.322l-.004-.004A.997.997 0 0017 2h-4a1 1 0 100 2h1.586l-3.293 3.293a1 1 0 001.414 1.414L16 5.414V7a1 1 0 102 0V3a.997.997 0 00-.076-.383z"></path>
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                            </svg>
                        </a>
                    @endauth
                </dd>
            </div>
            @if($assignment->hasAltPhone())
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Альтернативный номер
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-block">{{ $assignment->formatted_alt_phone }}</span>
                        @auth('crm')
                            <a href="{{ route('crm.assignment.call-alt', $assignment) }}"
                               class="inline-block cursor-pointer">
                                <svg class="ml-2 -mb-1 h-4 w-4 text-gray-500 hover:text-teal-600"
                                     fill="currentColor"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M17.924 2.617a.997.997 0 00-.215-.322l-.004-.004A.997.997 0 0017 2h-4a1 1 0 100 2h1.586l-3.293 3.293a1 1 0 001.414 1.414L16 5.414V7a1 1 0 102 0V3a.997.997 0 00-.076-.383z"></path>
                                    <path
                                        d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                            </a>
                        @endauth
                    </dd>
                </div>
            @endif
            @if($assignment->lead->ipAddress)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Регион регистрации
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2 flex justify-between">
                        <span>{{ $assignment->lead->ipAddress->country_name }}, {{ $assignment->lead->ipAddress->city. ' ('.$assignment->lead->ipAddress->region.')' }}</span>
                        <span class="text-xs text-gray-600">
                               Время: {{ now()->setTimezone($assignment->lead->ipAddress->timezone)->format('H:i:s') }}
                            </span>
                    </dd>
                </div>
            @endif
            @if($assignment->reject_reason)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Причина отказа
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{$assignment->reject_reason }}
                    </dd>
                </div>
            @endif
            @if($assignment->deposit_sum)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Сумма депозита
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{sprintf("%s \$",$assignment->deposit_sum) }}
                    </dd>
                </div>
            @endif
            <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                <dt class="text-sm leading-5 font-medium text-gray-500">
                    Пол
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $assignment->gender }}
                </dd>
            </div>
            @if($assignment->age)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Возраст
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{$assignment->age ?? 'N/A' }}
                    </dd>
                </div>
            @endif
            @if($assignment->profession)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Профессия
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ Str::ucfirst($assignment->profession) }}
                    </dd>
                </div>
            @endif
            @if($assignment->comment)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Комментарий
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2 text-wrap">
                        {{ $assignment->comment }}
                    </dd>
                </div>
            @endif
            @if(auth('web')->check())
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Офис
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $assignment->getManager()->office->name  }}
                    </dd>
                </div>
            @endif
            @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Менеджер
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $assignment->getManager()->name  }}
                    </dd>
                </div>
            @endif
            <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                <dt class="text-sm leading-5 font-medium text-gray-500">
                    Лендинг
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                    <a target="_blank" class="hover:text-teal-600"
                       href="https://{{$assignment->lead->domain}}">{{ $assignment->lead->domain }}</a>
                </dd>
            </div>
            <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                <dt class="text-sm leading-5 font-medium text-gray-500">
                    Дата и время регистрации
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $assignment->registered_at->format('M d, H:i:s') }}
                </dd>
            </div>
            <div
                class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                <dt class="text-sm leading-5 font-medium text-gray-500">
                    Дата и время назначения
                </dt>
                <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                    {{ $assignment->created_at->format('M d, H:i:s') }}  <span class="ml-4 text-sm text-gray-600">({{ $assignment->created_at->diffForHumans($assignment->registered_at) . ' регистрации' }})</span>
                </dd>
            </div>
            @if((auth('web')->check() || auth('crm')->user()->isOfficeHead()) && $assignment->called_at !== null)
                <div
                    class="mt-8 sm:mt-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:border-t sm:border-gray-200 sm:px-6 sm:py-5">
                    <dt class="text-sm leading-5 font-medium text-gray-500">
                        Дата и время набора
                    </dt>
                    <dd class="mt-1 text-sm leading-5 text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $assignment->called_at->format('M d, H:i:s') }} <span class="ml-4 text-sm text-gray-600">({{ $assignment->called_at->diffForHumans($assignment->created_at) . ' назначения' }})</span>
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    <div x-show="openTransfer" class="fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
        <div x-show="openTransfer" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div x-show="openTransfer" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                <button @click="openTransfer = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150" aria-label="Close">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="post" action="{{ route('crm.assignment.transfer', $assignment) }}">
                @csrf
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                            Передача лида
                        </h3>
                        <div class="mt-2">
                            <div class="mr-4 w-full">
                                <label for="targetManager" class="block text-sm font-medium leading-5 text-gray-700">
                                    Выберите менеджера
                                </label>
                                <div class="mt-1 rounded-md shadow-sm">
                                    <select id="targetManager"
                                            name="manager_id"
                                            class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                        @foreach($assignment->route->manager->office->managers as $manager)
                                            @if($manager->id != $assignment->route->manager_id)
                                                <option
                                                    value="{{$manager->id}}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @error('manager_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Передать
                          </button>
                        </span>
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                          <button @click="openTransfer = false" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Отмена
                          </button>
                        </span>
                </div>
            </form>
        </div>
    </div>
</div>
