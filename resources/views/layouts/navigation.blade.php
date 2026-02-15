<nav x-data="{ open: false }" class="bg-dark-800/70 backdrop-blur-lg border-b border-dark-700 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Right Side: Logo & Links -->
            <div class="flex gap-8 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-900/30 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-cream-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent hidden sm:block">
                            مدیریت گزارشات
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex lg:gap-2">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('dashboard') ? 'bg-primary-500/20 text-primary-400 border border-primary-500/30' : 'text-cream-300 hover:text-cream-100 hover:bg-dark-700' }}">
                        داشبورد
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('reports.*') ? 'bg-primary-500/20 text-primary-400 border border-primary-500/30' : 'text-cream-300 hover:text-cream-100 hover:bg-dark-700' }}">
                        گزارش‌ها
                    </a>

                    <a href="{{ route('partorders.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('partorders.*') ? 'bg-primary-500/20 text-primary-400 border border-primary-500/30' : 'text-cream-300 hover:text-cream-100 hover:bg-dark-700' }}">
                        سفارشات قطعات
                    </a>

                    <a href="{{ route('workrequests.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('workrequests.*') ? 'bg-primary-500/20 text-primary-400 border border-primary-500/30' : 'text-cream-300 hover:text-cream-100 hover:bg-dark-700' }}">
                        درخواست‌ها
                    </a>

                    @if(auth()->user()->isCEO())
                    <a href="{{ route('users.index') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300
                              {{ request()->routeIs('users.*') ? 'bg-primary-500/20 text-primary-400 border border-primary-500/30' : 'text-cream-300 hover:text-cream-100 hover:bg-dark-700' }}">
                        کاربران
                    </a>
                    @endif
                </div>
            </div>

            <!-- Left Side: User Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:gap-4">
                <!-- Role Badge -->
                <span class="badge badge-info">
                    @switch(auth()->user()->role)
                    @case('technician') تکنسین @break
                    @case('reception') پذیرش @break
                    @case('supply') تامین @break
                    @case('ceo') مدیر عامل @break
                    @endswitch
                </span>

                <!-- User Dropdown -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen"
                        class="flex items-center gap-3 px-4 py-2 rounded-xl bg-dark-700 hover:bg-dark-600 transition-all duration-300 border border-dark-600">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-sm">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-cream-100">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-cream-300 transition-transform duration-300" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen"
                        @click.away="dropdownOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-0 mt-2 w-56 rounded-xl bg-dark-800 border border-dark-700 shadow-2xl shadow-black/50 overflow-hidden"
                        style="display: none;">

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-cream-200 hover:bg-dark-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            پروفایل کاربری
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors border-t border-dark-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                خروج از حساب
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center lg:hidden">
                <button @click="open = !open" class="p-2 rounded-lg text-cream-300 hover:text-cream-100 hover:bg-dark-700 transition-all">
                    <svg class="h-6 w-6" :class="{'hidden': open, 'block': !open}" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{'block': open, 'hidden': !open}" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="lg:hidden border-t border-dark-700">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-500/20 text-primary-400' : 'text-cream-300' }}">
                داشبورد
            </a>
            <a href="{{ route('reports.index') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('reports.*') ? 'bg-primary-500/20 text-primary-400' : 'text-cream-300' }}">
                گزارش کار
            </a>
            <a href="{{ route('partorders.index') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('partorders.*') ? 'bg-primary-500/20 text-primary-400' : 'text-cream-300' }}">
                سفارشات قطعات
            </a>
            <a href="{{ route('workrequests.index') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('workrequests.*') ? 'bg-primary-500/20 text-primary-400' : 'text-cream-300' }}">
                گردش کار
            </a>
            @if(auth()->user()->isCEO())
            <a href="{{ route('users.index') }}" class="block px-4 py-3 rounded-lg text-base font-medium {{ request()->routeIs('users.*') ? 'bg-primary-500/20 text-primary-400' : 'text-cream-300' }}">
                کاربران
            </a>
            @endif
        </div>

        <div class="pt-4 pb-3 border-t border-dark-700">
            <div class="px-4 mb-3">
                <div class="text-base font-medium text-cream-100">{{ auth()->user()->name }}</div>
                <div class="text-sm text-cream-400">{{ auth()->user()->email }}</div>
            </div>
            <div class="space-y-1 px-4">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-cream-300 hover:text-cream-100 hover:bg-dark-700 rounded-lg">
                    پروفایل
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-right px-4 py-2 text-base font-medium text-red-400 hover:bg-red-500/10 rounded-lg">
                        خروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>