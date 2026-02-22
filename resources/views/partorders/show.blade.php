<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('partorders.index') }}" class="p-2 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                        <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                            جزئیات سفارش قطعه
                        </h1>
                        <p class="text-dark-400 mt-1">شماره: {{ $partorder->order_number }}</p>
                    </div>
                </div>

                @php
                $statusConfig = match($partorder->status) {
                'approved' => ['badge-success', 'تایید شده', '✓'],
                'failed' => ['badge-danger', 'رد شده', '✕'],
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'sent' => ['badge-info', 'ارسال شده', '📦'],
                default => ['badge-info', 'جدید', '★']
                };
                @endphp
                <span class="badge {{ $statusConfig[0] }} text-lg shadow-lg">
                    {{ $statusConfig[2] }} {{ $statusConfig[1] }}
                </span>
            </div>

            <!-- اطلاعات اصلی سفارش -->
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">مشخصات سفارش</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="section-inner">
                        <label class="text-sm text-dark-400 block mb-1">سفارش‌دهنده</label>
                        <p class="text-cream-100 font-semibold">{{ $partorder->user->name }}</p>
                    </div>

                    <div class="section-inner">
                        <label class="text-sm text-dark-400 block mb-1">نام قطعه</label>
                        <p class="text-cream-100 font-semibold">{{ $partorder->part_name }}</p>
                    </div>

                    <div class="section-inner">
                        <label class="text-sm text-dark-400 block mb-1">نام تجهیز</label>
                        <p class="text-cream-100 font-semibold">{{ $partorder->equipment_name }}</p>
                    </div>

                    <div class="section-inner">
                        <label class="text-sm text-dark-400 block mb-1">تاریخ سفارش</label>
                        <p class="text-cream-100 font-semibold">{{ $partorder->order_date_jalali }}</p>
                    </div>

                    <div class="section-inner">
                        <label class="text-sm text-dark-400 block mb-1">نوع بسته‌بندی</label>
                        <p class="text-cream-100 font-semibold">{{ $partorder->package }}</p>
                    </div>

                    <div class="section-inner">
                        <label class="text-sm text-dark-400 block mb-1">تعداد</label>
                        <p class="text-primary-400 font-bold text-lg">{{ $partorder->quantity }}</p>
                    </div>
                </div>
            </div>

            <!-- مشخصات فنی -->
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">جزئیات فنی</h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-sm font-semibold text-cream-200 mb-2 block">مشخصات فنی:</label>
                        <div class="section-inner">
                            <p class="text-cream-100 leading-relaxed whitespace-pre-wrap">{{ $partorder->specifications }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-cream-200 mb-2 block">توضیحات تکمیلی:</label>
                        <div class="section-inner">
                            <p class="text-cream-100 leading-relaxed whitespace-pre-wrap">{{ $partorder->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- وضعیت تاییدها -->
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">وضعیت تاییدها</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @php
                    $approvals = [
                    ['label' => 'پذیرش', 'status' => $partorder->reception_approval, 'role' => 'reception'],
                    ['label' => 'تامین', 'status' => $partorder->supply_approval, 'role' => 'supply'],
                    ['label' => 'مدیر عامل', 'status' => $partorder->ceo_approval, 'role' => 'ceo'],
                    ];
                    @endphp

                    @foreach($approvals as $approval)
                    @php
                    $statusVal = $approval['status'];
                    if ($statusVal === 1 || $statusVal === '1' || $statusVal === true) {
                    $config = ['bg-green-500/25 border-green-500/40', 'text-green-300', '✓ تایید شده'];
                    } elseif ($statusVal === 0 || $statusVal === '0' || $statusVal === false) {
                    $config = ['bg-red-500/25 border-red-500/40', 'text-red-300', '✕ رد شده'];
                    } else {
                    $config = ['bg-dark-800/50 border-dark-600', 'text-dark-400', '⏱ در انتظار'];
                    }
                    @endphp
                    <div class="p-4 rounded-xl border-2 text-center {{ $config[0] }} transition-all duration-300">
                        <div class="text-lg font-bold {{ $config[1] }} mb-1">
                            {{ $approval['label'] }}
                        </div>
                        <div class="text-sm {{ $config[1] }}">
                            {{ $config[2] }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- History of Approvals -->
                @if($partorder->approvals->count() > 0)
                <div class="mt-6 space-y-3">
                    <h3 class="text-sm font-semibold text-cream-200">تاریخچه تاییدها:</h3>
                   @foreach($partorder->approvals as $approval)
                    <div class="section-inner flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-xs shadow-md">
                                {{ mb_substr($approval->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-cream-100 font-medium">{{ $approval->user->name }}</p>
                                <p class="text-xs text-dark-400">{{ $approval->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge {{ $approval->isApproved() ? 'badge-success' : 'badge-danger' }}">
                                {{ $approval->isApproved() ? 'تایید' : 'رد' }}
                            </span>
                            @if($approval->comment)
                            <p class="text-xs text-dark-400 mt-1 max-w-xs">{{ $approval->comment }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- دکمه‌های تایید/رد -->
            @if(auth()->user()->isApprover())
            <div class="card-luxury p-6">
                <h3 class="text-lg font-bold text-cream-100 mb-4">اقدام شما:</h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    <form action="{{ route('partorders.approve', $partorder) }}" method="POST" class="flex-1">
                        @csrf
                        <textarea name="comment" rows="2" placeholder="نظر شما (اختیاری)"
                            class="input-luxury w-full mb-3 resize-none"></textarea>
                        <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            تایید سفارش
                        </button>
                    </form>

                    <form action="{{ route('partorders.reject', $partorder) }}" method="POST" class="flex-1">
                        @csrf
                        <textarea name="comment" rows="2" placeholder="دلیل رد *" required
                            class="input-luxury w-full mb-3 resize-none"></textarea>
                        <button type="submit" class="w-full px-6 py-3 rounded-xl font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center justify-center gap-2 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            رد سفارش
                        </button>
                    </form>
                </div>
            </div>
            @endif
            <x-comments-section
                :reportable="$partorder"
                reportableType="App\Models\PartOrder" />
            <!-- دکمه‌های ویرایش/حذف -->
            @if(auth()->id() == $partorder->user_id && in_array($partorder->status, ['new', 'pending']))
            <div class="flex gap-4 justify-end">
                <a href="{{ route('partorders.edit', $partorder) }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    ویرایش سفارش
                </a>

                <form action="{{ route('partorders.destroy', $partorder) }}" method="POST" onsubmit="return confirm('آیا از حذف این سفارش اطمینان دارید؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-6 py-3 rounded-xl font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center gap-2 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        حذف سفارش
                    </button>
                </form>
            </div>
            @endif

            <!-- اطلاعات اضافی -->
            @if($partorder->last_action_at)
            <div class="card-luxury p-4">
                <div class="flex items-center gap-3 text-sm text-dark-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>آخرین تغییر توسط <span class="text-cream-200 font-medium">{{ $partorder->lastActionBy->name ?? 'سیستم' }}</span> در تاریخ {{ $partorder->last_action_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>