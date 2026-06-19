<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        تسک‌ها
                    </h1>
                    <p class="text-dark-400 mt-2">مدیریت و پیگیری تسک‌های ارسال‌شده به تعمیرکاران</p>
                </div>
                <a href="{{ route('tasks.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    ارسال تسک جدید
                </a>
            </div>

            {{-- Filter --}}
            <div class="card-luxury p-4">
                <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap gap-3">
                    <select name="status" class="input-luxury lg:w-44">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="pending" {{ request('status') == 'pending'     ? 'selected' : '' }}>در انتظار</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>در حال انجام</option>
                        <option value="done" {{ request('status') == 'done'        ? 'selected' : '' }}>انجام شده</option>
                    </select>
                    <select name="assigned_to" class="input-luxury lg:w-48">
                        <option value="">همه تعمیرکاران</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ request('assigned_to') == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary">فیلتر</button>
                    @if(request('status') || request('assigned_to'))
                    <a href="{{ route('tasks.index') }}" class="btn-secondary">حذف فیلتر</a>
                    @endif
                </form>
            </div>

            {{-- List --}}
            @if($tasks->count() > 0)
            <div class="space-y-3">
                @foreach($tasks as $task)
                @php
                $statusConfig = match($task->status) {
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'in_progress' => ['badge-info', 'در حال انجام', '🔧'],
                'done' => ['badge-success', 'انجام شده', '✓'],
                default => ['badge-info', '---', ''],
                };
                @endphp
                <div class="card-luxury p-5 hover:shadow-xl hover:shadow-primary-900/15 transition-all duration-200">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                        {{-- اطلاعات ورک ریکوست --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs text-dark-400">شماره درخواست:</span>
                                <span class="text-cream-100 font-bold">{{ $task->workRequest->request_number }}</span>
                                <span class="badge {{ $statusConfig[0] }} text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                                @if($task->isNew())
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-primary-500/20 text-primary-400 border border-primary-500/30">جدید</span>
                                @endif
                            </div>
                            <div class="text-cream-200 font-medium">{{ $task->workRequest->equipment_name }}</div>
                            <div class="text-sm text-dark-400 mt-1">{{ $task->workRequest->device_model }} — {{ $task->workRequest->serial_number }}</div>
                        </div>

                        {{-- تعمیرکار --}}
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-dark-400">تعمیرکار:</span>
                            <span class="text-cream-200 font-medium">{{ $task->assignedTo->name }}</span>
                        </div>

                        {{-- گزارشات --}}
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2.5 py-1 rounded-full text-xs border bg-dark-800 text-dark-300 border-dark-600">
                                {{ $task->reports->count() }} گزارش
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs border bg-dark-800 text-dark-300 border-dark-600">
                                {{ $task->partOrders->count() }} سفارش
                            </span>
                        </div>

                        {{-- تاریخ --}}
                        <div class="text-xs text-dark-400 shrink-0">
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($task->created_at)->format('Y/m/d') }}
                        </div>

                        {{-- دکمه --}}
                        <a href="{{ route('tasks.show', $task) }}"
                            class="btn-secondary px-4 py-2 text-sm shrink-0">
                            مشاهده
                        </a>
                        @if($task->status === 'pending')
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                            onsubmit="return confirm('آیا از حذف این تسک اطمینان دارید؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 text-sm bg-red-500/10 text-red-400 hover:bg-red-500/25 rounded-lg border border-red-500/25 transition-all shrink-0">
                                حذف
                            </button>
                        </form>
                        @endif
                    </div>

                    @if($task->note)
                    <div class="mt-3 pt-3 border-t border-dark-700 text-sm text-dark-300">
                        <span class="text-dark-500">یادداشت: </span>{{ $task->note }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($tasks->hasPages())
            <div class="flex justify-center pt-4 border-t-2 divider">
                {{ $tasks->links() }}
            </div>
            @endif
            @else
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">تسکی یافت نشد</h3>
                <p class="text-dark-400 mb-6">هنوز هیچ تسکی ارسال نشده است</p>
                <a href="{{ route('tasks.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اولین تسک را ارسال کنید
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>