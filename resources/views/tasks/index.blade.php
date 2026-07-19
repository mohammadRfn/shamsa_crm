<x-app-layout>
    <div class="py-5 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-3">

            {{-- تولبار یکپارچه: ارسال تسک + فیلترها همه توی یه ردیف --}}
            <div class="card-luxury p-2.5">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
                    <a href="{{ route('tasks.create') }}" class="btn-primary inline-flex items-center justify-center gap-2 !py-2 !px-4 text-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        ارسال تسک جدید
                    </a>

                    <form method="GET" action="{{ route('tasks.index') }}" class="flex-1 flex flex-col lg:flex-row gap-2">
                        <select name="status" class="input-luxury lg:w-44 !py-2 !px-3 text-sm">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="pending" {{ request('status') == 'pending'     ? 'selected' : '' }}>در انتظار</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>در حال عیب‌یابی</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>در حال تعمیر</option>
                        </select>
                        <select name="assigned_to" class="input-luxury lg:w-48 !py-2 !px-3 text-sm">
                            <option value="">همه تعمیرکاران</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ request('assigned_to') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-primary lg:w-auto !py-2 !px-4 text-sm shrink-0">فیلتر</button>
                        @if(request('status') || request('assigned_to'))
                        <a href="{{ route('tasks.index') }}" class="btn-secondary lg:w-auto !py-2 !px-4 text-sm shrink-0">حذف فیلتر</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- List --}}
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
                @endphp
                <div class="card-luxury p-2.5 hover:shadow-lg hover:shadow-primary-900/15 transition-all duration-200">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-2">

                        {{-- اطلاعات ورک ریکوست --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                <span class="text-cream-100 font-bold text-sm">{{ $task->workRequest->request_number }}</span>
                                <span class="badge {{ $statusConfig[0] }} !text-[10px] !px-2 !py-0.5">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                                @if($task->isNew())
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-500/20 text-primary-400 border border-primary-500/30">جدید</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-dark-400">{{ $task->workRequest->equipment_name }} · {{ $task->workRequest->device_model }} — {{ $task->workRequest->serial_number }}</div>
                        </div>

                        {{-- تعمیرکار --}}
                        <div class="flex items-center gap-1.5 text-[11px] shrink-0">
                            <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $task->assignedTo->name }}</span>
                        </div>

                        {{-- گزارشات --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] border bg-dark-800 text-dark-300 border-dark-600">
                                {{ $task->reports->count() }} گزارش
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] border bg-dark-800 text-dark-300 border-dark-600">
                                {{ $task->partOrders->count() }} سفارش
                            </span>
                        </div>

                        {{-- تاریخ --}}
                        <div class="text-[11px] text-dark-400 shrink-0">
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($task->created_at)->format('Y/m/d') }}
                        </div>

                        {{-- دکمه --}}
                        <a href="{{ route('tasks.show', $task) }}"
                            class="btn-secondary !px-3 !py-1.5 text-xs shrink-0">
                            مشاهده
                        </a>
                        @if($task->status === 'pending')
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                            onsubmit="return confirm('آیا از حذف این تسک اطمینان دارید؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1.5 text-xs bg-red-500/10 text-red-400 hover:bg-red-500/25 rounded-lg border border-red-500/25 transition-all shrink-0">
                                حذف
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if($tasks->hasPages())
            <div class="flex justify-center pt-3 border-t border-dark-700">
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