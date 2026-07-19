<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-4xl mx-auto space-y-2">

            {{-- ═══ هدر فشرده ═══ --}}
            @php
            $statusConfig = match($task->status) {
            'pending' => ['badge-warning', 'در انتظار', '⏱'],
            'in_progress' => ['badge-info', 'در حال عیب‌یابی', '🔍'],
            'done' => ['badge-success', 'در حال تعمیر', '🔧'],
            default => ['badge-info', '---', ''],
            };
            @endphp
            <div class="card-luxury p-2.5 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('my-tasks.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                        <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold text-cream-100 truncate">جزئیات تسک — {{ $task->workRequest->request_number }}</h1>
                    </div>
                </div>
                <span class="badge {{ $statusConfig[0] }} !text-xs shrink-0">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
            </div>

            {{-- اطلاعات درخواست کار --}}
            <div class="card-luxury p-3.5 space-y-3">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 text-xs">
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره درخواست</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-bold truncate text-right">{{ $task->workRequest->request_number }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ درخواست</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->workRequest->request_date_jalali }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">نوع درخواست</label>
                        @php
                        $typeConfig = match($task->workRequest->request_type) {
                        'repair' => ['bg-red-500/20 text-red-400 border-red-500/30', '🔧 تعمیر'],
                        'service' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', '⚙️ سرویس'],
                        'install' => ['bg-green-500/20 text-green-400 border-green-500/30', '🔌 نصب'],
                        'sale' => ['bg-yellow-500/20 text-yellow-400 border-yellow-500/30', '💰 فروش'],
                        default => ['bg-dark-700 text-dark-400 border-dark-600', '📋'],
                        };
                        @endphp
                        <div class="border border-dark-600/40 rounded-lg px-2 py-1 text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $typeConfig[0] }}">{{ $typeConfig[1] }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">مدل دستگاه</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->workRequest->device_model }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره سریال</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->workRequest->serial_number }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ثبت‌کننده درخواست</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->workRequest->user->name ?? '---' }}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    @if($task->workRequest->work_description)
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح کار درخواستی</label>
                        <div class="w-full border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs whitespace-pre-wrap text-right">{{ $task->workRequest->work_description }}</div>
                    </div>
                    @endif

                    @if($task->workRequest->issue_description)
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح ایراد اعلامی</label>
                        <div class="w-full border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs whitespace-pre-wrap text-right">{{ $task->workRequest->issue_description }}</div>
                    </div>
                    @endif

                    @if($task->workRequest->workflow_description)
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح گردش کار</label>
                        <div class="w-full border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs whitespace-pre-wrap text-right">{{ $task->workRequest->workflow_description }}</div>
                    </div>
                    @endif
                </div>

                @if($task->note)
                <div class="pt-2 border-t border-dark-700">
                    <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">یادداشت پذیرش</label>
                    <div class="border-r-2 border-primary-500/50 pr-2 text-cream-200 text-xs">{{ $task->note }}</div>
                </div>
                @endif
            </div>

            {{-- دکمه‌های اقدام — ثبت گزارش و سفارش --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <a href="{{ route('reports.create', ['task_id' => $task->id]) }}"
                    class="card-luxury p-2.5 hover:shadow-lg hover:shadow-primary-900/15 transition-all duration-200 group flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-primary-500/20 rounded-lg flex items-center justify-center border border-primary-500/30 group-hover:bg-primary-500/30 transition-colors shrink-0">
                        <svg class="w-4.5 h-4.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-cream-100 font-bold text-sm">ثبت گزارش کار</div>
                        <div class="text-dark-400 text-xs truncate">گزارش اقدامات انجام‌شده</div>
                    </div>
                </a>

                <a href="{{ route('partorders.create', ['task_id' => $task->id]) }}"
                    class="card-luxury p-2.5 hover:shadow-lg hover:shadow-primary-900/15 transition-all duration-200 group flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-yellow-500/20 rounded-lg flex items-center justify-center border border-yellow-500/30 group-hover:bg-yellow-500/30 transition-colors shrink-0">
                        <svg class="w-4.5 h-4.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-cream-100 font-bold text-sm">ثبت سفارش قطعه</div>
                        <div class="text-dark-400 text-xs truncate">درخواست خرید قطعات مورد نیاز</div>
                    </div>
                </a>
            </div>

            {{-- ═══ بخش‌های جمع‌وبازشو ═══ --}}

            {{-- گزارشات ثبت‌شده --}}
            @if($task->reports->count() > 0)
            <details class="card-luxury p-3 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        گزارش‌های ثبت‌شده
                        <span class="badge badge-info !text-[10px] !py-0.5">{{ $task->reports->count() }}</span>
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-2 mt-2 border-t border-dark-700 space-y-1.5">
                    @foreach($task->reports as $report)
                    <div class="border border-dark-600/40 rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-cream-200 font-medium text-xs truncate">{{ $report->part_name }}</div>
                            <div class="text-[11px] text-dark-400">{{ $report->request_number }}</div>
                        </div>
                        <a href="{{ route('reports.show', $report) }}" class="btn-secondary text-xs !px-2.5 !py-1 shrink-0">مشاهده</a>
                    </div>
                    @endforeach
                </div>
            </details>
            @endif

            {{-- سفارشات قطعه --}}
            @if($task->partOrders->count() > 0)
            <details class="card-luxury p-3 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                        </svg>
                        سفارشات قطعه
                        <span class="badge badge-info !text-[10px] !py-0.5">{{ $task->partOrders->count() }}</span>
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-2 mt-2 border-t border-dark-700 space-y-1.5">
                    @foreach($task->partOrders as $order)
                    <div class="border border-dark-600/40 rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-cream-200 font-medium text-xs truncate">{{ $order->equipment_name }}</div>
                            <div class="text-[11px] text-dark-400">{{ $order->order_number }}</div>
                        </div>
                        <a href="{{ route('partorders.show', $order) }}" class="btn-secondary text-xs !px-2.5 !py-1 shrink-0">مشاهده</a>
                    </div>
                    @endforeach
                </div>
            </details>
            @endif

        </div>
    </div>
</x-app-layout>