@extends('crm::layout')

@section('application')
    <div class="min-h-screen min-w-screen bg-gray-100">
        <div class="min-w-lg flex flex-col items-center">
            <div class="max-w-xl flex flex-col mt-8 bg-white shadow-md rounded">
                @foreach($poll as $result)
                <div class="pt-6 pb-4 text-left border-b px-5">
                    <p class="font-semibold">{{ $result->getQuestion() }}</p>
                    <p class="pt-3">{{ $result->getAnswer() }}</p>
                </div>
            @endforeach
            </div>
        </div>
    </div>

@endsection
