<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-2.5">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2.5 flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-sm font-bold text-cream-100 truncate">پروفایل کاربر</h1>
            </div>

            @php
            $roleMap = [
            'technician' => ['تکنسین', 'badge-info'],
            'reception' => ['پذیرش', 'badge-success'],
            'supply' => ['تامین', 'badge-warning'],
            'ceo' => ['مدیرعامل', 'badge-danger'],
            ];
            [$roleLabel, $roleBadge] = $roleMap[$user->role] ?? ['نامشخص', 'badge-info'];
            $totalActivity = ($user->reports_count ?? 0) + ($user->part_orders_count ?? 0) + ($user->work_requests_count ?? 0);
            @endphp

            {{-- کارت پروفایل --}}
            <div class="card-luxury p-3">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500/25 to-primary-800/25 border border-primary-500/20 flex items-center justify-center text-base font-bold text-cream-100 shrink-0">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-0.5">
                            <h2 class="text-sm font-bold text-cream-100 truncate">{{ $user->name }}</h2>
                            <span class="badge {{ $roleBadge }} !text-[10px]">{{ $roleLabel }}</span>
                        </div>
                        <p class="text-[11px] text-dark-400 direction-ltr">{{ $user->email }}</p>
                        <p class="text-[10px] text-dark-500 mt-0.5">عضویت از {{ $user->created_at->diffForHumans() }}</p>
                    </div>

                    <a href="{{ route('users.edit', $user) }}" class="btn-secondary !py-1 !px-2.5 text-xs inline-flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        ویرایش
                    </a>
                </div>

                {{-- آمار --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2.5 pt-2.5 border-t border-dark-700/60">
                    @foreach([
                    ['گزارش کار', $user->reports_count ?? 0, 'text-primary-400', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['سفارش قطعه', $user->part_orders_count ?? 0, 'text-cream-300', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['گردش کار', $user->work_requests_count ?? 0, 'text-primary-300', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['کل فعالیت', $totalActivity, 'text-cream-100', 'M13 10V3L4 14h7v7l9-11h-7z'],
                    ] as [$label, $count, $color, $icon])
                    <div class="border border-dark-600/40 hover:bg-dark-700/25 hover:border-dark-500/50 rounded-lg p-1.5 text-center transition-all duration-200">
                        <svg class="w-3.5 h-3.5 {{ $color }} mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                        <div class="text-lg font-bold text-cream-100">{{ $count }}</div>
                        <div class="text-[10px] text-dark-400 mt-0.5">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- نمودار فعالیت --}}
            <div class="card-luxury p-3">
                <div class="flex items-center justify-between mb-2 flex-wrap gap-1.5">
                    <div>
                        <h3 class="text-xs font-bold text-cream-100 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h14a1 1 0 001-1V9.5a1 1 0 00-.4-.8L12 3 3.4 8.7a1 1 0 00-.4.8V18a1 1 0 001 1z" />
                            </svg>
                            فعالیت ۶ ماه اخیر
                        </h3>
                        <p class="text-[10px] text-dark-400 mt-0.5">تعداد آیتم‌های ثبت‌شده به تفکیک ماه</p>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] text-dark-400">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-400 inline-block"></span>گزارش</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-cream-300 inline-block"></span>سفارش</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-700 inline-block"></span>گردش</span>
                    </div>
                </div>

                <div class="border border-dark-600/40 rounded-lg p-2">
                    <div class="relative" style="height: 150px;">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>

         {{-- آخرین فعالیت‌ها --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5">

    {{-- آخرین گزارش‌ها --}}
    <div class="card-luxury p-3">
        <details class="group">
            <summary class="text-xs font-bold text-cream-100 flex items-center justify-between gap-1.5 cursor-pointer list-none select-none">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    آخرین گزارش‌ها
                </span>
                <svg class="w-3.5 h-3.5 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="mt-2.5 min-h-[260px]">
                @if($recentReports->count())
                    <div class="space-y-1.5">
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
                               class="flex items-center gap-2 p-1.5 rounded-lg border border-dark-600/40 hover:bg-primary-500/5 hover:border-primary-500/25 transition-all duration-200 group">
                                <div class="w-6 h-6 rounded-lg bg-primary-500/15 border border-primary-500/25 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-cream-200 group-hover:text-primary-400 transition-colors truncate">
                                        {{ $report->part_name }}
                                    </div>
                                    <div class="text-[10px] text-dark-500 mt-0.5">
                                        {{ $report->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="badge {{ $sconf[0] }} !text-[10px] shrink-0">{{ $sconf[1] }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-dark-500 text-center py-3">گزارشی ثبت نشده</p>
                @endif
            </div>
        </details>
    </div>

    {{-- آخرین سفارش‌ها --}}
    <div class="card-luxury p-3">
        <details class="group">
            <summary class="text-xs font-bold text-cream-100 flex items-center justify-between gap-1.5 cursor-pointer list-none select-none">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    آخرین سفارش ها
                </span>
                <svg class="w-3.5 h-3.5 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>

            <div class="mt-2.5 min-h-[260px]">
                @if($recentPartOrders->count())
                    <div class="space-y-1.5">
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
                               class="flex items-center gap-2 p-1.5 rounded-lg border border-dark-600/40 hover:bg-primary-500/5 hover:border-primary-500/25 transition-all duration-200 group">
                                <div class="w-6 h-6 rounded-lg bg-cream-300/10 border border-cream-300/20 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-medium text-cream-200 group-hover:text-primary-400 transition-colors truncate">
                                        {{ is_string($order->part_name) ? $order->part_name : ($order->title ?? 'سفارش #'.$order->id) }}
                                    </div>
                                    <div class="text-[10px] text-dark-500 mt-0.5">
                                        {{ $order->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="badge {{ $sconf[0] }} !text-[10px] shrink-0">{{ $sconf[1] }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-dark-500 text-center py-3">سفارشی ثبت نشده</p>
                @endif
            </div>
        </details>
    </div>

</div>

            {{-- Chart.js --}}
            <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
            <script>
                (function() {
                    const activity = @json($activity);
                    const monthNamesFa = {
                        'Jan': 'ژانویه',
                        'Feb': 'فوریه',
                        'Mar': 'مارس',
                        'Apr': 'آوریل',
                        'May': 'مه',
                        'Jun': 'ژوئن',
                        'Jul': 'ژوئیه',
                        'Aug': 'اوت',
                        'Sep': 'سپتامبر',
                        'Oct': 'اکتبر',
                        'Nov': 'نوامبر',
                        'Dec': 'دسامبر',
                        'January': 'ژانویه',
                        'February': 'فوریه',
                        'March': 'مارس',
                        'April': 'آوریل',
                        'June': 'ژوئن',
                        'July': 'ژوئیه',
                        'August': 'اوت',
                        'September': 'سپتامبر',
                        'October': 'اکتبر',
                        'November': 'نوامبر',
                        'December': 'دسامبر',
                    };
                    const labels = activity.map(m => monthNamesFa[m.month_en] || m.month_en);
                    const reports = activity.map(m => m.reports);
                    const orders = activity.map(m => m.part_orders);
                    const wrs = activity.map(m => m.work_requests);

                    const primary = 'rgba(232,71,106,0.65)';
                    const primaryB = 'rgba(232,71,106,0.9)';
                    const cream = 'rgba(232,229,217,0.55)';
                    const creamB = 'rgba(232,229,217,0.8)';
                    const primaryDk = 'rgba(120,30,55,0.7)';
                    const primaryDkB = 'rgba(120,30,55,0.9)';

                    const gridColor = 'rgba(255,255,255,0.05)';
                    const labelColor = '#888580';

                    const ctx = document.getElementById('activityChart').getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                    label: 'گزارش',
                                    data: reports,
                                    backgroundColor: primary,
                                    borderColor: primaryB,
                                    borderWidth: 1,
                                    borderRadius: 5,
                                    borderSkipped: false,
                                    maxBarThickness: 16
                                },
                                {
                                    label: 'سفارش',
                                    data: orders,
                                    backgroundColor: cream,
                                    borderColor: creamB,
                                    borderWidth: 1,
                                    borderRadius: 5,
                                    borderSkipped: false,
                                    maxBarThickness: 16
                                },
                                {
                                    label: 'گردش',
                                    data: wrs,
                                    backgroundColor: primaryDk,
                                    borderColor: primaryDkB,
                                    borderWidth: 1,
                                    borderRadius: 5,
                                    borderSkipped: false,
                                    maxBarThickness: 16
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
                                    backgroundColor: '#1c1c1a',
                                    titleColor: '#e8e5d9',
                                    bodyColor: '#a8a49a',
                                    borderColor: '#3a3835',
                                    borderWidth: 1,
                                    padding: 8,
                                    cornerRadius: 8,
                                    titleFont: {
                                        family: 'inherit',
                                        size: 11
                                    },
                                    bodyFont: {
                                        family: 'inherit',
                                        size: 10
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        color: gridColor
                                    },
                                    ticks: {
                                        color: labelColor,
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        precision: 0,
                                        color: labelColor,
                                        font: {
                                            size: 10
                                        }
                                    },
                                    grid: {
                                        color: gridColor
                                    }
                                },
                            },
                        },
                    });
                })();
            </script>
</x-app-layout>