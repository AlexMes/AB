@extends('crm::app')
@section('title', __('crm::common.labels'))
@section('content')
    <div class="mt-8">
        <div class="flex flex-col">
            <div class="container mx-auto lg:w-1/2 sm:w-full">
                <div class="w-full flex justify-end">
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                        <a href="{{ route('crm.labels.create') }}"
                           class="inline-flex justify-center items-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-teal-600 hover:bg-teal-500 focus:outline-none focus:border-teal-700 focus:shadow-outline-teal active:bg-teal-700 transition duration-150 ease-in-out">
                            <svg viewBox="0 0 128 128" width="20px" height="20px" fill="white" class="mr-4">
                                <path
                                    d="M105,23C105,23,105,23,105,23C82.4,0.4,45.6,0.4,23,23C0.4,45.6,0.4,82.4,23,105c11.3,11.3,26.2,17,41,17s29.7-5.7,41-17C127.6,82.4,127.6,45.6,105,23z M100.8,100.8c-20.3,20.3-53.3,20.3-73.5,0C7,80.5,7,47.5,27.2,27.2C37.4,17.1,50.7,12,64,12s26.6,5.1,36.8,15.2C121,47.5,121,80.5,100.8,100.8z"/><path
                                    d="M83,61H67V45c0-1.7-1.3-3-3-3s-3,1.3-3,3v16H45c-1.7,0-3,1.3-3,3s1.3,3,3,3h16v16c0,1.7,1.3,3,3,3s3-1.3,3-3V67h16c1.7,0,3-1.3,3-3S84.7,61,83,61z"/>
                            </svg>
                            @lang('crm::label.add')
                        </a>
                    </span>
                </div>
                <div class="mt-8 flex flex-col w-full bg-white shadow">
                    @if($labels->isEmpty())
                        <span class="flex w-auto justify-center text-center mt-20 py-4 bg-white shadow">
                            @lang('crm::label.no_labels_found')
                        </span>
                    @else
                        @foreach($labels as $label)
                            <div class="flex w-full bg-white p-4 flex justify-between items-center border-b">
                                <div
                                    class="w-1/12 px-4 text-md text-justify text-gray-500"
                                >
                                    <a href="{{ route('crm.labels.show', $label) }}"
                                       class="font-normal">#{{$label->id}}</a>
                                </div>
                                <div class="w-1/2 flex pl-12">
                                    <a href="{{ route('crm.labels.show', $label) }}"
                                       class="font-normal">{{$label->name}}</a>
                                </div>
                                <div class="w-1/12 flex justify-end">
                                    <form action="{{ route('crm.labels.destroy', $label->id) }}"
                                          method="POST"
                                          class="flex">
                                        <a href="{{ route('crm.labels.edit', $label) }}">
                                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round"
                                                 stroke-width="1" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn">
                                            <svg class="h-5 w-5 text-gray-500" fill="none"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round"
                                                 stroke-width="1" stroke="currentColor" viewBox="0 0 32 32">
                                                <path
                                                    d="M23 27H11c-1.1 0-2-.9-2-2V8h16v17C25 26.1 24.1 27 23 27zM27 8L7 8M14 8V6c0-.6.4-1 1-1h4c.6 0 1 .4 1 1v2M17 23L17 12M21 23L21 12M13 23L13 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
