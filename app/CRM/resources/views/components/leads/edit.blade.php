@if($errors->any())
    <div class="rounded-md bg-red-100 mb-4 p-4 max-w-7xl mx-auto">
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

<div class="relative bg-white shadow overflow-hidden sm:rounded-md max-w-7xl mx-auto">
    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ sprintf('[#%d] %s ', $assignment->lead_id, $assignment->lead->fullname) }}
        </h3>
    </div>
    <form method="post"
          x-data="{
                status:'{{old('status', $assignment->status)}}',
                callback_at: '{{old('callback_at', optional($assignment->callback_at)->toDateTimeString('minute'))}}',
                callback_time: '{{optional($assignment->callback_at)->toTimeString('minute')}}',
                callback_date: '{{optional($assignment->callback_at)->toDateString()}}'
              }"
          x-init="
                $watch('callback_time', value => callback_at = callback_date + ' ' + callback_time);
                $watch('callback_date', value => callback_at = callback_date + ' ' + callback_time);
              "
          action="{{ route('crm.assignments.update', $assignment) }}" class="px-6">
        @method('PUT')
        @csrf
        <div
            class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-gray-200 sm:pt-5">
            <label for="lead_status" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Cтатус
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <select id="lead_status"
                            x-model="status"
                            name="status"
                            class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                        @foreach(\App\CRM\Status::all() as $status)
                            <option
                                value="{{$status['name']}}" {{ old('status', $assignment->status) === $status['name'] ? 'selected' : '' }}>{{$status['name']}}</option>
                        @endforeach
                    </select>
                </div>
                @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div x-show="status == 'Перезвон'"
             class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="callback_date" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Время перезвона
            </label>
            <input type="hidden" x-model="callback_at" name="callback_at">
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <input id="callback_date"
                           x-model="callback_date"
                           name="callback_date"
                           class="form-input block w-full sm:text-sm sm:leading-5"
                           type="date"/>
                </div>
                <div class="max-w-xs rounded-md shadow-sm mt-2">
                    <input id="callback_time"
                           x-model="callback_time"
                           name="callback_time"
                           class="form-input block w-full sm:text-sm sm:leading-5"
                           type="time"/>
                </div>
                @error('callback_at') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div x-show="status == 'Отказ'"
             class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="reject_reason" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Причина отказа
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <select id="reject_reason"
                            name="reject_reason"
                            class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                        <option value="">Выберите причину</option>
                        @foreach(\App\CRM\Reason::all() as $reason)
                            <option
                                value="{{$reason['name']}}" {{ old('reject_reason', $assignment->reject_reason) === $reason['name'] ? 'selected' : '' }}>{{$reason['name']}}</option>
                        @endforeach
                    </select>
                </div>
                @error('reject_reason') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div
            x-show="status == 'Депозит'"
            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="deposit_sum" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Сумма депозита
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                  <span class="text-gray-500 sm:text-sm sm:leading-5">
                                    $
                                  </span>
                        </div>
                        <input type="number" name="deposit_sum" id="deposit_sum"
                               value="{{ old('deposit_sum', $assignment->deposit_sum) }}"
                               class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5"
                               placeholder="0" aria-describedby="price-currency"/>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <span class="text-gray-500 sm:text-sm sm:leading-5" id="price-currency">
                                USD
                              </span>
                        </div>
                    </div>
                </div>
                @error('deposit_sum') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div
            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="alt_phone" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Альтернативный номер
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <input value="{{ old('alt_phone', $assignment->alt_phone) }}" id="alt_phone"
                           name="alt_phone" class="form-input block w-full sm:text-sm sm:leading-5"
                           type="tel"
                           maxlength="15"
                           placeholder="+7-937-99-92"/>
                </div>
                @error('alt_phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div
            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="gender_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Пол
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <select id="gender_id"
                            name="gender_id"
                            class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                        <option value="0" {{ $assignment->gender_id === 0 ? 'selected':'' }} >Неизвестный
                        </option>
                        <option value="1" {{ $assignment->gender_id === 1 ? 'selected':'' }} >Мужской</option>
                        <option value="2" {{ $assignment->gender_id === 2 ? 'selected':'' }} >Женский</option>
                    </select>
                </div>
                @error('gender_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div
            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="age" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Возраст
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <select id="age"
                            name="age"
                            class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                        <option value="">Неизвестно</option>
                        @foreach(\App\CRM\Age::all() as $age)
                            <option
                                value="{{$age['name']}}" {{ old('age', $assignment->age) === $age['name'] ? 'selected' : '' }}>{{$age['name']}}</option>
                        @endforeach
                    </select>
                </div>
                @error('age') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div
            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="profession" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Профессия
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-xs rounded-md shadow-sm">
                    <select id="profession"
                            name="profession"
                            class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                        <option value="">Неизвестно</option>
                        @foreach(\App\CRM\Profession::all() as $profession)
                            <option
                                value="{{$profession['name']}}" {{ old('profession', $assignment->profession) === $profession['name'] ? 'selected' : '' }}>{{$profession['name']}}</option>
                        @endforeach
                    </select>
                </div>
                @error('profession') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div
            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
            <label for="comment" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Комментарий
            </label>
            <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-lg flex rounded-md shadow-sm">
                        <textarea id="comment" name="comment" maxlength="1024" rows="3"
                                  placeholder="Something nice about the customer..."
                                  class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">{{ old('comment', $assignment->comment) }}</textarea>
                </div>
                @error('comment') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-8 border-t border-gray-200 py-5">
            <div class="flex justify-end">
                  <span class="inline-flex rounded-md shadow-sm">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> Отмена
                    </a>
                  </span>
                <span class="ml-3 inline-flex rounded-md shadow-sm">
                    <button type="submit"
                            class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition duration-150 ease-in-out">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> Сохранить
                    </button>
                  </span>
            </div>
        </div>
    </form>
</div>
