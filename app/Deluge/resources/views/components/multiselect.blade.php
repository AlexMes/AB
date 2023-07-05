@php $multiple = isset($multiple) ? $multiple : true @endphp

<div class="flex-auto flex flex-col" x-on:click.away="close('{{ $id }}')">
    <div class="flex flex-col items-center relative" >
        <div class="w-full">
            <div class="my-2 bg-white p-1 flex border border-gray-200 rounded">
                <div class="flex flex-auto flex-wrap"></div>
                <input x-show="!isOpen('{{ $id }}')" x-on:click="show.{{ $id }} = !show.{{ $id }}" x-bind:value="selectedValues('{{ $id }}')" readonly class="p-1 px-2 appearance-none outline-none w-full text-gray-800">
                <input x-show="isOpen('{{ $id }}')" x-model="search.{{ $id }}" x-ref="search_{{ $id }}" class="p-1 px-2 appearance-none outline-none w-full text-gray-800">
                <div>
                    <button x-show="multiple.{{ $id }} || isOpen('{{ $id }}')" type="button" x-on:click.prevent="isOpen('{{ $id }}') ? search.{{ $id }}='' : removeAll('{{ $id }}')" class="cursor-pointer w-6 h-full flex items-center text-gray-400 outline-none focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x w-4 h-4">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="text-gray-300 w-8 py-1 pl-2 pr-1 border-l flex items-center border-gray-200">
                    <button type="button" x-show="!isOpen('{{ $id }}')" x-on:click.prevent="open('{{ $id }}')" class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-up w-4 h-4">
                            <polyline points="18 9 12 15 6 9"></polyline>
                        </svg>
                    </button>

                    <button type="button" x-show="isOpen('{{ $id }}')" x-on:click.prevent="close('{{ $id }}')" class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-up w-4 h-4">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="isOpen('{{ $id }}')" class="absolute shadow top-full z-40 w-full lef-0 rounded max-h-64 overflow-y-auto svelte-5uyqqj">
            <div class="flex flex-col w-full">
                <template x-for="(option,index) in options.{{ $id }}" :key="option">
                    <div x-show="!option.filtered">
                        <div @click="select('{{ $id }}',index,$event)" class="cursor-pointer w-full border-gray-100 rounded-t border-b hover:bg-indigo-100" style="">
                            <div x-bind:class="option.selected ? 'border-indigo-600' : ''" class="flex w-full items-center p-2 pl-2 border-transparent bg-white border-l-2 relative hover:bg-indigo-600 hover:text-indigo-100 hover:border-indigo-600">
                                <div class="w-full items-center flex">
                                    <div class="mx-2 leading-6" x-text="option.text"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<select id="{{ $id }}"
        x-model="filters.{{ $id }}"
        {{ $multiple ? 'multiple' : '' }}
        name="{{ $id }}{{ $multiple ? '[]' : ''}}"
        class="hidden fn-multiselect">
    @unless($multiple)
        <option value="" {{ empty($selected) ? 'selected' : '' }}>Не выбрано</option>
    @endunless
    @foreach($options as $option)
        @php $value = is_object($option) ? $option->$trackBy : $option @endphp
        @php $text = is_object($option) ? $option->$label : $option @endphp
        <option
            value="{{$value}}" {{ in_array($value, Arr::wrap($selected ?? request($id, []))) ? 'selected' : '' }}>{{ $text }}</option>
    @endforeach
</select>
