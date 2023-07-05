<!doctype html>
<html class="overflow-x-hidden" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gamble | Login</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="h-screen overflow-x-hidden font-sans antialiased leading-none bg-gray-100">
<div>
    <div class="min-h-screen min-w-screen">
        <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="sm:mx-auto sm:w-full sm:max-w-md">
                        <h2 class="my-6 text-center text-2xl leading-9 font-bold text-gray-900">
                            Sign in to your account
                        </h2>
                    </div>
                    @error('access')
                    <div class="rounded-md bg-red-100 my-6 p-4 max-w-7xl mx-auto">
                        <p class="text-sm leading-5 font-medium text-red-800">
                            {{ $message }}
                        </p>
                    </div>
                    @enderror
                    <form action="{{ route('gamble.authenticate') }}" method="POST">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium leading-5 text-gray-700">
                                Email address
                            </label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <input id="email" name="email" value="{{ old('email') }}" placeholder="user@domain.tld" type="email" required
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"/>
                            </div>
                            @error('email')
                            <p class="text-red-500 text-xs mt-3">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <label for="password" class="block text-sm font-medium leading-5 text-gray-700">
                                Password
                            </label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <input id="password" name="password" placeholder="******" type="password" required
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"/>
                            </div>
                            @error('email')
                            <p class="text-red-500 text-xs mt-3">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="mt-6">
                              <span class="block w-full rounded-md shadow-sm">
                                <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                                  Sign in
                                </button>
                              </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
</body>
</html>
