<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-3">

            {{-- ═══ هدر فشرده ═══ --}}
            @php
            $statusConfig = match($report->status) {
            'approved' => ['badge-success', 'تایید شده', '✓'],
            'rejected' => ['badge-danger', 'رد شده', '✕'],
            'pending' => ['badge-warning', 'در انتظار', '⏱'],
            default => ['badge-info', 'جدید', '★']
            };
            @endphp
            <div class="card-luxury p-2.5 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('reports.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                        <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold text-cream-100 truncate">جزئیات گزارش — {{ $report->request_number }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="badge {{ $statusConfig[0] }} !text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                    <a href="{{ route('reports.pdf', $report) }}" target="_blank"
                        class="btn-secondary !py-1.5 !px-3 text-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </a>
                    @if(auth()->id() == $report->user_id && in_array($report->status, ['new', 'pending']))
                    <a href="{{ route('reports.edit', $report) }}" class="p-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('آیا از حذف این گزارش اطمینان دارید؟')">
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

            {{-- اطلاعات فنی --}}
            <div class="card-luxury p-3.5 space-y-3">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 text-xs">
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تکنسین</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->user->name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شرح کار درخواستی</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->part_name }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">مدل دستگاه</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->device_model }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره سریال دستگاه</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->serial_number }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ درخواست</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->request_date_jalali }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ پایان</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->end_date_jalali }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تعداد نیرو</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->workers_count }} نفر</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ساعت کار</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $report->hours_per_worker }} ساعت</div>
                    </div>
                </div>

                {{-- شرح ایراد و فعالیت --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-1 border-t border-dark-700/60">
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح ایراد اعلامی</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs min-h-[44px] whitespace-pre-wrap text-right resize-y overflow-auto">{{ $report->issue_description }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">گزارش فعالیت انجام‌شده</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs min-h-[44px] whitespace-pre-wrap text-right resize-y overflow-auto">{{ $report->activity_report }}</div>
                    </div>
                </div>

                {{-- قطعات مصرف‌شده --}}
                <div class="pt-1 border-t border-dark-700/60">
                    <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">قطعات مصرف‌شده</label>
                    @php
                    $parts = json_decode($report->used_parts_list) ?? [];
                    @endphp
                    @if(count($parts) > 0)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($parts as $part)
                        <span class="inline-flex items-center gap-1 border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 text-xs">
                            <svg class="w-3 h-3 text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ $part }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-dark-400">قطعه‌ای مصرف نشده است</p>
                    @endif
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
                    ['label' => 'پذیرش', 'status' => $report->request_approval, 'role' => 'reception'],
                    ['label' => 'تامین', 'status' => $report->supply_approval, 'role' => 'supply'],
                    ['label' => 'مدیر عامل', 'status' => $report->ceo_approval, 'role' => 'ceo'],
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

                {{-- تاریخچه تاییدها — جمع‌وبازشو --}}
                @if($report->approvals->count() > 0)
                <details class="pt-2 border-t border-dark-700/60 group">
                    <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden pt-1">
                        <h4 class="text-xs font-semibold text-dark-400">تاریخچه تاییدها ({{ $report->approvals->count() }})</h4>
                        <svg class="w-3.5 h-3.5 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="space-y-2 pt-2">
                        @foreach($report->approvals as $approval)
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
                    <form action="{{ route('reports.approve', $report) }}" method="POST" class="flex-1">
                        @csrf
                        <textarea name="comment" rows="2" placeholder="نظر شما (اختیاری)"
                            class="input-luxury w-full mb-2 !py-1.5 !px-2 text-xs resize-none"></textarea>
                        <button type="submit" class="btn-primary w-full !py-1.5 text-xs inline-flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            تایید گزارش
                        </button>
                    </form>

                    <form action="{{ route('reports.reject', $report) }}" method="POST" class="flex-1">
                        @csrf
                        <textarea name="comment" rows="2" placeholder="دلیل رد *" required
                            class="input-luxury w-full mb-2 !py-1.5 !px-2 text-xs resize-none"></textarea>
                        <button type="submit" class="w-full !py-1.5 rounded-lg font-semibold text-xs bg-red-500/25 text-red-300 border border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            رد گزارش
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- پیوست‌ها --}}
            <details class="card-luxury p-3 group">
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
                <div class="pt-3 mt-2 border-t border-dark-700">
                    <x-attachments.panel :model="$report" mode="show" />
                </div>
            </details>

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
                    <x-comments-section :reportable="$report" reportableType="App\Models\Report" />
                </div>
            </details>

            {{-- آخرین تغییر / PDF لینک اضافی می‌تونه اینجا هم بمونه --}}

        </div>
    </div>
</x-app-layout>