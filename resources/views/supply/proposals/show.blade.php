<x-app-layout>
    <div class="py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-5">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('supply-proposals.index') }}"
                        class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                        <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent leading-tight">
                            جزئیات پیشنهاد تامین
                        </h1>
                        <p class="text-dark-400 text-sm">{{ $proposal->part_name }} — {{ $proposal->supplier_name }}</p>
                    </div>
                </div>

                @php
                $statusConfig = match($proposal->status) {
                    'approved'  => ['badge-success', 'تایید مدیریت',    '✓'],
                    'rejected'  => ['badge-danger',  'رد شده',           '✕'],
                    'ordered'   => ['badge-info',    'سفارش داده شد',    '📦'],
                    'delivered' => ['badge-success', 'تحویل شد',         '✔✔'],
                    default     => ['badge-warning', 'در انتظار بررسی', '⏱'],
                };
                @endphp
                <span class="badge {{ $statusConfig[0] }} shadow-lg">
                    {{ $statusConfig[2] }} {{ $statusConfig[1] }}
                </span>
            </div>

            {{-- اطلاعات اصلی پیشنهاد --}}
            <div class="card-luxury p-4 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b-2 divider">
                    <div class="w-7 h-7 bg-primary-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-cream-100">مشخصات پیشنهاد</h2>
                </div>

                {{-- ردیف فشرده به‌جای گرید باکس‌دار --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                    <div><span class="text-dark-400">قطعه:</span> <span class="text-cream-100 font-semibold">{{ $proposal->part_name }}</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">فروشنده:</span> <span class="text-cream-100 font-semibold">{{ $proposal->supplier_name }}</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">قیمت واحد:</span> <span class="text-primary-400 font-bold">{{ number_format($proposal->unit_price) }} ت</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">تعداد:</span> <span class="text-cream-100 font-semibold">{{ $proposal->quantity }}</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">جمع کل:</span> <span class="text-primary-400 font-bold">{{ number_format($proposal->unit_price * $proposal->quantity) }} ت</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">تحویل تخمینی:</span> <span class="text-cream-100 font-semibold">{{ $proposal->estimated_delivery_jalali ?? '---' }}</span></div>
                </div>

                @if($proposal->note)
                <div class="section-inner !py-2">
                    <label class="text-xs text-dark-400 block mb-1">یادداشت تامین‌کننده</label>
                    <p class="text-cream-100 text-sm leading-relaxed">{{ $proposal->note }}</p>
                </div>
                @endif

                <x-attachments.panel :model="$proposal" mode="show" />

                <div class="flex items-center gap-2 text-xs text-dark-400 pt-2 border-t divider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    ثبت شده توسط
                    <span class="text-cream-200 font-medium">{{ $proposal->creator->name ?? '---' }}</span>
                    <span>·</span>
                    {{ $proposal->created_at->diffForHumans() }}
                </div>
            </div>

            {{-- سفارش قطعه مرتبط --}}
            <div class="card-luxury p-4 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b-2 divider">
                    <div class="w-7 h-7 bg-primary-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-cream-100">سفارش قطعه مرتبط</h2>
                </div>

                @php $po = $proposal->partOrder; @endphp
                @if($po)
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                    <div><span class="text-dark-400">شماره سفارش:</span> <span class="text-cream-100 font-semibold">{{ $po->order_number }}</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">تجهیز:</span> <span class="text-cream-100 font-semibold">{{ $po->equipment_name }}</span></div>
                    <span class="text-dark-600">|</span>
                    <div><span class="text-dark-400">سفارش‌دهنده:</span> <span class="text-cream-100 font-semibold">{{ $po->user->name ?? '---' }}</span></div>
                </div>

                {{-- جدول قطعات سفارش --}}
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-dark-700/50">
                                <th class="border border-dark-600 px-2 py-1 text-xs text-cream-300 text-center w-8">ردیف</th>
                                <th class="border border-dark-600 px-2 py-1 text-xs text-cream-300 text-right">نام قطعه</th>
                                <th class="border border-dark-600 px-2 py-1 text-xs text-cream-300 text-right">مشخصات</th>
                                <th class="border border-dark-600 px-2 py-1 text-xs text-cream-300 text-center">تعداد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($po->part_name ?? [] as $i => $pname)
                            <tr class="{{ $pname === $proposal->part_name ? 'bg-primary-500/10' : '' }}">
                                <td class="border border-dark-600 px-2 py-1 text-center text-cream-400 text-xs">{{ $i + 1 }}</td>
                                <td class="border border-dark-600 px-2 py-1 text-xs {{ $pname === $proposal->part_name ? 'text-primary-400 font-bold' : 'text-cream-100' }}">
                                    {{ $pname }}
                                    @if($pname === $proposal->part_name)
                                    <span class="text-xs text-primary-500 mr-1">← این پیشنهاد</span>
                                    @endif
                                </td>
                                <td class="border border-dark-600 px-2 py-1 text-cream-100 text-xs">{{ ($po->specifications ?? [])[$i] ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1 text-center text-primary-400 font-bold text-xs">{{ ($po->quantity ?? [])[$i] ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="border border-dark-600 px-2 py-2 text-center text-dark-400 text-xs">قطعه‌ای ثبت نشده</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('partorders.show', $po) }}" class="btn-ghost text-xs inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        مشاهده سفارش کامل
                    </a>
                </div>
                @endif
            </div>

            {{-- یادداشت مدیر --}}
            @if($proposal->ceo_note || $proposal->selected_at)
            <div class="card-luxury p-4 space-y-2 border-primary-500/30">
                <div class="flex items-center gap-2 pb-2 border-b-2 divider">
                    <div class="w-7 h-7 bg-primary-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-cream-100">نظر مدیریت</h2>
                    @if($proposal->selected_at)
                    <span class="text-xs text-dark-400 mr-auto">
                        تاریخ اقدام: <span class="text-cream-300 font-medium">{{ $proposal->selected_at->diffForHumans() }}</span>
                    </span>
                    @endif
                </div>
                @if($proposal->ceo_note)
                <p class="text-cream-100 text-sm leading-relaxed">{{ $proposal->ceo_note }}</p>
                @endif
            </div>
            @endif

            {{-- سایر پیشنهادها برای همین سفارش --}}
            @if($relatedProposals->count() > 0)
            <div class="card-luxury p-4 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b-2 divider">
                    <div class="w-7 h-7 bg-primary-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-cream-100">سایر پیشنهادها برای این سفارش</h2>
                    <span class="badge badge-info text-xs">{{ $relatedProposals->count() }} پیشنهاد</span>
                </div>

                <div class="space-y-2">
                    @foreach($relatedProposals as $rel)
                    @php
                    $relConfig = match($rel->status) {
                        'approved'  => ['badge-success', 'تایید مدیریت',    '✓'],
                        'rejected'  => ['badge-danger',  'رد شده',           '✕'],
                        'ordered'   => ['badge-info',    'سفارش داده شد',    '📦'],
                        'delivered' => ['badge-success', 'تحویل شد',         '✔✔'],
                        default     => ['badge-warning', 'در انتظار بررسی', '⏱'],
                    };
                    @endphp
                    <div class="section-inner !py-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                        <div class="flex-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                            <span class="text-cream-100 font-medium">{{ $rel->part_name }}</span>
                            <span class="text-dark-400 text-xs">·</span>
                            <span class="text-cream-300">{{ $rel->supplier_name }}</span>
                            <span class="badge {{ $relConfig[0] }} text-xs">{{ $relConfig[2] }} {{ $relConfig[1] }}</span>
                            <span class="text-dark-400 text-xs">| قیمت: <span class="text-primary-400 font-bold">{{ number_format($rel->unit_price) }} ت</span></span>
                            <span class="text-dark-400 text-xs">تعداد: <span class="text-cream-200">{{ $rel->quantity }}</span></span>
                            <span class="text-dark-400 text-xs">جمع: <span class="text-primary-400 font-semibold">{{ number_format($rel->unit_price * $rel->quantity) }} ت</span></span>
                        </div>
                        <a href="{{ route('supply-proposals.show', $rel) }}"
                            class="btn-ghost text-xs inline-flex items-center gap-1 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            مشاهده
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- تغییر وضعیت --}}
            <div class="card-luxury p-4 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b-2 divider">
                    <div class="w-7 h-7 bg-primary-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-cream-100">تغییر وضعیت</h2>
                </div>

                <form action="{{ route('supply-proposals.changeStatus', $proposal) }}" method="POST" class="space-y-3">
                    @csrf

                    @if(auth()->user()->isCEO())
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-cream-200">یادداشت مدیریت</label>
                        <textarea name="ceo_note" rows="2"
                            placeholder="نظر یا توضیحات مدیریت..."
                            class="input-luxury w-full resize-none text-sm">{{ old('ceo_note', $proposal->ceo_note) }}</textarea>
                    </div>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        @foreach($statuses as $key => $label)
                        @php
                        $canChange = auth()->user()->isCEO() ||
                            (auth()->user()->isSupply() && in_array($key, \App\Models\SupplyProposal::SUPPLY_STATUSES));
                        $isActive  = $proposal->status === $key;
                        @endphp
                        @if($canChange)
                        <button type="submit" name="status" value="{{ $key }}"
                            class="px-3 py-1.5 rounded-xl text-sm font-semibold transition-all duration-200 border-2
                            {{ $isActive
                                ? 'bg-primary-500/25 text-primary-300 border-primary-500/50 cursor-default'
                                : 'bg-dark-800/60 text-cream-300 border-dark-600 hover:border-primary-500/40 hover:text-primary-400 hover:bg-primary-500/10'
                            }}"
                            {{ $isActive ? 'disabled' : '' }}>
                            {{ $label }}
                            @if($isActive) <span class="mr-1 text-xs">← فعلی</span> @endif
                        </button>
                        @endif
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- دکمه‌های ویرایش/حذف --}}
            @if((auth()->user()->isSupply() && $proposal->created_by === auth()->id() && $proposal->status === 'pending') || auth()->user()->isCEO())
            <div class="flex gap-3 justify-end">
                <a href="{{ route('supply-proposals.edit', $proposal) }}" class="btn-secondary text-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    ویرایش پیشنهاد
                </a>
                <form action="{{ route('supply-proposals.destroy', $proposal) }}" method="POST"
                    onsubmit="return confirm('آیا از حذف این پیشنهاد اطمینان دارید؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center gap-2 shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        حذف پیشنهاد
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>