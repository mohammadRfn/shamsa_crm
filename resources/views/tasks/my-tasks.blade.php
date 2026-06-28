<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        تسک‌های من
                    </h1>
                    <p class="text-dark-400 mt-2">وظایف ارجاع‌داده‌شده به شما</p>
                </div>
                @if($unseenCount > 0)
                <span class="px-4 py-2 rounded-xl bg-primary-500/20 text-primary-400 border border-primary-500/30 font-bold text-sm">
                    {{ $unseenCount }} تسک جدید
                </span>
                @endif
            </div>

            {{-- Filter --}}
            <div class="card-luxury p-4">
                <form method="GET" action="{{ route('my-tasks.index') }}" class="flex flex-wrap gap-3">
                    <select name="status" class="input-luxury lg:w-44">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="pending" {{ request('status') == 'pending'     ? 'selected' : '' }}>⏱ در انتظار</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>🔍 در حال عیب‌یابی</option>
                        <option value="done" {{ request('status') == 'done'        ? 'selected' : '' }}>🔧 در حال تعمیر</option>
                    </select>
                    <button type="submit" class="btn-primary">فیلتر</button>
                    @if(request('status'))
                    <a href="{{ route('my-tasks.index') }}" class="btn-secondary">حذف فیلتر</a>
                    @endif
                </form>
            </div>

            {{-- Tasks --}}
            @if($tasks->count() > 0)
            <div class="space-y-4">
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

                <div class="card-luxury p-6 hover:shadow-2xl hover:shadow-primary-900/20 transition-all duration-300 {{ $isNew ? 'border-r-4 border-primary-500' : '' }}">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-4">

                        {{-- محتوای اصلی --}}
                        <div class="flex-1 space-y-4">
                            {{-- تیتر --}}
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-cream-100 font-bold text-lg">{{ $task->workRequest->request_number }}</span>
                                        <span class="badge {{ $statusConfig[0] }} text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                                        @if($isNew)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-500/20 text-primary-400 border border-primary-500/30 animate-pulse">
                                            🔔 جدید
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-cream-300 font-medium mt-0.5">{{ $task->workRequest->equipment_name }}</div>
                                    <div class="text-sm text-dark-400">{{ $task->workRequest->device_model }} — سریال: {{ $task->workRequest->serial_number }}</div>
                                </div>
                            </div>

                            {{-- خلاصه اطلاعات --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 section-inner text-sm">
                                <div>
                                    <span class="text-dark-400 text-xs">نوع درخواست</span>
                                    <div class="text-cream-200 font-medium mt-0.5">{{ $task->workRequest->request_type }}</div>
                                </div>
                                <div>
                                    <span class="text-dark-400 text-xs">تاریخ درخواست</span>
                                    <div class="text-cream-200 font-medium mt-0.5">{{ $task->workRequest->request_date_jalali }}</div>
                                </div>
                                <div>
                                    <span class="text-dark-400 text-xs">ارسال‌کننده تسک</span>
                                    <div class="text-cream-200 font-medium mt-0.5">{{ $task->createdBy->name }}</div>
                                </div>
                            </div>

                            {{-- شرح کار --}}
                            @if($task->workRequest->work_description)
                            <div class="text-sm">
                                <span class="text-dark-400 text-xs">شرح کار:</span>
                                <p class="text-cream-300 mt-0.5 line-clamp-2">{{ $task->workRequest->work_description }}</p>
                            </div>
                            @endif

                            @if($task->note)
                            <div class="text-sm border-r-2 border-primary-500/40 pr-3">
                                <span class="text-dark-400 text-xs">یادداشت پذیرش:</span>
                                <p class="text-cream-300 mt-0.5">{{ $task->note }}</p>
                            </div>
                            @endif

                            {{-- آمار گزارشات --}}
                            <div class="flex gap-3">
                                <span class="px-3 py-1 rounded-full text-xs border bg-dark-800 text-dark-300 border-dark-600">
                                    📋 {{ $task->reports->count() }} گزارش کار
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs border bg-dark-800 text-dark-300 border-dark-600">
                                    📦 {{ $task->partOrders->count() }} سفارش قطعه
                                </span>
                            </div>
                        </div>

                        {{-- دکمه‌ها --}}
                        <div class="flex lg:flex-col gap-2 shrink-0">
                            <a href="{{ route('my-tasks.show', $task) }}"
                                class="btn-primary text-sm px-4 py-2 text-center">
                                مشاهده تسک
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-center">{{ $tasks->links() }}</div>

            @else
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">تسکی وجود ندارد</h3>
                <p class="text-dark-400">هنوز هیچ تسکی به شما اختصاص داده نشده است.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>