<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}"
                    class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-cream-100 hover:bg-dark-700 transition-all border border-dark-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-cream-100">پروفایل کاربر</h1>
            </div>

            {{-- Profile Card --}}
            @php
            $roleMap = [
            'technician' => ['تکنسین', 'bg-blue-500/15 text-blue-400 border-blue-500/25', 'from-blue-500/20 to-blue-700/20'],
            'reception' => ['پذیرش', 'bg-green-500/15 text-green-400 border-green-500/25', 'from-green-500/20 to-green-700/20'],
            'supply' => ['تامین', 'bg-yellow-500/15 text-yellow-400 border-yellow-500/25','from-yellow-500/20 to-yellow-700/20'],
            'ceo' => ['مدیرعامل', 'bg-red-500/15 text-red-400 border-red-500/25', 'from-red-500/20 to-red-700/20'],
            ];
            [$roleLabel, $roleClass, $avatarGradient] = $roleMap[$user->role] ?? ['نامشخص', 'bg-dark-700 text-dark-400 border-dark-600', 'from-dark-600 to-dark-700'];
            $totalActivity = ($user->reports_count ?? 0) + ($user->part_orders_count ?? 0) + ($user->work_requests_count ?? 0);
            @endphp

            <div class="card-luxury p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br {{ $avatarGradient }} border border-primary-500/20 flex items-center justify-center text-4xl font-bold text-cream-100 shrink-0">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <h2 class="text-2xl font-bold text-cream-100">{{ $user->name }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $roleClass }}">{{ $roleLabel }}</span>
                        </div>
                        <p class="text-dark-400 direction-ltr">{{ $user->email }}</p>
                        <p class="text-sm text-dark-500 mt-1">عضویت از {{ $user->created_at->diffForHumans() }}</p>
                    </div>

                    <a href="{{ route('users.edit', $user) }}" class="btn-secondary inline-flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        ویرایش
                    </a>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t-2 divider">
                    @foreach([
                    ['گزارش کار', $user->reports_count ?? 0, 'text-blue-400', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['سفارش قطعه', $user->part_orders_count ?? 0, 'text-green-400', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['گردش کار', $user->work_requests_count ?? 0, 'text-yellow-400', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['کل فعالیت', $totalActivity, 'text-primary-400','M13 10V3L4 14h7v7l9-11h-7z'],
                    ] as [$label, $count, $color, $icon])
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-cream-100">{{ $count }}</div>
                        <div class="text-xs text-dark-400 mt-1">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Activity Chart --}}
            <div class="card-luxury p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-cream-100">فعالیت ۶ ماه اخیر</h3>
                        <p class="text-sm text-dark-400 mt-0.5">تعداد آیتم‌های ثبت‌شده به تفکیک ماه</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-dark-400">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-blue-500/60 inline-block"></span>گزارش</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-green-500/60 inline-block"></span>سفارش</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-yellow-500/60 inline-block"></span>گردش</span>
                    </div>
                </div>

                <div class="relative" style="height: 200px;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Reports --}}
                <div class="card-luxury p-6">
                    <h3 class="text-base font-bold text-cream-100 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        آخرین گزارش‌ها
                    </h3>
                    @if($recentReports->count())
                    <div class="space-y-2">
                        @foreach($recentReports as $report)
                        @php
                        $sconf = match($report->status) {
                        'approved' => ['badge-success', '✓'],
                        'rejected' => ['badge-danger', '✕'],
                        'pending' => ['badge-warning', '⏱'],
                        default => ['badge-info', '★'],
                        };
                        @endphp
                        <a href="{{ route('reports.show', $report) }}"
                            class="flex items-center gap-3 p-3 rounded-xl bg-dark-800/40 hover:bg-dark-700/50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/15 border border-blue-500/25 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-cream-200 group-hover:text-primary-400 transition-colors truncate">{{ $report->part_name }}</div>
                                <div class="text-xs text-dark-500 mt-0.5">{{ $report->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="badge {{ $sconf[0] }} text-xs shrink-0">{{ $sconf[1] }}</span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-dark-500 text-center py-4">گزارشی ثبت نشده</p>
                    @endif
                </div>

                {{-- Recent Part Orders --}}
                <div class="card-luxury p-6">
                    <h3 class="text-base font-bold text-cream-100 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        آخرین سفارش‌ها
                    </h3>
                    @if($recentPartOrders->count())
                    <div class="space-y-2">
                        @foreach($recentPartOrders as $order)
                        @php
                        $sconf = match($order->status) {
                        'approved' => ['badge-success', '✓'],
                        'rejected' => ['badge-danger', '✕'],
                        'pending' => ['badge-warning', '⏱'],
                        default => ['badge-info', '★'],
                        };
                        @endphp
                        <a href="{{ route('partorders.show', $order) }}"
                            class="flex items-center gap-3 p-3 rounded-xl bg-dark-800/40 hover:bg-dark-700/50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-green-500/15 border border-green-500/25 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-cream-200 group-hover:text-primary-400 transition-colors truncate">
                                    {{ is_string($order->part_name) 
           ? $order->part_name 
           : ($order->title ?? 'سفارش #'.$order->id) 
    }}
                                </div>
                                <div class="text-xs text-dark-500 mt-0.5">{{ $order->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="badge {{ $sconf[0] }} text-xs shrink-0">{{ $sconf[1] }}</span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-dark-500 text-center py-4">سفارشی ثبت نشده</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script>
        (function() {
            const activity = @json($activity);
            const labels = activity.map(m => m.month_en);
            const reports = activity.map(m => m.reports);
            const orders = activity.map(m => m.part_orders);
            const wrs = activity.map(m => m.work_requests);

            const isDark = document.documentElement.classList.contains('dark') ||
                window.matchMedia('(prefers-color-scheme: dark)').matches;

            const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
            const labelColor = isDark ? '#888580' : '#6b7280';

            const ctx = document.getElementById('activityChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                            label: 'گزارش',
                            data: reports,
                            backgroundColor: 'rgba(59,130,246,0.55)',
                            borderColor: 'rgba(59,130,246,0.85)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'سفارش',
                            data: orders,
                            backgroundColor: 'rgba(34,197,94,0.55)',
                            borderColor: 'rgba(34,197,94,0.85)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'گردش',
                            data: wrs,
                            backgroundColor: 'rgba(234,179,8,0.55)',
                            borderColor: 'rgba(234,179,8,0.85)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1c1c1a' : '#ffffff',
                            titleColor: isDark ? '#e8e5d9' : '#111',
                            bodyColor: isDark ? '#888580' : '#555',
                            borderColor: isDark ? '#3a3835' : '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 8,
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: labelColor,
                                font: {
                                    size: 12
                                }
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: labelColor,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: gridColor
                            },
                        },
                    },
                },
            });
        })();
    </script>
</x-app-layout>