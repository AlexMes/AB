@extends('crm::app')
@section('title', __('crm::common.labels'))
@section('content')
    <div class="mt-8">
        <div class="flex flex-col">
            <h3 class="container mx-auto text-center">{{ $label->name }}</h3>
            <div class="container mx-auto mt-8 flex flex-col {{ $label->assignments->isEmpty() ? "md:w-1/2 sm:w-full py-8" : "" }} bg-white shadow">
                @if($label->assignments->isEmpty())
                    <span class="text-sm text-gray-600 flex justify-center">
                        @lang('crm::label.no_label_leads_found')
                    </span>
                @else
                    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 max-w-8xl mx-auto">
                        <div
                            class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                            <table class="min-w-full">
                                <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.id')
                                    </th>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.offer')
                                    </th>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.name')
                                    </th>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.phone')
                                    </th>
                                    @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                                        <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            @lang('crm::lead.manager')
                                        </th>
                                    @endif
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.status')
                                    </th>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.assigned_at')
                                    </th>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.comment')
                                    </th>
                                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('crm::lead.form_labels')
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white">
                                @foreach($label->assignments as $assignment)
                                    <tr class="cursor-pointer hover:bg-gray-100"
                                        onclick="window.location = '{{ route('crm.assignments.show', $assignment->id) }}'">
                                        <td class="px-5 py-4 whitespace-no-wrap border-b border-gray-200 text-sm font-thin text-gray-500">
                                            {{ $assignment->id }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                            {{ $assignment->route->offer->name }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-no-wrap  border-b border-gray-200 text-sm font-medium text-gray-600">
                                            {{ \Illuminate\Support\Str::limit($assignment->lead->fullname, 40) }}
                                        </td>
                                        <td class="px-5 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-700">
                                            {{ $assignment->lead->phone }}
                                        </td>
                                        @if(auth('web')->check() || auth('crm')->user()->isOfficeHead() || auth('crm')->user()->isAdmin())
                                            <td class="px-5 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-700">
                                                {{ $assignment->route->manager->name }}
                                            </td>
                                        @endif
                                        <td class="px-5 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                            @component('crm::components.status-badge', ['status' => $assignment->status]) @endcomponent
                                        </td>
                                        <td class="px-5 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                            {{ $assignment->registered_at->format('M d, H:i:s') }}
                                        </td>
                                        <td class="px-5 py-4 border-b whitespace-normal border-gray-200 text-sm leading-5 text-gray-500 max-w-4xl">
                                            {{ $assignment->comment }}
                                        </td>
                                        <td class="px-5 py-4 border-b whitespace-normal border-gray-200 text-sm leading-5 text-gray-500 max-w-4xl">
                                            @foreach($assignment->labels as $label)
                                                <div class="p-2 bg-gray-200 rounded mr-2 my-2 text-center max-w-max-content">{{ $label->name }}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <div class="container mx-auto flex justify-end mt-12 {{ $label->assignments->isEmpty() ? "justify-center" : "" }}">
                <span class="inline-flex rounded-md shadow-sm">
                    <a href="{{ route('crm.labels.index') }}"
                       class="inline-flex items-center py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                        @lang('crm::label.to_labels_list')
                    </a>
                </span>
            </div>
        </div>
    </div>
@endsection
