@php
    $stageLabels = \App\Models\WorkRequestStage::$stageLabels;
    $statusConfig = [
        'pending'  => ['bg-yellow-500/20 border-yellow-500/30 text-yellow-300', '⏱ در انتظار'],
        'done'     => ['bg-green-500/20 border-green-500/30 text-green-300',  '✓ انجام شده'],
        'rejected' => ['bg-red-500/20 border-red-500/30 text-red-300',        '✕ رد شده'],
    ];
    $canEdit = auth()->user()->isReception() || auth()->user()->isCEO();
    $stages = $workrequest->stages ? $workrequest->stages->keyBy('stage') : collect();
@endphp

<div class="card-luxury p-6 space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-cream-100">گزارش مراحل کار</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($stageLabels as $stageKey => $stageLabel)
            @php
                $stageRow = $stages[$stageKey] ?? null;
                $currentStatus = $stageRow?->status ?? 'pending';
                [$cardClass, $statusText] = $statusConfig[$currentStatus];
            @endphp

            <div class="p-4 rounded-xl border-2 {{ $cardClass }} space-y-3 transition-all duration-300">
                {{-- عنوان مرحله + وضعیت --}}
                <div class="flex items-center justify-between">
                    <span class="font-bold text-cream-100">{{ $stageLabel }}</span>
                    <span class="text-sm font-semibold">{{ $statusText }}</span>
                </div>

                {{-- یادداشت و تاریخ (اگه موجود) --}}
                @if($stageRow?->note)
                    <p class="text-xs text-dark-300 bg-dark-900/40 rounded-lg px-3 py-2">
                        {{ $stageRow->note }}
                    </p>
                @endif
                @if($stageRow?->actioned_at)
                    <p class="text-xs text-dark-400">
                        {{ $stageRow->actioned_at->diffForHumans() }}
                        @if($stageRow->actionedBy)
                            توسط {{ $stageRow->actionedBy->name }}
                        @endif
                    </p>
                @endif

                {{-- فرم ویرایش — فقط رسپشن و CEO --}}
                @if($canEdit)
                    <form action="{{ route('workrequests.stage.update', [$workrequest, $stageKey]) }}"
                          method="POST" class="space-y-2 pt-2 border-t border-white/10">
                        @csrf @method('PATCH')

                        <select name="status" class="input-luxury w-full text-sm">
                            <option value="pending"  {{ $currentStatus === 'pending'  ? 'selected' : '' }}>⏱ در انتظار</option>
                            <option value="done"     {{ $currentStatus === 'done'     ? 'selected' : '' }}>✓ انجام شده</option>
                            <option value="rejected" {{ $currentStatus === 'rejected' ? 'selected' : '' }}>✕ رد شده</option>
                        </select>

                        <input type="text" name="note"
                               value="{{ old('note', $stageRow?->note) }}"
                               placeholder="یادداشت (اختیاری)"
                               class="input-luxury w-full text-sm">

                        <button type="submit" class="btn-primary w-full text-sm py-2">
                            ثبت
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>