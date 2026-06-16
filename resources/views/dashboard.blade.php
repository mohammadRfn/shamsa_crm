<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header -->
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                    داشبورد
                </h1>
                <p class="text-dark-400 mt-2">خوش آمدید {{ auth()->user()->name }} 👋</p>
            </div>

            @php
            $user = auth()->user();

            // آمار کلی
            $totalReports = \App\Models\Report::forRole($user->role)->count();
            $pendingReports = \App\Models\Report::forRole($user->role)->pending()->count();
            $approvedReports = \App\Models\Report::forRole($user->role)->approved()->count();
            $rejectedReports = \App\Models\Report::forRole($user->role)->rejected()->count();

            $totalPartOrders = \App\Models\PartOrder::forRole($user->role)->count();
            $pendingPartOrders = \App\Models\PartOrder::forRole($user->role)->pending()->count();
            $approvedPartOrders = \App\Models\PartOrder::forRole($user->role)->approved()->count();
            $rejectedPartOrders = \App\Models\PartOrder::forRole($user->role)->rejected()->count();

            $totalWorkRequests = \App\Models\WorkRequest::forRole($user->role)->count();
            $pendingWorkRequests = \App\Models\WorkRequest::forRole($user->role)->pending()->count();
            $approvedWorkRequests = \App\Models\WorkRequest::forRole($user->role)->approved()->count();
            $rejectedWorkRequests = \App\Models\WorkRequest::forRole($user->role)->rejected()->count();

            // گزارش‌های اخیر
            $recentReports = \App\Models\Report::forRole($user->role)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();
            @endphp

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Reports -->
                <div class="card-luxury p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-dark-400 mb-1">گزارش کار</p>
                            <p class="text-3xl font-bold text-cream-100">{{ $totalReports }}</p>
                        </div>
                        <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-yellow-400 font-medium">{{ $pendingReports }}</span>
                        <span class="text-dark-400 mr-2">در انتظار</span>
                    </div>
                </div>

                <!-- Approved Reports -->
                <div class="card-luxury p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-dark-400 mb-1">تایید شده</p>
                            <p class="text-3xl font-bold text-green-400">{{ $approvedReports + $approvedPartOrders + $approvedWorkRequests }}</p>
                        </div>
                        <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-400 font-medium">{{ ($totalReports+$totalPartOrders+$totalWorkRequests) > 0 ? round((($approvedReports+$approvedPartOrders+$approvedWorkRequests) / ($totalReports+$totalPartOrders+$totalWorkRequests)) * 100) : 0 }}%</span>
                        <span class="text-dark-400 mr-2">از کل</span>
                    </div>
                </div>

                <!-- Part Orders -->
                <div class="card-luxury p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-dark-400 mb-1">سفارش قطعات</p>
                            <p class="text-3xl font-bold text-cream-100">{{ $totalPartOrders }}</p>
                        </div>
                        <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-yellow-400 font-medium">{{ $pendingPartOrders }}</span>
                        <span class="text-dark-400 mr-2">در انتظار</span>
                    </div>
                </div>

                <!-- Work Requests -->
                <div class="card-luxury p-6 hover:scale-105 transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-dark-400 mb-1">گردش کار</p>
                            <p class="text-3xl font-bold text-cream-100">{{ $totalWorkRequests }}</p>
                        </div>
                        <div class="w-14 h-14 bg-orange-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-yellow-400 font-medium">{{ $pendingWorkRequests }}</span>
                        <span class="text-dark-400 mr-2">در انتظار</span>
                    </div>
                </div>
            </div>

            <!-- Chart Section & Quick Actions (دو ستونه) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Status Distribution -->
                <div class="card-luxury p-6">
                    <h3 class="text-lg font-bold text-cream-100 mb-6">توزیع وضعیت گزارش‌ها</h3>
                    <div class="space-y-4">
                        @php
                        $statusData = [
                        ['label' => 'در انتظار', 'value' => $pendingReports + $pendingPartOrders + $pendingWorkRequests, 'color' => 'bg-yellow-500', 'text' => 'text-yellow-400'],
                        ['label' => 'تایید شده', 'value' => $approvedReports + $approvedPartOrders + $approvedWorkRequests, 'color' => 'bg-green-500', 'text' => 'text-green-400'],
                        ['label' => 'رد شده', 'value' => $rejectedReports + $rejectedPartOrders + $rejectedWorkRequests, 'color' => 'bg-red-500', 'text' => 'text-red-400'],
                        ];
                        @endphp

                        @foreach($statusData as $status)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-cream-200">{{ $status['label'] }}</span>
                                <span class="text-sm font-bold {{ $status['text'] }}">{{ $status['value'] }}</span>
                            </div>
                            <div class="w-full bg-dark-800 rounded-full h-3 overflow-hidden">
                                @php
                                $totalAll = $totalReports + $totalPartOrders + $totalWorkRequests;
                                $percent = $totalAll > 0 ? ($status['value'] / $totalAll) * 100 : 0;
                                @endphp
                                <div class="{{ $status['color'] }} h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $percent }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-luxury p-6">
                    <h3 class="text-lg font-bold text-cream-100 mb-6">دسترسی سریع</h3>
                    <div class="space-y-3">
                        @if($user->isTechnician())
                        <a href="{{ route('reports.create') }}"
                            class="flex items-center gap-3 p-4 bg-dark-50 hover:bg-primary-50 rounded-xl transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-dark-500">ثبت گزارش جدید</p>
                                <p class="text-xs text-dark-400">ایجاد گزارش فنی جدید</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('partorders.create') }}"
                            class="flex items-center gap-3 p-4 bg-dark-50 hover:bg-primary-50 rounded-xl transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-dark-500">سفارش قطعه</p>
                                <p class="text-xs text-dark-400">ثبت سفارش قطعات یدکی</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('workrequests.create') }}"
                            class="flex items-center gap-3 p-4 bg-dark-50 hover:bg-primary-50 rounded-xl transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-dark-500">گردش کار</p>
                                <p class="text-xs text-dark-400">ثبت درخواست تعمیر/سرویس</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @else
                        <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-3 p-4 bg-dark-50 hover:bg-primary-50 rounded-xl transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-dark-500">مشاهده گزارش‌ کارها</p>
                                <p class="text-xs text-dark-400">بررسی و تایید گزارش‌ها</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('partorders.index') }}"
                            class="flex items-center gap-3 p-4 bg-dark-50 hover:bg-primary-50 rounded-xl transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-dark-500">سفارشات قطعات</p>
                                <p class="text-xs text-dark-400">بررسی و تایید سفارشات</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('workrequests.index') }}"
                            class="flex items-center gap-3 p-4 bg-dark-50 hover:bg-primary-50 rounded-xl transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-dark-500">گردش کار</p>
                                <p class="text-xs text-dark-400">بررسی و تایید درخواست‌ها</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-300 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
                <!-- Recent Reports (تمام عرض - بیرون از گرید) -->
                @if($recentReports->count() > 0)
                <div class="card-luxury p-6 mt-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-cream-100">آخرین گزارش‌ها</h3>
                        <a href="{{ route('reports.index') }}" class="text-sm text-primary-400 hover:text-primary-300 transition-colors">
                            مشاهده همه ←
                        </a>
                    </div>
                    <div class="space-y-3">
                        @foreach($recentReports as $report)
                        <a href="{{ route('reports.show', $report) }}"
                            class="flex items-center gap-4 p-4 bg-dark-800/50 hover:bg-dark-700/50 rounded-xl transition-all group border-2 border-transparent hover:border-dark-600">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-sm shadow-md">
                                {{ mb_substr($report->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-cream-100 truncate">{{ $report->part_name }}</p>
                                <p class="text-xs text-dark-400">{{ $report->user->name }} • {{ $report->created_at->diffForHumans() }}</p>
                            </div>
                            @php
                            $statusConfig = match($report->status) {
                            'approved' => ['badge-success', '✓'],
                            'rejected' => ['badge-danger', '✕'],
                            'pending' => ['badge-warning', '⏱'],
                            default => ['badge-info', '★']
                            };
                            @endphp
                            <span class="badge {{ $statusConfig[0] }}">
                                {{ $statusConfig[1] }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ============================================================
             فقط برای CEO — مدیریت پشتیبان‌گیری دیتابیس
             این بلوک رو قبل از بسته‌شدن </div> پایانی داشبورد اضافه کن
             ============================================================ --}}
                @if(auth()->user()->isCEO())
                <div class="mt-8">
                    <div class="card-luxury p-6 sm:p-8">
                        {{-- Header --}}
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                            <div class="flex items-start gap-4 flex-1">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-500/10 border border-red-200">
                                    <svg class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                    </svg>
                                </div>

                                <div class="flex-1">
                                    <h3 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                                        مدیریت دیتابیس
                                    </h3>
                                    <p class="text-sm leading-7 text-dark-400 mt-2 max-w-2xl">
                                        از این بخش برای تهیه‌ی نسخه پشتیبان و بازیابی اطلاعات استفاده می‌شود.
                                        این عملیات مستقیماً روی دیتابیس اثر دارد و فقط برای مدیر سیستم فعال است.
                                    </p>

                                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="section-inner">
                                            <p class="text-xs text-dark-400">وضعیت دسترسی</p>
                                            <p class="mt-1 text-sm font-semibold text-green-600">فعال برای CEO</p>
                                        </div>
                                        <div class="section-inner">
                                            <p class="text-xs text-dark-400">نوع خروجی</p>
                                            <p class="mt-1 text-sm font-semibold text-primary-600">فایل SQL کامل</p>
                                        </div>
                                        <div class="section-inner">
                                            <p class="text-xs text-dark-400">حساسیت</p>
                                            <p class="mt-1 text-sm font-semibold text-orange-600">بالا</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Info Badge --}}
                            <div class="lg:w-auto">
                                <div class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-medium text-red-700">
                                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                    پنل مدیریتی حساس
                                </div>
                            </div>
                        </div>

                        {{-- Alerts --}}
                        @if(session('backup_success'))
                        <div class="mt-6 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('backup_success') }}</span>
                        </div>
                        @endif

                        @if(session('backup_error'))
                        <div class="mt-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('backup_error') }}</span>
                        </div>
                        @endif

                        {{-- Export & Import Cards --}}
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Export --}}
                            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 flex flex-col h-full">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 border border-blue-200">
                                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-cream-100">دریافت پشتیبان</p>
                                        <p class="mt-1 text-xs leading-5 text-dark-400">
                                            یک فایل <span class="text-sm font-mono text-primary-600">.sql</span> کامل از کل دیتابیس دانلود می‌شود.
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('database.export') }}"
                                    class="btn-primary flex items-center justify-center gap-2 w-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    دانلود فایل SQL
                                </a>
                            </div>
                            {{-- Import --}}
                            <div class="rounded-2xl border border-orange-200 bg-orange-50 p-5 flex flex-col h-full">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 border border-orange-200">
                                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-cream-100">بازیابی از پشتیبان</p>
                                        <p class="mt-1 text-xs leading-5 text-red-700">
                                            ⚠️ این عملیات دیتابیس فعلی را
                                            <strong>جایگزین</strong> می‌کند. از صحت فایل اطمینان حاصل کنید.
                                        </p>
                                    </div>
                                </div>

                                <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data" id="importForm" onsubmit="return confirmImport()">
                                    @csrf

                                    <input type="file" name="sql_file" id="sql_file" accept=".sql,.txt" class="hidden" onchange="updateFileName(this)">

                                    <label for="sql_file" class="input-luxury block cursor-pointer text-center hover:border-primary-300">
                                        <span class="flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span id="fileLabelText">انتخاب فایل SQL...</span>
                                        </span>
                                    </label>

                                    @error('sql_file')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                    <button type="submit"
                                        class="btn-primary mt-4 flex items-center justify-center gap-2 w-full bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 border-orange-700/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                                        </svg>
                                        اجرای ایمپورت
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function updateFileName(input) {
                            const label = document.getElementById('fileLabelText');
                            if (input.files && input.files[0]) {
                                label.textContent = input.files[0].name;
                            } else {
                                label.textContent = 'انتخاب فایل SQL...';
                            }
                        }

                        function confirmImport() {
                            const file = document.getElementById('sql_file');
                            if (!file.files || !file.files[0]) {
                                alert('لطفاً ابتدا یک فایل SQL انتخاب کنید.');
                                return false;
                            }
                            return confirm(
                                '⚠️ هشدار!\n\nاین عملیات دیتابیس فعلی را کاملاً جایگزین خواهد کرد.\n\nآیا مطمئن هستید؟'
                            );
                        }
                    </script>
                </div>
                @endif
            </div>
        </div>
</x-app-layout>