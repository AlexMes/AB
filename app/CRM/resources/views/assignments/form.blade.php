@extends('crm::app')
@section('title', __('crm::lead.lead_edit'))
@section('content')
    @if($errors->any() || ($assignment->status === 'Депозит' && !auth('web')->check()))
        <div class="rounded-md bg-red-100 mt-6 p-4 max-w-7xl mx-auto">
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
                        @if($assignment->status === 'Депозит' && !auth('web')->check())
                            @lang('crm::validation.status_deposit_blocked')
                        @else
                            @lang('crm::validation.common_error')
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-md mt-8 max-w-7xl mx-auto">
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
                callback_date: '{{optional($assignment->callback_at)->toDateString()}}',
                openConfirmation: false,
                blockConfirmation: true,
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
                <label for="status" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    @lang('crm::lead.status')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="status"
                                x-model="status"
                                name="status"
                                class="form-select block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option value="" hidden>@lang('crm::common.select_empty')</option>
                            @foreach(\App\CRM\Status::selectable()->get() as $status)
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
                    @lang('crm::lead.form_callback_at')
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
                    @lang('crm::lead.form_reject_reason')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="reject_reason"
                                name="reject_reason"
                                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option value="">@lang('crm::lead.select_reject_reason')</option>
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
                    @lang('crm::lead.form_deposit_sum')
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
                    @lang('crm::lead.form_alt_phone')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <input value="{{ old('alt_phone', $assignment->alt_phone) }}" id="alt_phone"
                               name="alt_phone" class="form-input block w-full sm:text-sm sm:leading-5"
                               type="tel"
                               maxlength="15"
                               @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
                               placeholder="+7-937-99-92"/>
                    </div>
                    @error('alt_phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="gender_id" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    @lang('crm::lead.form_gender')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="gender_id"
                                name="gender_id"
                                @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
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
                    @lang('crm::lead.form_age')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="age"
                                name="age"
                                @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
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
                    @lang('crm::lead.form_profession')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="profession"
                                name="profession"
                                @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
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
                <label for="timezone" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    @lang('crm::lead.timezone')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-xs rounded-md shadow-sm">
                        <select id="timezone"
                                name="timezone"
                                @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
                                class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                            <option value="">Неизвестно</option>
                            @foreach(\App\CRM\Timezone::all() as $timezone)
                                <option
                                    value="{{$timezone['name']}}" {{ old('timezone', $assignment->timezone) === $timezone['name'] ? 'selected' : '' }}>{{$timezone['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('timezone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="comment" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    @lang('crm::lead.comment')
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-lg flex rounded-md shadow-sm">
                        <textarea id="comment" name="comment" maxlength="1024" rows="3"
                                  placeholder="Something nice about the customer..."
                                  @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
                                  class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">{{ old('comment', $assignment->comment) }}</textarea>
                    </div>
                    @error('comment') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div
                class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                <label for="labels" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                    <a href="{{ route('crm.labels.index') }}">
                        @lang('crm::lead.form_labels')
                    </a>
                </label>
                <div class="mt-1 sm:mt-0 sm:col-span-2">
                    <div class="max-w-lg flex">
                        <div class="flex flex-col">
                            @foreach($labels as $label)
                                <div class="my-2 flex items-center">
                                    <input {{ in_array($label->id, old('labels', $assignment->labels->pluck('id')->toArray())) ? "checked" : "" }}
                                        type="checkbox" name="labels[]" value="{{$label->id}}"
                                        @if(auth('web')->check() && auth('web')->user()->isSupport()) disabled @endif
                                        class="mr-2" id="{{$label}}">
                                    <label for="{{$label}}" class="text-sm font-normal">
                                        {{$label->name}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('labels') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-8 border-t border-gray-200 py-5">
                <div class="flex justify-end">
                  <span class="inline-flex rounded-md shadow-sm">
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> @lang('crm::common.cancel')
                    </a>
                  </span>
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                    <button x-on:click.prevent="
                            if (status == 'Депозит') {
                                openConfirmation=true;
                                blockConfirmation=true;
                                setTimeout(() => blockConfirmation=false, 3000);
                            } else $el.submit();
                        "
                            class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition duration-150 ease-in-out">
                     <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path
                             d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                             clip-rule="evenodd" fill-rule="evenodd"></path></svg> @lang('crm::common.save')
                    </button>
                  </span>
                </div>
            </div>

            <div x-show="openConfirmation" class="fixed bottom-0 inset-x-0 px-4 pb-4 sm:inset-0 sm:flex sm:items-center sm:justify-center" style="display: none;">
                <div x-show="openConfirmation" x-description="Background overlay, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div x-show="openConfirmation" x-description="Modal panel, show/hide based on modal state." x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative bg-white rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
                        <button @click="openConfirmation = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150" aria-label="Close">
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
                                    @lang('crm::lead.deposit_creation_confirmation_title')
                                </h3>
                                <div class="mt-2">
                                    <div class="mr-4 w-full">
                                        @lang('crm::lead.deposit_creation_confirmation_text')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button x-bind:disabled="blockConfirmation" type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            @lang('crm::common.confirm')
                          </button>
                        </span>
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                          <button @click="openConfirmation = false" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            @lang('crm::common.cancel')
                          </button>
                        </span>
                        </div>
                    </form>
                </div>
            </div>
        </form>
    </div>
@endsection
