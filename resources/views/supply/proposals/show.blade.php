<x-app-layout>
    <div class="py-3 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-2">

            {{-- ═══ هدر فشرده ═══ --}}
            @php
            $statusConfig = match($proposal->status) {
            'approved' => ['badge-success', 'تایید مدیریت', '✓'],
            'rejected' => ['badge-danger', 'رد شده', '✕'],
            'ordered' => ['badge-info', 'سفارش داده شد', '📦'],
            'delivered' => ['badge-success', 'تحویل شد', '✔✔'],
            default => ['badge-warning', 'در انتظار بررسی', '⏱'],
            };
            $canEditDelete = (auth()->user()->isSupply() && $proposal->created_by === auth()->id() && $proposal->status === 'pending') || auth()->user()->isCEO();
            @endphp
            <div class="card-luxury p-2 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('supply-proposals.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                        <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold text-cream-100 truncate">جزئیات پیشنهاد تامین — {{ $proposal->part_name }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="badge {{ $statusConfig[0] }} !text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                    @if($canEditDelete)
                    <a href="{{ route('supply-proposals.edit', $proposal) }}" class="p-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="{{ route('supply-proposals.destroy', $proposal) }}" method="POST" onsubmit="return confirm('آیا از حذف این پیشنهاد اطمینان دارید؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25" title="حذف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- مشخصات پیشنهاد --}}
            <div class="card-luxury p-2.5 space-y-2">
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-2 text-xs">
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">نام قطعه</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $proposal->part_name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">فروشنده</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $proposal->supplier_name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">قیمت واحد</label>
                        <div class="border border-dark-600/40 text-primary-400 rounded-lg px-2 py-1 font-bold truncate text-right">{{ number_format($proposal->unit_price) }} ت</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تعداد</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $proposal->quantity }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">جمع کل</label>
                        <div class="border border-dark-600/40 text-primary-400 rounded-lg px-2 py-1 font-bold truncate text-right">{{ number_format($proposal->unit_price * $proposal->quantity) }} ت</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تحویل تخمینی</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $proposal->estimated_delivery_jalali ?? '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ثبت‌کننده</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $proposal->creator->name ?? '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ ثبت</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $proposal->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                @if($proposal->note)
                <div>
                    <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">یادداشت تامین‌کننده</label>
                    <div class="w-full border border-dark-600/40 text-cream-100 rounded-lg p-1.5 text-xs min-h-[36px] whitespace-pre-wrap text-right resize-y overflow-auto">{{ $proposal->note }}</div>
                </div>
                @endif
            </div>

            {{-- ═══ سفارش قطعه مرتبط + تغییر وضعیت — کنار هم، هم‌ارتفاع ═══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 items-stretch">

                {{-- سفارش قطعه مرتبط --}}
                @php $po = $proposal->partOrder; @endphp
                @if($po)
                <div class="card-luxury p-2.5 space-y-2 h-full flex flex-col">
                    <h3 class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        سفارش قطعه مرتبط
                    </h3>

                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره سفارش</label>
                            <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $po->order_number }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تجهیز</label>
                            <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $po->equipment_name }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">سفارش‌دهنده</label>
                            <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right">{{ $po->user->name ?? '---' }}</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-32 overflow-y-auto rounded-lg">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="bg-dark-700/50">
                                    <th class="border border-dark-600 px-2 py-1 text-cream-300 text-center w-8">ردیف</th>
                                    <th class="border border-dark-600 px-2 py-1 text-cream-300 text-right">نام قطعه</th>
                                    <th class="border border-dark-600 px-2 py-1 text-cream-300 text-right">مشخصات</th>
                                    <th class="border border-dark-600 px-2 py-1 text-cream-300 text-center">تعداد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($po->part_name ?? [] as $i => $pname)
                                <tr class="{{ $pname === $proposal->part_name ? 'bg-primary-500/10' : '' }}">
                                    <td class="border border-dark-600 px-2 py-1 text-center text-cream-400">{{ $i + 1 }}</td>
                                    <td class="border border-dark-600 px-2 py-1 {{ $pname === $proposal->part_name ? 'text-primary-400 font-bold' : 'text-cream-100' }}">
                                        {{ $pname }}
                                        @if($pname === $proposal->part_name)
                                        <span class="text-primary-500 mr-1">← این پیشنهاد</span>
                                        @endif
                                    </td>
                                    <td class="border border-dark-600 px-2 py-1 text-cream-100">{{ ($po->specifications ?? [])[$i] ?? '-' }}</td>
                                    <td class="border border-dark-600 px-2 py-1 text-center text-primary-400 font-bold">{{ ($po->quantity ?? [])[$i] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="border border-dark-600 px-2 py-2 text-center text-dark-400">قطعه‌ای ثبت نشده</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-auto pt-1">
                        <a href="{{ route('partorders.show', $po) }}" class="btn-ghost !py-1 !px-2.5 text-xs inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            مشاهده سفارش کامل
                        </a>
                    </div>
                </div>
                @endif

                {{-- تغییر وضعیت --}}
                <div class="card-luxury p-2.5 space-y-2 h-full flex flex-col">
                    <h3 class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        تغییر وضعیت
                    </h3>
                    <form action="{{ route('supply-proposals.changeStatus', $proposal) }}" method="POST" class="space-y-2 flex-1 flex flex-col">
                        @csrf

                        @if(auth()->user()->isCEO())
                        <div class="flex-1 flex flex-col">
                            <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">یادداشت مدیریت</label>
                            <textarea name="ceo_note" placeholder="نظر یا توضیحات مدیریت..."
                                class="input-luxury w-full !py-1 !px-2 text-xs resize-y flex-1 min-h-[3.5rem]">{{ old('ceo_note', $proposal->ceo_note) }}</textarea>
                        </div>
                        @endif

                        <div class="flex flex-wrap gap-1.5 mt-auto">
                            @foreach($statuses as $key => $label)
                            @php
                            $canChange = auth()->user()->isCEO() ||
                            (auth()->user()->isSupply() && in_array($key, \App\Models\SupplyProposal::SUPPLY_STATUSES));
                            $isActive = $proposal->status === $key;
                            @endphp
                            @if($canChange)
                            <button type="submit" name="status" value="{{ $key }}"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all duration-200 border
                                {{ $isActive
                                    ? 'bg-primary-500/25 text-primary-300 border-primary-500/50 cursor-default'
                                    : 'bg-dark-800/60 text-cream-300 border-dark-600 hover:border-primary-500/40 hover:text-primary-400 hover:bg-primary-500/10'
                                }}"
                                {{ $isActive ? 'disabled' : '' }}>
                                {{ $label }}
                                @if($isActive) <span class="mr-1">← فعلی</span> @endif
                            </button>
                            @endif
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>

            {{-- ═══ سایر پیشنهادها + نظر مدیریت — کنار هم، شرینکی ═══ --}}
            @if($relatedProposals->count() > 0 || $proposal->ceo_note || $proposal->selected_at)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 items-start">

                {{-- سایر پیشنهادها --}}
                @if($relatedProposals->count() > 0)
                <details class="card-luxury p-2 group">
                    <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                        <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            سایر پیشنهادها
                            <span class="badge badge-info !text-[10px] !py-0.5">{{ $relatedProposals->count() }}</span>
                        </span>
                        <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="pt-2 mt-2 border-t border-dark-700 space-y-1.5 max-h-48 overflow-y-auto">
                        @foreach($relatedProposals as $rel)
                        @php
                        $relConfig = match($rel->status) {
                        'approved' => ['badge-success', 'تایید مدیریت', '✓'],
                        'rejected' => ['badge-danger', 'رد شده', '✕'],
                        'ordered' => ['badge-info', 'سفارش داده شد', '📦'],
                        'delivered' => ['badge-success', 'تحویل شد', '✔✔'],
                        default => ['badge-warning', 'در انتظار بررسی', '⏱'],
                        };
                        @endphp
                        <div class="border border-dark-600/40 rounded-lg px-2 py-1.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1.5">
                            <div class="flex-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                                <span class="text-cream-100 font-medium">{{ $rel->part_name }}</span>
                                <span class="text-dark-400">·</span>
                                <span class="text-cream-300">{{ $rel->supplier_name }}</span>
                                <span class="badge {{ $relConfig[0] }} !text-[10px] !py-0.5">{{ $relConfig[2] }} {{ $relConfig[1] }}</span>
                                <span class="text-dark-400">قیمت: <span class="text-primary-400 font-bold">{{ number_format($rel->unit_price) }} ت</span></span>
                                <span class="text-dark-400">تعداد: <span class="text-cream-200">{{ $rel->quantity }}</span></span>
                                <span class="text-dark-400">جمع: <span class="text-primary-400 font-semibold">{{ number_format($rel->unit_price * $rel->quantity) }} ت</span></span>
                            </div>
                            <a href="{{ route('supply-proposals.show', $rel) }}" class="btn-ghost !py-0.5 !px-2 text-xs inline-flex items-center gap-1 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                مشاهده
                            </a>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif

                {{-- نظر مدیریت --}}
                @if($proposal->ceo_note || $proposal->selected_at)
                <details class="card-luxury p-2 group">
                    <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                        <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            نظر مدیریت
                            @if($proposal->selected_at)
                            <span class="text-[10px] text-dark-400 font-normal">· {{ $proposal->selected_at->diffForHumans() }}</span>
                            @endif
                        </span>
                        <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="pt-2 mt-2 border-t border-dark-700">
                        @if($proposal->ceo_note)
                        <p class="text-cream-100 text-xs leading-relaxed">{{ $proposal->ceo_note }}</p>
                        @else
                        <p class="text-dark-400 text-xs">یادداشتی ثبت نشده</p>
                        @endif
                    </div>
                </details>
                @endif
            </div>
            @endif

            {{-- پیوست‌ها --}}
            <details class="card-luxury p-2 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                        </svg>
                        پیوست‌ها
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-2 mt-2 border-t border-dark-700">
                    <x-attachments.panel :model="$proposal" mode="show" />
                </div>
            </details>
            <details class="card-luxury p-3 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        نظرات و مکالمات
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-3 mt-2 border-t border-dark-700">
                    <x-comments-section :reportable="$proposal" reportableType="App\Models\SupplyProposal" />
                </div>
            </details>
        </div>
    </div>
</x-app-layout>