<x-app-layout>
    <div class="py-5 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-4">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        داشبورد
                    </h1>
                    <p class="text-dark-400 text-sm mt-1">خوش آمدید {{ auth()->user()->name }} 👋</p>
                </div>
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
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Total Reports -->
                <div class="card-luxury p-4 hover:scale-[1.02] transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-dark-400 mb-1">گزارش کار</p>
                            <p class="text-2xl font-bold text-cream-100">{{ $totalReports }}</p>
                        </div>
                        <div class="w-11 h-11 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-yellow-400 font-medium">{{ $pendingReports }}</span>
                        <span class="text-dark-400 mr-1.5">در انتظار</span>
                    </div>
                </div>

                <!-- Approved Reports -->
                <div class="card-luxury p-4 hover:scale-[1.02] transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-dark-400 mb-1">تایید شده</p>
                            <p class="text-2xl font-bold text-green-400">{{ $approvedReports + $approvedPartOrders + $approvedWorkRequests }}</p>
                        </div>
                        <div class="w-11 h-11 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-green-400 font-medium">{{ ($totalReports+$totalPartOrders+$totalWorkRequests) > 0 ? round((($approvedReports+$approvedPartOrders+$approvedWorkRequests) / ($totalReports+$totalPartOrders+$totalWorkRequests)) * 100) : 0 }}%</span>
                        <span class="text-dark-400 mr-1.5">از کل</span>
                    </div>
                </div>

                <!-- Part Orders -->
                <div class="card-luxury p-4 hover:scale-[1.02] transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-dark-400 mb-1">سفارش قطعات</p>
                            <p class="text-2xl font-bold text-cream-100">{{ $totalPartOrders }}</p>
                        </div>
                        <div class="w-11 h-11 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-yellow-400 font-medium">{{ $pendingPartOrders }}</span>
                        <span class="text-dark-400 mr-1.5">در انتظار</span>
                    </div>
                </div>

                <!-- Work Requests -->
                <div class="card-luxury p-4 hover:scale-[1.02] transition-transform duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-dark-400 mb-1">گردش کار</p>
                            <p class="text-2xl font-bold text-cream-100">{{ $totalWorkRequests }}</p>
                        </div>
                        <div class="w-11 h-11 bg-orange-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-yellow-400 font-medium">{{ $pendingWorkRequests }}</span>
                        <span class="text-dark-400 mr-1.5">در انتظار</span>
                    </div>
                </div>
            </div>

            <!-- Chart Section, Quick Actions & Recent Reports (سه ستونه فشرده) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Status Distribution -->
                <div class="card-luxury p-4">
                    <h3 class="text-sm font-bold text-cream-100 mb-3">توزیع وضعیت</h3>
                    <div class="space-y-3">
                        @php
                        $statusData = [
                        ['label' => 'در انتظار', 'value' => $pendingReports + $pendingPartOrders + $pendingWorkRequests, 'color' => 'bg-yellow-500', 'text' => 'text-yellow-400'],
                        ['label' => 'تایید شده', 'value' => $approvedReports + $approvedPartOrders + $approvedWorkRequests, 'color' => 'bg-green-500', 'text' => 'text-green-400'],
                        ['label' => 'رد شده', 'value' => $rejectedReports + $rejectedPartOrders + $rejectedWorkRequests, 'color' => 'bg-red-500', 'text' => 'text-red-400'],
                        ];
                        @endphp

                        @foreach($statusData as $status)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-cream-200">{{ $status['label'] }}</span>
                                <span class="text-xs font-bold {{ $status['text'] }}">{{ $status['value'] }}</span>
                            </div>
                            <div class="w-full bg-dark-800 rounded-full h-2 overflow-hidden">
                                @php
                                $totalAll = $totalReports + $totalPartOrders + $totalWorkRequests;
                                $percent = $totalAll > 0 ? ($status['value'] / $totalAll) * 100 : 0;
                                @endphp
                                <div class="{{ $status['color'] }} h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $percent }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-luxury p-4">
                    <h3 class="text-sm font-bold text-cream-100 mb-3">دسترسی سریع</h3>
                    <div class="space-y-2">
                        @if($user->isTechnician())
                        <a href="{{ route('reports.create') }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-50 hover:bg-primary-50 rounded-lg transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-8 h-8 bg-primary-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-dark-500 truncate">ثبت گزارش جدید</p>
                                <p class="text-[11px] text-dark-400 truncate">ایجاد گزارش فنی جدید</p>
                            </div>
                            <svg class="w-4 h-4 text-dark-300 group-hover:text-primary-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('partorders.create') }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-50 hover:bg-primary-50 rounded-lg transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-dark-500 truncate">سفارش قطعه</p>
                                <p class="text-[11px] text-dark-400 truncate">ثبت سفارش قطعات یدکی</p>
                            </div>
                            <svg class="w-4 h-4 text-dark-300 group-hover:text-primary-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('workrequests.create') }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-50 hover:bg-primary-50 rounded-lg transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-dark-500 truncate">گردش کار</p>
                                <p class="text-[11px] text-dark-400 truncate">ثبت درخواست تعمیر/سرویس</p>
                            </div>
                            <svg class="w-4 h-4 text-dark-300 group-hover:text-primary-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @else
                        <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-50 hover:bg-primary-50 rounded-lg transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-dark-500 truncate">مشاهده گزارش‌ کارها</p>
                                <p class="text-[11px] text-dark-400 truncate">بررسی و تایید گزارش‌ها</p>
                            </div>
                            <svg class="w-4 h-4 text-dark-300 group-hover:text-primary-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('partorders.index') }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-50 hover:bg-primary-50 rounded-lg transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-dark-500 truncate">سفارشات قطعات</p>
                                <p class="text-[11px] text-dark-400 truncate">بررسی و تایید سفارشات</p>
                            </div>
                            <svg class="w-4 h-4 text-dark-300 group-hover:text-primary-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('workrequests.index') }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-50 hover:bg-primary-50 rounded-lg transition-all group border border-dark-200 hover:border-primary-300">
                            <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-dark-500 truncate">گردش کار</p>
                                <p class="text-[11px] text-dark-400 truncate">بررسی و تایید درخواست‌ها</p>
                            </div>
                            <svg class="w-4 h-4 text-dark-300 group-hover:text-primary-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Recent Reports (ستون سوم به‌جای تمام‌عرض جدا) -->
                <div class="card-luxury p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-cream-100">آخرین گزارش‌ها</h3>
                        <a href="{{ route('reports.index') }}" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">
                            همه ←
                        </a>
                    </div>
                    @if($recentReports->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentReports as $report)
                        <a href="{{ route('reports.show', $report) }}"
                            class="flex items-center gap-2.5 p-2.5 bg-dark-800/50 hover:bg-dark-700/50 rounded-lg transition-all group border-2 border-transparent hover:border-dark-600">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-xs shadow-md shrink-0">
                                {{ mb_substr($report->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-cream-100 truncate">{{ $report->part_name }}</p>
                                <p class="text-[11px] text-dark-400 truncate">{{ $report->user->name }} • {{ $report->created_at->diffForHumans() }}</p>
                            </div>
                            @php
                            $statusConfig = match($report->status) {
                            'approved' => ['badge-success', '✓'],
                            'rejected' => ['badge-danger', '✕'],
                            'pending' => ['badge-warning', '⏱'],
                            default => ['badge-info', '★']
                            };
                            @endphp
                            <span class="badge {{ $statusConfig[0] }} shrink-0">
                                {{ $statusConfig[1] }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-dark-400 text-center py-6">گزارشی ثبت نشده است</p>
                    @endif
                </div>
            </div>

            {{-- ============================================================
             فقط برای CEO — مدیریت پشتیبان‌گیری دیتابیس
             به‌صورت details/summary جمع‌شده (پیش‌فرض بسته) تا فضای صفحه رو نگیره
             ============================================================ --}}
            @if(auth()->user()->isCEO())
            <details class="card-luxury group">
                <summary class="flex items-center justify-between p-4 cursor-pointer list-none">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-500/10 border border-red-200 shrink-0">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-cream-100">مدیریت دیتابیس</p>
                            <p class="text-[11px] text-dark-400">پشتیبان‌گیری و بازیابی — فقط برای مدیر سیستم</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-dark-400 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>

                <div class="px-4 pb-4 border-t border-dark-200 pt-4">
                    {{-- Alerts --}}
                    @if(session('backup_success'))
                    <div class="mb-3 flex items-start gap-2 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-xs">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('backup_success') }}</span>
                    </div>
                    @endif

                    @if(session('backup_error'))
                    <div class="mb-3 flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('backup_error') }}</span>
                    </div>
                    @endif

                    {{-- Status chips inline --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="badge badge-success">دسترسی فعال برای CEO</span>
                        <span class="badge badge-info">خروجی: SQL کامل</span>
                        <span class="badge badge-warning">حساسیت: بالا</span>
                    </div>

                    {{-- Export & Import Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Export --}}
                        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 flex flex-col h-full">
                            <div class="flex items-center gap-2.5 flex-1">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 border border-blue-200 shrink-0">
                                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-cream-100">دریافت پشتیبان</p>
                                    <p class="mt-0.5 text-[11px] leading-4 text-dark-400">
                                        فایل <span class="text-xs font-mono text-primary-600">.sql</span> کامل از دیتابیس
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('database.export') }}"
                                class="btn-primary text-sm py-2 mt-3 flex items-center justify-center gap-2 w-full">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                دانلود فایل SQL
                            </a>
                        </div>
                        {{-- Import --}}
                        <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4 flex flex-col h-full">
                            <div class="flex items-center gap-2.5 flex-1">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-100 border border-orange-200 shrink-0">
                                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm text-cream-100">بازیابی از پشتیبان</p>
                                    <p class="mt-0.5 text-[11px] leading-4 text-red-700">
                                        ⚠️ دیتابیس فعلی را <strong>جایگزین</strong> می‌کند
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data" id="importForm" onsubmit="return confirmImport()" class="mt-3">
                                @csrf

                                <input type="file" name="sql_file" id="sql_file" accept=".sql,.txt" class="hidden" onchange="updateFileName(this)">

                                <label for="sql_file" class="input-luxury block cursor-pointer text-center text-sm py-2 hover:border-primary-300">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span id="fileLabelText">انتخاب فایل SQL...</span>
                                    </span>
                                </label>

                                @error('sql_file')
                                <p class="mt-1.5 text-[11px] text-red-600">{{ $message }}</p>
                                @enderror

                                <button type="submit"
                                    class="btn-primary text-sm py-2 mt-2 flex items-center justify-center gap-2 w-full bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 border-orange-700/30">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                                    </svg>
                                    اجرای ایمپورت
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </details>

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
            @endif
        </div>
    </div>
</x-app-layout>