<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        پیشنهادهای تامین
                    </h1>
                    <p class="text-dark-400 mt-2">مدیریت و پیگیری پیشنهادهای قیمت قطعات</p>
                </div>
                @if(auth()->user()->isSupply())
                <a href="{{ route('supply-proposals.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    ثبت پیشنهاد جدید
                </a>
                @endif
            </div>

            {{-- فیلتر --}}
            <div class="card-luxury p-6">
                <form method="GET" action="{{ route('supply-proposals.index') }}" class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="جستجو در نام فروشنده، قطعه، شماره سفارش..."
                                class="input-luxury w-full pr-12">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-dark-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <select name="status" class="input-luxury lg:w-52">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary lg:w-auto">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        جستجو
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('supply-proposals.index') }}" class="btn-secondary lg:w-auto">حذف فیلتر</a>
                    @endif
                </form>
            </div>

            @if($proposals->count() > 0)
            <div class="space-y-2.5">
                @foreach($proposals as $proposal)
                @php
                $statusConfig = match($proposal->status) {
                    'approved'  => ['badge-success', 'تایید مدیریت', '✓'],
                    'rejected'  => ['badge-danger',  'رد شده',        '✕'],
                    'ordered'   => ['badge-info',    'سفارش داده شد', '📦'],
                    'delivered' => ['badge-success', 'تحویل شد',      '✔✔'],
                    default     => ['badge-warning', 'در انتظار بررسی','⏱'],
                };
                @endphp
                <div class="card-luxury p-3 sm:p-4 hover:shadow-lg hover:shadow-primary-900/10 transition-all duration-200 group">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">

                        {{-- عنوان + بج + متادیتا فشرده --}}
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm sm:text-base font-bold text-cream-100 group-hover:text-primary-400 transition-colors">
                                    {{ $proposal->part_name }}
                                </h3>
                                <span class="badge {{ $statusConfig[0] }} text-[10px] px-2 py-0.5 leading-none">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                                <span class="inline-flex items-center gap-1 text-dark-400">
                                    <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-cream-200 font-medium">{{ $proposal->supplier_name }}</span>
                                </span>

                                <span class="inline-flex items-center gap-1 text-dark-400">
                                    <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-primary-400 font-bold">{{ number_format($proposal->unit_price) }} ت</span>
                                    <span class="text-dark-500">×{{ $proposal->quantity }}</span>
                                </span>

                                <span class="inline-flex items-center gap-1 text-dark-400">
                                    <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-cream-200">{{ $proposal->estimated_delivery_jalali ?? '---' }}</span>
                                </span>

                                <span class="inline-flex items-center gap-1 text-dark-400 min-w-0">
                                    <svg class="w-3.5 h-3.5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-cream-200 truncate">{{ $proposal->partOrder->order_number ?? '---' }}</span>
                                    <span class="text-dark-600">·</span>
                                    <span class="text-cream-300 truncate">{{ $proposal->partOrder->equipment_name ?? '' }}</span>
                                </span>
                            </div>
                        </div>

                        {{-- جمع کل --}}
                        <div class="text-xs sm:text-sm shrink-0 sm:text-left">
                            <span class="text-dark-400">جمع کل:</span>
                            <span class="text-primary-400 font-bold text-sm sm:text-base">{{ number_format($proposal->unit_price * $proposal->quantity) }}</span>
                            <span class="text-dark-400">تومان</span>
                        </div>

                        {{-- اکشن‌ها --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('supply-proposals.show', $proposal) }}"
                                class="p-1.5 rounded-lg bg-dark-800 text-dark-300 hover:text-primary-400 hover:bg-dark-700 transition-all border border-dark-700" title="مشاهده">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            @if(auth()->user()->isSupply() && $proposal->created_by === auth()->id() && $proposal->status === 'pending')
                            <a href="{{ route('supply-proposals.edit', $proposal) }}"
                                class="p-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('supply-proposals.destroy', $proposal) }}" method="POST"
                                onsubmit="return confirm('آیا از حذف این پیشنهاد اطمینان دارید؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25" title="حذف">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    {{-- ثبت‌کننده و تاریخ: خیلی ریز، فقط یک خط پایین --}}
                    <div class="mt-1.5 pt-1.5 border-t border-dark-700/40 flex items-center gap-1 text-[11px] text-dark-500">
                        <span>{{ $proposal->creator->name ?? '---' }}</span>
                        <span>·</span>
                        <span>{{ $proposal->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-center">{{ $proposals->links() }}</div>

            @else
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">پیشنهادی یافت نشد</h3>
                <p class="text-dark-400 mb-6">هنوز هیچ پیشنهادی ثبت نشده است</p>
                @if(auth()->user()->isSupply())
                <a href="{{ route('supply-proposals.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اولین پیشنهاد را ثبت کنید
                </a>
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>