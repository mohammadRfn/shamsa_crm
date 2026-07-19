<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-3">

            {{-- ═══ هدر فشرده ═══ --}}
            @php
            $statusConfig = match($partorder->status) {
            'approved' => ['badge-success', 'تایید شده', '✓'],
            'failed' => ['badge-danger', 'رد شده', '✕'],
            'pending' => ['badge-warning', 'در انتظار', '⏱'],
            'sent' => ['badge-info', 'ارسال شده', '📦'],
            default => ['badge-info', 'جدید', '★']
            };
            @endphp
            <div class="card-luxury p-2.5 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('partorders.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                        <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold text-cream-100 truncate">جزئیات سفارش قطعه — {{ $partorder->order_number }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="badge {{ $statusConfig[0] }} !text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                    <a href="{{ route('partorders.pdf', $partorder) }}" target="_blank"
                        class="btn-secondary !py-1.5 !px-3 text-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </a>
                    @if(auth()->id() == $partorder->user_id && in_array($partorder->status, ['new', 'pending']))
                    <a href="{{ route('partorders.edit', $partorder) }}" class="p-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="{{ route('partorders.destroy', $partorder) }}" method="POST" onsubmit="return confirm('آیا از حذف این سفارش اطمینان دارید؟')">
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

            {{-- مشخصات سفارش --}}
            <div class="card-luxury p-3.5 space-y-3">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">سفارش‌دهنده</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $partorder->user->name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">نام تجهیز</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $partorder->equipment_name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ سفارش</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $partorder->order_date_jalali }}</div>
                    </div>
                </div>

                {{-- جدول قطعات — شکل و رنگ حفظ شده، فقط فشرده‌تر --}}
                @php
                $itemsList = $partorder->items->values();
                @endphp
                <div class="overflow-x-auto pt-1">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-dark-700/50">
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-center w-8">ردیف</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">نام قطعه</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">مشخصات</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">پکیج</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-center w-16">تعداد</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">توضیحات</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-center w-10">فایل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partorder->part_name ?? [] as $i => $pname)
                            @php $rowItem = $itemsList[$i] ?? null; @endphp
                            <tr>
                                <td class="border border-dark-600 px-1.5 py-1.5 text-center text-cream-400 text-xs">{{ $i + 1 }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-100 text-xs">{{ $pname }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-100 text-xs">{{ ($partorder->specifications ?? [])[$i] ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-100 text-xs">{{ ($partorder->package ?? [])[$i] ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center text-primary-400 font-bold text-xs">{{ ($partorder->quantity ?? [])[$i] ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-100 text-xs">{{ ($partorder->description ?? [])[$i] ?? '-' }}</td>
                                <td class="border border-dark-600 px-1.5 py-1.5 text-center">
                                    @if($rowItem)
                                    <button type="button"
                                        onclick="toggleAttachRow({{ $rowItem->id }})"
                                        class="relative inline-flex items-center justify-center w-7 h-7 rounded-lg hover:bg-dark-700/70 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        @if($rowItem->attachments->count() > 0)
                                        <span class="absolute -top-1 -left-1 w-3.5 h-3.5 bg-primary-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                                            {{ $rowItem->attachments->count() }}
                                        </span>
                                        @endif
                                    </button>
                                    @else
                                    <span class="text-[11px] text-dark-500">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if($rowItem)
                            <tr id="attach-row-{{ $rowItem->id }}" class="attach-row hidden">
                                <td colspan="7" class="border border-dark-600 p-2.5 bg-dark-800/40">
                                    <x-attachments.panel :model="$rowItem" mode="show" />
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="7" class="border border-dark-600 px-3 py-3 text-center text-dark-400 text-xs">قطعه‌ای ثبت نشده</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- وضعیت تاییدها --}}
            <div class="card-luxury p-3.5 space-y-3">
                <h3 class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    وضعیت تاییدها
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
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
                    <div class="p-2.5 rounded-lg border text-center {{ $config[0] }} transition-all duration-300">
                        <div class="text-xs font-bold {{ $config[1] }} mb-0.5">
                            {{ $approval['label'] }}
                        </div>
                        <div class="text-[11px] {{ $config[1] }}">
                            {{ $config[2] }}
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($partorder->approvals->count() > 0)
                <details class="pt-2 border-t border-dark-700/60 group">
                    <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden pt-1">
                        <h4 class="text-xs font-semibold text-dark-400">تاریخچه تاییدها ({{ $partorder->approvals->count() }})</h4>
                        <svg class="w-3.5 h-3.5 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="space-y-2 pt-2">
                        @foreach($partorder->approvals as $approval)
                        <div class="border border-dark-600/40 rounded-lg px-2.5 py-1.5 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-[10px] shrink-0">
                                    {{ mb_substr($approval->user->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-cream-100 font-medium text-xs truncate">{{ $approval->user->name }}</p>
                                    <p class="text-[10px] text-dark-400">{{ $approval->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="badge {{ $approval->isApproved() ? 'badge-success' : 'badge-danger' }} !text-[10px]">
                                    {{ $approval->isApproved() ? 'تایید' : 'رد' }}
                                </span>
                                @if($approval->comment)
                                <p class="text-[10px] text-dark-400 mt-0.5 max-w-[10rem] truncate">{{ $approval->comment }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endif
            </div>

            {{-- دکمه‌های تایید/رد --}}
            @if(auth()->user()->isApprover())
            <div class="card-luxury p-3.5">
                <h3 class="text-sm font-bold text-cream-100 mb-2.5">اقدام شما:</h3>
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <form action="{{ route('partorders.approve', $partorder) }}" method="POST" class="flex-1">
                        @csrf
                        <textarea name="comment" rows="2" placeholder="نظر شما (اختیاری)"
                            class="input-luxury w-full mb-2 !py-1.5 !px-2 text-xs resize-none"></textarea>
                        <button type="submit" class="btn-primary w-full !py-1.5 text-xs inline-flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            تایید سفارش
                        </button>
                    </form>

                    <form action="{{ route('partorders.reject', $partorder) }}" method="POST" class="flex-1">
                        @csrf
                        <textarea name="comment" rows="2" placeholder="دلیل رد *" required
                            class="input-luxury w-full mb-2 !py-1.5 !px-2 text-xs resize-none"></textarea>
                        <button type="submit" class="w-full !py-1.5 rounded-lg font-semibold text-xs bg-red-500/25 text-red-300 border border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            رد سفارش
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- آخرین تغییر --}}
            @if($partorder->last_action_at)
            <div class="text-[11px] text-dark-400 flex items-center gap-1.5 px-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>آخرین تغییر توسط <span class="text-cream-200 font-medium">{{ $partorder->lastActionBy->name ?? 'سیستم' }}</span> — {{ $partorder->last_action_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif

            {{-- نظرات و مکالمات --}}
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
                    <x-comments-section :reportable="$partorder" reportableType="App\Models\PartOrder" />
                </div>
            </details>

        </div>
    </div>

    <script>
        function toggleAttachRow(id) {
            const row = document.getElementById('attach-row-' + id);
            if (row) row.classList.toggle('hidden');
        }
    </script>
</x-app-layout>