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

            <!-- Chart Section -->
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
                                <div class="{{ $status['color'] }} h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $totalReports > 0 ? ($status['value'] / $totalReports * 100) : 0 }}%">
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
                            class="flex items-center gap-3 p-4 bg-dark-700/50 hover:bg-dark-700 rounded-xl transition-all group border-2 border-dark-600 hover:border-primary-500">
                            <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-cream-100">ثبت گزارش جدید</p>
                                <p class="text-xs text-dark-400">ایجاد گزارش فنی جدید</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-500 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('partorders.create') }}"
                            class="flex items-center gap-3 p-4 bg-dark-700/50 hover:bg-dark-700 rounded-xl transition-all group border-2 border-dark-600 hover:border-primary-500">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-cream-100">سفارش قطعه</p>
                                <p class="text-xs text-dark-400">ثبت سفارش قطعات یدکی</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-500 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('workrequests.create') }}"
                            class="flex items-center gap-3 p-4 bg-dark-700/50 hover:bg-dark-700 rounded-xl transition-all group border-2 border-dark-600 hover:border-primary-500">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-cream-100">گردش کار</p>
                                <p class="text-xs text-dark-400">ثبت درخواست تعمیر/سرویس</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-500 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @else
                        <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-3 p-4 bg-dark-700/50 hover:bg-dark-700 rounded-xl transition-all group border-2 border-dark-600 hover:border-primary-500">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-cream-100">مشاهده گزارش‌ کارها</p>
                                <p class="text-xs text-dark-400">بررسی و تایید گزارش‌ها</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-500 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('partorders.index') }}"
                            class="flex items-center gap-3 p-4 bg-dark-700/50 hover:bg-dark-700 rounded-xl transition-all group border-2 border-dark-600 hover:border-primary-500">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-cream-100">سفارشات قطعات</p>
                                <p class="text-xs text-dark-400">بررسی و تایید سفارشات</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-500 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <a href="{{ route('workrequests.index') }}"
                            class="flex items-center gap-3 p-4 bg-dark-700/50 hover:bg-dark-700 rounded-xl transition-all group border-2 border-dark-600 hover:border-primary-500">
                            <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-cream-100">گردش کار</p>
                                <p class="text-xs text-dark-400">بررسی و تایید درخواست‌ها</p>
                            </div>
                            <svg class="w-5 h-5 text-dark-500 group-hover:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            @if($recentReports->count() > 0)
            <div class="card-luxury p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-cream-100">آخرین گزارش‌ها</h3>
                    <a href="{{ route('reports.index') }}" class="text-sm text-primary-400 hover:text-primary-300 transition-colors">
                        مشاهده همه →
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

        </div>
    </div>
</x-app-layout>