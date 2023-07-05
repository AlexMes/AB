@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 py-12 bg-gray-50 sm:px-6 lg:px-8">
        <div class="w-full max-w-md p-5 bg-white rounded-md shadow-md">
            <div>
                <div class="flex items-center justify-center hd:w-3/5 hd:self-center">
                    <svg class="w-16 h-16" viewBox="0 0 130 76" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M26.2301 61.3885C27.6748 64.1133 27.6192 68.3951 26.1745 70.953C24.7298 73.511 21.0625 75.5685 18.0064 75.5685H4.11496C1.05885 75.5685 -0.219162 73.3998 1.28111 70.7306L13.9501 47.9314C15.4504 45.2622 17.8397 45.2622 19.2288 47.987L26.2301 61.3885Z"
                            fill="#2C7A7B" />
                        <path
                            d="M67.6822 56.9399C67.6822 56.9399 66.4597 54.7155 65.015 52.0464L41.5663 8.28307C40.1216 5.6139 37.7323 5.55829 36.232 8.22747L29.5641 20.4612C28.0638 23.1303 28.1194 27.5234 29.6197 30.1925L52.2349 70.7862C53.7352 73.4554 57.4581 75.6241 60.5142 75.6241L71.3495 75.5685C74.4056 75.5685 77.0728 75.5685 77.3506 75.5685C77.5728 75.5685 76.5727 73.3441 75.128 70.675L70.4049 61.8333C68.9046 59.1086 67.7377 56.9399 67.6822 56.9399Z"
                            fill="#2C7A7B" />
                        <path
                            d="M120.636 18.4593C118.914 21.0172 115.191 23.0191 112.246 23.0191C109.301 23.0191 105.745 20.7392 104.3 18.07L97.632 5.83632C96.1873 3.16715 97.4653 0.998444 100.521 0.998444L126.582 1.22087C129.638 1.22087 130.749 3.33397 129.082 5.89193L120.636 18.4593Z"
                            fill="#ADD5D5" />
                        <path
                            d="M61.681 19.8495C61.681 19.8495 62.9034 22.0738 64.3481 24.743L87.7968 68.5063C89.2415 71.1754 91.6309 71.231 93.1311 68.5619L99.8546 56.3838C101.355 53.7146 101.299 49.3216 99.799 46.6524L77.1838 6.05876C75.6835 3.38959 71.9606 1.22089 68.9045 1.22089L58.0692 1.27649C55.0131 1.27649 52.3459 1.27649 52.0681 1.27649C51.7903 1.27649 52.846 3.5008 54.2907 6.16998L59.0138 15.0116C60.403 17.6252 61.6254 19.8495 61.681 19.8495Z"
                            fill="#ADD5D5" />
                    </svg>
                    <span class="mt-2 ml-1.5 text-3xl font-bold leading-9 text-center text-gray-600">
                        AdsBoard
                    </span>
                </div>
            </div>
            <form class="mt-8" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm">
                    <div>
                        <input aria-label="Email address" name="email" type="email" value="{{ old('email') }}"
                            required
                            class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-none appearance-none rounded-t-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                            placeholder="{{ __('E-Mail Address') }}">
                        @if ($errors->has('email'))
                            <p class="my-2 text-sm font-semibold text-red-700">{{ $errors->first('email') }}</p>
                        @endif
                    </div>
                    <div class="-mt-px">
                        <input aria-label="Password" name="password" type="password" required
                            class="appearance-none rounded-b-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5 {{ $errors->has('password') ? 'border-2 border-red-500' : '' }}"
                            placeholder="{{ __('Password') }}">
                        @if ($errors->has('password'))
                            <p class="mt-2 text-sm font-semibold text-red-700">{{ $errors->first('password') }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="relative flex justify-center w-full px-4 py-2 text-sm font-medium leading-5 text-white transition duration-150 ease-in-out bg-teal-700 border border-transparent rounded-md group hover:bg-teal-600 focus:outline-none focus:border-green-700 focus:shadow-outline-green active:bg-green-700">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-teal-500 transition duration-150 ease-in-out group-hover:text-teal-400"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
