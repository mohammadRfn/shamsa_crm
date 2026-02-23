<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                       سفارش قطعه
                    </h1>
                    <p class="text-dark-400 mt-2">مدیریت و پیگیری سفارش قطعات یدکی</p>
                </div>

                @if(auth()->user()->isTechnician())
                <a href="{{ route('partorders.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    ثبت سفارش جدید
                </a>
                @endif
            </div>

            <!-- Search & Filter Bar -->
            <div class="card-luxury p-6">
                <form method="GET" action="{{ route('partorders.index') }}" class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="جستجو در نام قطعه، شماره سفارش، تجهیزات یا نام تکنسین..."
                                class="input-luxury w-full pr-12">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-dark-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <select name="status" class="input-luxury lg:w-48">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جدید</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تایید شده</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>رد شده</option>
                    </select>

                    <button type="submit" class="btn-primary lg:w-auto">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        جستجو
                    </button>

                    @if(request('search') || request('status'))
                    <a href="{{ route('partorders.index') }}" class="btn-secondary lg:w-auto">
                        حذف فیلتر
                    </a>
                    @endif
                </form>
            </div>

            <!-- Orders Grid -->
            @if($partOrders->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($partOrders as $order)
                <div class="card-luxury p-6 hover:shadow-2xl hover:shadow-primary-900/20 hover:scale-[1.02] transition-all duration-300 group">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4 pb-4 border-b-2 divider">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-cream-100 group-hover:text-primary-400 transition-colors">
                                {{ implode('، ', $order->part_name ?? []) }}
                            </h3>
                            <p class="text-sm text-dark-400 mt-1">سفارش: {{ $order->order_number }}</p>
                        </div>

                        @php
                        $statusConfig = match($order->status) {
                        'approved' => ['badge-success', 'تایید شده', '✓'],
                        'failed' => ['badge-danger', 'رد شده', '✕'],
                        'pending' => ['badge-warning', 'در انتظار', '⏱'],
                        'sent' => ['badge-info', 'ارسال شده', '📦'],
                        default => ['badge-info', 'جدید', '★']
                        };
                        @endphp
                        <span class="badge {{ $statusConfig[0] }}">
                            {{ $statusConfig[2] }} {{ $statusConfig[1] }}
                        </span>
                    </div>

                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-dark-400">تکنسین:</span>
                            <span class="text-cream-200 font-medium">{{ $order->user->name }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-dark-400">تاریخ:</span>
                            <span class="text-cream-200 font-medium">{{ $order->order_date_jalali }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span class="text-dark-400">تجهیز:</span>
                            <span class="text-cream-200 font-medium truncate">{{ Str::limit($order->equipment_name, 15) }}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="text-dark-400">تعداد:</span>
                            <span class="text-primary-400 font-bold">{{ implode('، ', array_map('strval', $order->quantity ?? [])) }}</span>
                        </div>
                    </div>

                    <!-- Package Info -->
                    <div class="section-inner mb-4">
                        <div class="text-xs text-dark-400 mb-1">بسته‌بندی:</div>
                        <div class="text-sm text-cream-100 font-medium">{{ implode('، ', $order->package ?? []) }}</div>
                    </div>

                    <!-- Approval Status -->
                    <div class="flex gap-2 mb-4 flex-wrap">
                        @php
                        $approvals = [
                        ['label' => 'پذیرش', 'status' => $order->reception_approval],
                        ['label' => 'تامین', 'status' => $order->supply_approval],
                        ['label' => 'مدیر', 'status' => $order->ceo_approval],
                        ];
                        @endphp

                        @foreach($approvals as $approval)
                        @php
                        $statusVal = $approval['status'];

                        // تبدیل صریح برای جلوگیری از مشکل type
                        if ($statusVal === 1 || $statusVal === '1' || $statusVal === true) {
                        $approvalClass = 'bg-green-500/20 text-green-400 border-green-500/30';
                        $icon = '✓';
                        } elseif ($statusVal === 0 || $statusVal === '0' || $statusVal === false) {
                        $approvalClass = 'bg-red-500/20 text-red-400 border-red-500/30';
                        $icon = '✕';
                        } else {
                        $approvalClass = 'bg-dark-700 text-dark-400 border-dark-600';
                        $icon = '⏱';
                        }
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $approvalClass }}">
                            {{ $icon }} {{ $approval['label'] }}
                        </span>
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4 border-t-2 divider">
                        <a href="{{ route('partorders.show', $order) }}"
                            class="flex-1 btn-secondary text-center py-2">
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            مشاهده
                        </a>

                        @if(auth()->id() == $order->user_id && in_array($order->status, ['new', 'pending']))
                        <a href="{{ route('partorders.edit', $order) }}"
                            class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-lg hover:bg-yellow-500/30 transition-all border-2 border-yellow-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <form action="{{ route('partorders.destroy', $order) }}"
                            method="POST"
                            onsubmit="return confirm('آیا از حذف این سفارش اطمینان دارید؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-all border-2 border-red-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $partOrders->links() }}
            </div>

            @else
            <!-- Empty State -->
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">سفارشی یافت نشد</h3>
                <p class="text-dark-400 mb-6">هنوز هیچ سفارشی ثبت نشده است</p>
                @if(auth()->user()->isTechnician())
                <a href="{{ route('partorders.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اولین سفارش را ثبت کنید
                </a>
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>