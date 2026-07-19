<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-2">

            {{-- تولبار یکپارچه: فیلتر + نشان تسک‌های جدید --}}
            <div class="card-luxury p-2">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
                    <form method="GET" action="{{ route('my-tasks.index') }}" class="flex-1 flex flex-col lg:flex-row gap-2">
                        <select name="status" class="input-luxury lg:w-48 !py-1.5 !px-2 text-xs">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏱ در انتظار</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>🔍 در حال عیب‌یابی</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>🔧 در حال تعمیر</option>
                        </select>
                        <button type="submit" class="btn-primary lg:w-auto !py-1.5 !px-3.5 text-xs shrink-0">فیلتر</button>
                        @if(request('status'))
                        <a href="{{ route('my-tasks.index') }}" class="btn-secondary lg:w-auto !py-1.5 !px-3.5 text-xs shrink-0">حذف فیلتر</a>
                        @endif
                    </form>

                    @if($unseenCount > 0)
                    <span class="px-2.5 py-1.5 rounded-lg bg-primary-500/20 text-primary-400 border border-primary-500/30 font-bold text-xs shrink-0 text-center">
                        {{ $unseenCount }} تسک جدید
                    </span>
                    @endif
                </div>
            </div>

            {{-- Tasks --}}
            @if($tasks->count() > 0)
            <div class="space-y-1.5">
                @foreach($tasks as $task)
                @php
                $statusConfig = match($task->status) {
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'in_progress' => ['badge-info', 'در حال عیب‌یابی', '🔍'],
                'done' => ['badge-success', 'در حال تعمیر', '🔧'],
                default => ['badge-info', '---', ''],
                };
                $isNew = $task->isNew();
                @endphp

                <div class="card-luxury p-2 hover:shadow-lg hover:shadow-primary-900/15 transition-all duration-200 {{ $isNew ? 'border-r-4 border-primary-500' : '' }}">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-2">

                        {{-- محتوای اصلی --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-cream-100 font-bold text-sm">{{ $task->workRequest->request_number }}</span>
                                <span class="badge {{ $statusConfig[0] }} !text-[10px] !px-2 !py-0.5">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                                @if($isNew)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-500/20 text-primary-400 border border-primary-500/30 animate-pulse">
                                    🔔 جدید
                                </span>
                                @endif
                            </div>
                            <div class="text-cream-300 font-medium text-xs mt-0.5">{{ $task->workRequest->equipment_name }}</div>
                            <div class="text-[11px] text-dark-400">{{ $task->workRequest->device_model }} — سریال: {{ $task->workRequest->serial_number }}</div>
                        </div>

                        {{-- خلاصه اطلاعات: یک ردیف افقی --}}
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] shrink-0">
                            <span class="text-dark-400">نوع: <span class="text-cream-200 font-medium">{{ $task->workRequest->request_type }}</span></span>
                            <span class="text-dark-400">تاریخ: <span class="text-cream-200 font-medium">{{ $task->workRequest->request_date_jalali }}</span></span>
                            <span class="text-dark-400">ارسال‌کننده: <span class="text-cream-200 font-medium">{{ $task->createdBy->name }}</span></span>
                        </div>

                        {{-- آمار گزارشات --}}
                        <div class="flex gap-1.5 shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] border bg-dark-800 text-dark-300 border-dark-600">
                                📋 {{ $task->reports->count() }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] border bg-dark-800 text-dark-300 border-dark-600">
                                📦 {{ $task->partOrders->count() }}
                            </span>
                        </div>

                        {{-- دکمه --}}
                        <a href="{{ route('my-tasks.show', $task) }}"
                            class="btn-primary text-xs !px-3.5 !py-1.5 text-center shrink-0">
                            مشاهده تسک
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-center">{{ $tasks->links() }}</div>

            @else
            <div class="card-luxury p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-3 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-cream-200 mb-1">تسکی وجود ندارد</h3>
                <p class="text-dark-400 text-sm">هنوز هیچ تسکی به شما اختصاص داده نشده است.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>