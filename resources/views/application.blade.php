@extends('layouts.app')

@section('content')
    <scroll-to-top></scroll-to-top>
    @auth
        <application-menu ref="auth" class="mb-8" :user="{{ auth()->user()->loadCount('unreadNotifications') }}"></application-menu>
    @endauth
    <v-dialog></v-dialog>

    <div class="mb-32 mx-4 md:mx-12">
        <router-view></router-view>
    </div>
@endsection
