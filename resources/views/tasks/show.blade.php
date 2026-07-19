<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-2">

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
                    <a href="{{ route('tasks.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 items-start">

                {{-- ستون چپ: اطلاعات ورک‌ریکوست + گزارشات + سفارشات --}}
                <div class="lg:col-span-2 space-y-2">

                    {{-- اطلاعات درخواست کار --}}
                    <div class="card-luxury p-3.5 space-y-3">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره درخواست</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-bold truncate text-right">{{ $task->workRequest->request_number }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ درخواست</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->workRequest->request_date_jalali }}</div>
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
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">نوع درخواست</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->workRequest->request_type }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ثبت‌کننده</label>
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
                    </div>

                    {{-- گزارشات ثبت‌شده --}}
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
                            @forelse($task->reports as $report)
                            <div class="border border-dark-600/40 rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-cream-200 font-medium text-xs truncate">{{ $report->part_name }}</div>
                                    <div class="text-[11px] text-dark-400 truncate">{{ $report->request_number }} — {{ $report->user->name }}</div>
                                </div>
                                <a href="{{ route('reports.show', $report) }}" class="btn-secondary text-xs !px-2.5 !py-1 shrink-0">مشاهده</a>
                            </div>
                            @empty
                            <p class="text-dark-400 text-xs">هنوز گزارشی ثبت نشده.</p>
                            @endforelse
                        </div>
                    </details>

                    {{-- سفارشات قطعه --}}
                    <details class="card-luxury p-3 group" >
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
                            @forelse($task->partOrders as $order)
                            <div class="border border-dark-600/40 rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-cream-200 font-medium text-xs truncate">{{ $order->equipment_name }}</div>
                                    <div class="text-[11px] text-dark-400 truncate">{{ $order->order_number }} — {{ $order->user->name }}</div>
                                </div>
                                <a href="{{ route('partorders.show', $order) }}" class="btn-secondary text-xs !px-2.5 !py-1 shrink-0">مشاهده</a>
                            </div>
                            @empty
                            <p class="text-dark-400 text-xs">هنوز سفارشی ثبت نشده.</p>
                            @endforelse
                        </div>
                    </details>

                </div>

                {{-- ستون راست: اطلاعات تسک + تغییر وضعیت --}}
                <div class="space-y-2">

                    <div class="card-luxury p-2.5 space-y-2">
                        <h3 class="text-sm font-bold text-cream-100">اطلاعات تسک</h3>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تعمیرکار</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->assignedTo->name }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ارسال‌کننده</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $task->createdBy->name }}</div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ ارسال</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">
                                    {{ \Morilog\Jalali\Jalalian::fromDateTime($task->created_at)->format('Y/m/d H:i') }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">وضعیت مشاهده</label>
                                <div class="border border-dark-600/40 rounded-lg px-2 py-1.5 font-medium truncate text-right {{ $task->seen_at ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $task->seen_at ? 'دیده شده' : 'دیده نشده' }}
                                </div>
                            </div>
                        </div>

                        @if($task->note)
                        <div class="pt-1.5 border-t border-dark-700">
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">یادداشت</label>
                            <div class="border-r-2 border-primary-500/50 pr-2 text-cream-200 text-xs">{{ $task->note }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- آپدیت وضعیت --}}
                    <div class="card-luxury p-2.5 space-y-2">
                        <h3 class="text-sm font-bold text-cream-100">تغییر وضعیت</h3>
                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" class="space-y-2">
                            @csrf @method('PATCH')
                            <select name="status" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>⏱ در انتظار</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>🔍 در حال عیب‌یابی</option>
                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>🔧 در حال تعمیر</option>
                            </select>
                            <button type="submit" class="btn-primary w-full !py-1.5 text-xs">ثبت وضعیت</button>
                        </form>
                    </div>

                    <a href="{{ route('workrequests.show', $task->workRequest) }}"
                        class="btn-secondary w-full text-center block !py-1.5 text-xs">
                        مشاهده درخواست اصلی ←
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>