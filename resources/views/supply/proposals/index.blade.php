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
            <div class="space-y-4">
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
                <div class="card-luxury p-6 hover:shadow-2xl hover:shadow-primary-900/20 transition-all duration-300 group">
                    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">

                        {{-- اطلاعات اصلی --}}
                        <div class="flex-1 space-y-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-lg font-bold text-cream-100 group-hover:text-primary-400 transition-colors">
                                    {{ $proposal->part_name }}
                                </h3>
                                <span class="badge {{ $statusConfig[0] }}">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-dark-400">فروشنده:</span>
                                    <span class="text-cream-200 font-medium truncate">{{ $proposal->supplier_name }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-dark-400">قیمت واحد:</span>
                                    <span class="text-primary-400 font-bold">{{ number_format($proposal->unit_price) }} ت</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span class="text-dark-400">تعداد:</span>
                                    <span class="text-cream-200 font-medium">{{ $proposal->quantity }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-dark-400">تحویل:</span>
                                    <span class="text-cream-200 font-medium">{{ $proposal->estimated_delivery_jalali ?? '---' }}</span>
                                </div>
                            </div>

                            {{-- سفارش مرتبط --}}
                            <div class="section-inner flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-dark-400">سفارش قطعه:</span>
                                <span class="text-cream-200 font-medium">{{ $proposal->partOrder->order_number ?? '---' }}</span>
                                <span class="text-dark-400">—</span>
                                <span class="text-cream-300">{{ $proposal->partOrder->equipment_name ?? '' }}</span>
                            </div>
                        </div>

                        {{-- اکشن‌ها --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('supply-proposals.show', $proposal) }}"
                                class="btn-secondary py-2 px-4 inline-flex items-center gap-1 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                مشاهده
                            </a>
                            @if(auth()->user()->isSupply() && $proposal->created_by === auth()->id() && $proposal->status === 'pending')
                            <a href="{{ route('supply-proposals.edit', $proposal) }}"
                                class="p-2 rounded-lg bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30 transition-all border-2 border-yellow-500/30" title="ویرایش">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('supply-proposals.destroy', $proposal) }}" method="POST"
                                onsubmit="return confirm('آیا از حذف این پیشنهاد اطمینان دارید؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-2 rounded-lg bg-red-500/20 text-red-400 hover:bg-red-500/30 transition-all border-2 border-red-500/30" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    {{-- قیمت کل --}}
                    <div class="mt-4 pt-4 border-t-2 divider flex items-center justify-between">
                        <span class="text-sm text-dark-400">
                            ثبت شده توسط:
                            <span class="text-cream-300 font-medium">{{ $proposal->creator->name ?? '---' }}</span>
                            <span class="mx-2">·</span>
                            {{ $proposal->created_at->diffForHumans() }}
                        </span>
                        <div class="text-sm">
                            <span class="text-dark-400">جمع کل:</span>
                            <span class="text-primary-400 font-bold text-base mr-1">
                                {{ number_format($proposal->unit_price * $proposal->quantity) }} تومان
                            </span>
                        </div>
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