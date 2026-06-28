<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('my-tasks.index') }}" class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-cream-100 hover:bg-dark-700 border border-dark-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-cream-100">جزئیات تسک</h1>
                    <p class="text-dark-400 text-sm mt-0.5">درخواست {{ $task->workRequest->request_number }}</p>
                </div>
                @php
                $statusConfig = match($task->status) {
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'in_progress' => ['badge-info', 'در حال عیب‌یابی', '🔍'],
                'done' => ['badge-success', 'در حال تعمیر', '🔧'],
                default => ['badge-info', '---', ''],
                };
                @endphp
                <span class="badge {{ $statusConfig[0] }}">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
            </div>

            {{-- اطلاعات درخواست — کارت اصلی --}}
            <div class="card-luxury p-6">
                <h2 class="text-lg font-bold text-cream-100 mb-5 pb-3 border-b-2 divider">اطلاعات درخواست کار</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm mb-5">
                    <div>
                        <div class="text-dark-400 text-xs mb-0.5">شماره درخواست</div>
                        <div class="text-cream-100 font-bold text-base">{{ $task->workRequest->request_number }}</div>
                    </div>
                    <div>
                        <div class="text-dark-400 text-xs mb-0.5">تاریخ درخواست</div>
                        <div class="text-cream-100 font-medium">{{ $task->workRequest->request_date_jalali }}</div>
                    </div>
                    <div>
                        <div class="text-dark-400 text-xs mb-0.5">نوع درخواست</div>
                        @php
                        $typeConfig = match($task->workRequest->request_type) {
                        'repair' => ['bg-red-500/20 text-red-400 border-red-500/30', '🔧 تعمیر'],
                        'service' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', '⚙️ سرویس'],
                        'install' => ['bg-green-500/20 text-green-400 border-green-500/30', '🔌 نصب'],
                        'sale' => ['bg-yellow-500/20 text-yellow-400 border-yellow-500/30', '💰 فروش'],
                        default => ['bg-dark-700 text-dark-400 border-dark-600', '📋'],
                        };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $typeConfig[0] }}">{{ $typeConfig[1] }}</span>
                    </div>
                    <div>
                        <div class="text-dark-400 text-xs mb-0.5">مدل دستگاه</div>
                        <div class="text-cream-100 font-medium">{{ $task->workRequest->device_model }}</div>
                    </div>
                    <div>
                        <div class="text-dark-400 text-xs mb-0.5">شماره سریال</div>
                        <div class="text-cream-100 font-medium">{{ $task->workRequest->serial_number }}</div>
                    </div>
                    <div>
                        <div class="text-dark-400 text-xs mb-0.5">ثبت‌کننده درخواست</div>
                        <div class="text-cream-100 font-medium">{{ $task->workRequest->user->name ?? '---' }}</div>
                    </div>
                </div>

                <div class="space-y-3">
                    @if($task->workRequest->work_description)
                    <div>
                        <div class="text-dark-400 text-xs mb-1">شرح کار درخواستی</div>
                        <div class="section-inner text-cream-200 text-sm">{{ $task->workRequest->work_description }}</div>
                    </div>
                    @endif

                    @if($task->workRequest->issue_description)
                    <div>
                        <div class="text-dark-400 text-xs mb-1">شرح ایراد اعلامی</div>
                        <div class="section-inner text-cream-200 text-sm">{{ $task->workRequest->issue_description }}</div>
                    </div>
                    @endif

                    @if($task->workRequest->workflow_description)
                    <div>
                        <div class="text-dark-400 text-xs mb-1">شرح گردش کار</div>
                        <div class="section-inner text-cream-200 text-sm">{{ $task->workRequest->workflow_description }}</div>
                    </div>
                    @endif
                </div>

                @if($task->note)
                <div class="mt-4 pt-4 border-t border-dark-700">
                    <div class="text-dark-400 text-xs mb-1">یادداشت پذیرش</div>
                    <div class="border-r-2 border-primary-500/50 pr-3 text-cream-200 text-sm">{{ $task->note }}</div>
                </div>
                @endif
            </div>

            {{-- دکمه‌های اقدام — ثبت گزارش و سفارش --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('reports.create', ['task_id' => $task->id]) }}"
                    class="card-luxury p-5 hover:shadow-xl hover:shadow-primary-900/20 transition-all duration-200 group block">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-500/20 rounded-xl flex items-center justify-center border border-primary-500/30 group-hover:bg-primary-500/30 transition-colors">
                            <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-cream-100 font-bold">ثبت گزارش کار</div>
                            <div class="text-dark-400 text-sm mt-0.5">گزارش اقدامات انجام‌شده</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('partorders.create', ['task_id' => $task->id]) }}"
                    class="card-luxury p-5 hover:shadow-xl hover:shadow-primary-900/20 transition-all duration-200 group block">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center border border-yellow-500/30 group-hover:bg-yellow-500/30 transition-colors">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-cream-100 font-bold">ثبت سفارش قطعه</div>
                            <div class="text-dark-400 text-sm mt-0.5">درخواست خرید قطعات مورد نیاز</div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- گزارشات ثبت‌شده --}}
            @if($task->reports->count() > 0)
            <div class="card-luxury p-6">
                <h2 class="text-lg font-bold text-cream-100 mb-4 pb-3 border-b-2 divider flex items-center justify-between">
                    <span>گزارش‌های ثبت‌شده</span>
                    <span class="text-sm font-normal text-dark-400">{{ $task->reports->count() }} مورد</span>
                </h2>
                <div class="space-y-2">
                    @foreach($task->reports as $report)
                    <div class="flex items-center justify-between py-3 border-b border-dark-700 last:border-0">
                        <div>
                            <div class="text-cream-200 font-medium text-sm">{{ $report->part_name }}</div>
                            <div class="text-xs text-dark-400 mt-0.5">{{ $report->request_number }}</div>
                        </div>
                        <a href="{{ route('reports.show', $report) }}" class="btn-secondary text-xs px-3 py-1.5">مشاهده</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- سفارشات ثبت‌شده --}}
            @if($task->partOrders->count() > 0)
            <div class="card-luxury p-6">
                <h2 class="text-lg font-bold text-cream-100 mb-4 pb-3 border-b-2 divider flex items-center justify-between">
                    <span>سفارشات قطعه</span>
                    <span class="text-sm font-normal text-dark-400">{{ $task->partOrders->count() }} مورد</span>
                </h2>
                <div class="space-y-2">
                    @foreach($task->partOrders as $order)
                    <div class="flex items-center justify-between py-3 border-b border-dark-700 last:border-0">
                        <div>
                            <div class="text-cream-200 font-medium text-sm">{{ $order->equipment_name }}</div>
                            <div class="text-xs text-dark-400 mt-0.5">{{ $order->order_number }}</div>
                        </div>
                        <a href="{{ route('partorders.show', $order) }}" class="btn-secondary text-xs px-3 py-1.5">مشاهده</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>