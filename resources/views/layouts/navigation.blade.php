<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-20 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Trang chủ') }}
                    </x-nav-link>

                    @auth
                    {{-- HỌC VIÊN --}}
                    @if(Auth::user()->role === 'student')
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Khóa học của tôi') }}
                    </x-nav-link>

                    {{-- GIÁO VIÊN --}}
                    @elseif(Auth::user()->role === 'teacher')
                    <x-nav-link :href="route('teacher.courses.index')" :active="request()->routeIs('teacher.courses.*')">
                        {{ __('Quản lý Khóa học') }}
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.revenue.index')" :active="request()->routeIs('teacher.revenue.*')">
                        {{ __('Doanh thu') }}
                    </x-nav-link>

                    {{-- ADMIN --}}
                    @elseif(Auth::user()->role === 'admin')
                    <x-nav-link :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions.*')">
                        {{ __('Quản lý Dòng tiền') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                        {{ __('Duyệt Khóa học') }}
                    </x-nav-link>
                    @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex flex-col items-end">
                                <div class="font-bold">{{ Auth::user()->name }}</div>

                                {{-- 👇 HIỂN THỊ SỐ DƯ VÍ CHO ADMIN & TEACHER --}}

                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Đăng nhập') }}</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Đăng ký') }}</a>
                    @endif
                </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Trang chủ') }}
            </x-responsive-nav-link>

            @auth
            @if(Auth::user()->role === 'student')
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Khóa học của tôi') }}
            </x-responsive-nav-link>
            @elseif(Auth::user()->role === 'teacher')
            <x-responsive-nav-link :href="route('teacher.courses.index')" :active="request()->routeIs('teacher.courses.*')">
                {{ __('Quản lý Khóa học') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('teacher.revenue.index')" :active="request()->routeIs('teacher.revenue.*')">
                {{ __('Doanh thu') }}
            </x-responsive-nav-link>
            @elseif(Auth::user()->role === 'admin')
            <x-responsive-nav-link :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions.*')">
                {{ __('Quản lý Dòng tiền') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                {{ __('Duyệt Khóa học') }}
            </x-responsive-nav-link>
            @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>

                {{-- 👇 HIỂN THỊ SỐ DƯ VÍ TRÊN MOBILE --}}
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'teacher')
                <div class="mt-1 text-sm text-green-600 font-bold">
                    Ví: {{ number_format(Auth::user()->account_balance) }} đ
                </div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
            @else
            <div class="mt-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('login')">{{ __('Log in') }}</x-responsive-nav-link>
                @if (Route::has('register'))
                <x-responsive-nav-link :href="route('register')">{{ __('Register') }}</x-responsive-nav-link>
                @endif
            </div>
            @endauth
        </div>
    </div>
</nav>
