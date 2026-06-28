<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('tasks.index') }}" class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-cream-100 hover:bg-dark-700 border border-dark-700 transition-all">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ستون چپ: اطلاعات ورک ریکوست --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- اطلاعات اصلی --}}
                    <div class="card-luxury p-6">
                        <h2 class="text-lg font-bold text-cream-100 mb-4 pb-3 border-b-2 divider">اطلاعات درخواست کار</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">شماره درخواست</div>
                                <div class="text-cream-100 font-bold">{{ $task->workRequest->request_number }}</div>
                            </div>
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">تاریخ درخواست</div>
                                <div class="text-cream-100 font-medium">{{ $task->workRequest->request_date_jalali }}</div>
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
                                <div class="text-dark-400 text-xs mb-0.5">نوع درخواست</div>
                                <div class="text-cream-100 font-medium">{{ $task->workRequest->request_type }}</div>
                            </div>
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">ثبت‌کننده</div>
                                <div class="text-cream-100 font-medium">{{ $task->workRequest->user->name ?? '---' }}</div>
                            </div>
                        </div>

                        @if($task->workRequest->work_description)
                        <div class="mt-4">
                            <div class="text-dark-400 text-xs mb-1">شرح کار درخواستی</div>
                            <div class="section-inner text-cream-200 text-sm">{{ $task->workRequest->work_description }}</div>
                        </div>
                        @endif

                        @if($task->workRequest->issue_description)
                        <div class="mt-3">
                            <div class="text-dark-400 text-xs mb-1">شرح ایراد اعلامی</div>
                            <div class="section-inner text-cream-200 text-sm">{{ $task->workRequest->issue_description }}</div>
                        </div>
                        @endif

                        @if($task->workRequest->workflow_description)
                        <div class="mt-3">
                            <div class="text-dark-400 text-xs mb-1">شرح گردش کار</div>
                            <div class="section-inner text-cream-200 text-sm">{{ $task->workRequest->workflow_description }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- گزارشات تعمیرکار --}}
                    <div class="card-luxury p-6">
                        <h2 class="text-lg font-bold text-cream-100 mb-4 pb-3 border-b-2 divider flex items-center justify-between">
                            <span>گزارش‌های ثبت‌شده</span>
                            <span class="text-sm font-normal text-dark-400">{{ $task->reports->count() }} مورد</span>
                        </h2>
                        @forelse($task->reports as $report)
                        <div class="flex items-center justify-between py-3 border-b border-dark-700 last:border-0">
                            <div>
                                <div class="text-cream-200 font-medium text-sm">{{ $report->part_name }}</div>
                                <div class="text-xs text-dark-400 mt-0.5">{{ $report->request_number }} — {{ $report->user->name }}</div>
                            </div>
                            <a href="{{ route('reports.show', $report) }}" class="text-primary-400 hover:text-primary-300 text-xs transition-colors">
                                مشاهده ←
                            </a>
                        </div>
                        @empty
                        <p class="text-dark-400 text-sm">هنوز گزارشی ثبت نشده.</p>
                        @endforelse
                    </div>

                    {{-- سفارشات قطعه --}}
                    <div class="card-luxury p-6">
                        <h2 class="text-lg font-bold text-cream-100 mb-4 pb-3 border-b-2 divider flex items-center justify-between">
                            <span>سفارشات قطعه</span>
                            <span class="text-sm font-normal text-dark-400">{{ $task->partOrders->count() }} مورد</span>
                        </h2>
                        @forelse($task->partOrders as $order)
                        <div class="flex items-center justify-between py-3 border-b border-dark-700 last:border-0">
                            <div>
                                <div class="text-cream-200 font-medium text-sm">{{ $order->equipment_name }}</div>
                                <div class="text-xs text-dark-400 mt-0.5">{{ $order->order_number }} — {{ $order->user->name }}</div>
                            </div>
                            <a href="{{ route('partorders.show', $order) }}" class="text-primary-400 hover:text-primary-300 text-xs transition-colors">
                                مشاهده ←
                            </a>
                        </div>
                        @empty
                        <p class="text-dark-400 text-sm">هنوز سفارشی ثبت نشده.</p>
                        @endforelse
                    </div>

                </div>

                {{-- ستون راست: اطلاعات تسک --}}
                <div class="space-y-4">

                    <div class="card-luxury p-5">
                        <h3 class="font-bold text-cream-100 mb-3 pb-2 border-b divider text-sm">اطلاعات تسک</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">تعمیرکار</div>
                                <div class="text-cream-100 font-medium">{{ $task->assignedTo->name }}</div>
                            </div>
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">ارسال‌کننده</div>
                                <div class="text-cream-100 font-medium">{{ $task->createdBy->name }}</div>
                            </div>
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">تاریخ ارسال</div>
                                <div class="text-cream-100 font-medium">
                                    {{ \Morilog\Jalali\Jalalian::fromDateTime($task->created_at)->format('Y/m/d H:i') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-dark-400 text-xs mb-0.5">وضعیت مشاهده</div>
                                <div class="font-medium {{ $task->seen_at ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $task->seen_at ? 'دیده شده' : 'دیده نشده' }}
                                </div>
                            </div>
                        </div>

                        @if($task->note)
                        <div class="mt-3 pt-3 border-t border-dark-700">
                            <div class="text-dark-400 text-xs mb-1">یادداشت</div>
                            <div class="text-cream-200 text-sm">{{ $task->note }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- آپدیت وضعیت --}}
                    <div class="card-luxury p-5">
                        <h3 class="font-bold text-cream-100 mb-3 text-sm">تغییر وضعیت</h3>
                        <form method="POST" action="{{ route('tasks.updateStatus', $task) }}" class="space-y-3">
                            @csrf @method('PATCH')
                            <select name="status" class="input-luxury w-full text-sm">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>⏱ در انتظار</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>🔍 در حال عیب‌یابی</option>
                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>🔧 در حال تعمیر</option>
                            </select>
                            <button type="submit" class="btn-primary w-full text-sm py-2">ثبت وضعیت</button>
                        </form>
                    </div>

                    <a href="{{ route('workrequests.show', $task->workRequest) }}"
                        class="btn-secondary w-full text-center block text-sm py-2.5">
                        مشاهده درخواست اصلی ←
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>