@php
    $stageLabels = \App\Models\WorkRequestStage::$stageLabels;
    $canEdit = auth()->user()->isReception() || auth()->user()->isCEO();
    $stages = $workrequest->stages ? $workrequest->stages->keyBy('stage') : collect();
    $doneCount = $stages->where('status', 'done')->count();
    $totalCount = count($stageLabels);
@endphp

<div class="space-y-3">

    {{-- نوار پیشرفت --}}
    <div class="flex items-center justify-between text-xs text-dark-400 mb-1">
        <span>پیشرفت مراحل</span>
        <span class="font-semibold text-cream-200">{{ $doneCount }} از {{ $totalCount }}</span>
    </div>
    <div class="w-full h-1.5 rounded-full bg-dark-700/60 overflow-hidden">
        <div class="h-full bg-gradient-to-l from-amber-500 to-amber-400 rounded-full transition-all duration-500"
             style="width: {{ $totalCount ? ($doneCount / $totalCount * 100) : 0 }}%"></div>
    </div>

    {{-- گرید فشرده مراحل --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
        @foreach($stageLabels as $stageKey => $stageLabel)
            @php
                $stageRow = $stages[$stageKey] ?? null;
                $isDone = ($stageRow?->status ?? 'pending') === 'done';
                $toggleTo = $isDone ? 'pending' : 'done';
            @endphp

            @if($canEdit)
                <form action="{{ route('workrequests.stage.update', [$workrequest, $stageKey]) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $toggleTo }}">
                    <button type="submit"
                        class="w-full rounded-xl border-2 p-2.5 flex flex-col items-center gap-1 text-center transition-all duration-200 cursor-pointer group
                        {{ $isDone
                            ? 'bg-gradient-to-b from-amber-400/90 to-amber-500/80 border-amber-400/60 shadow-[0_0_12px_-2px_rgba(251,191,36,0.35)]'
                            : 'bg-dark-800/40 border-dark-600/50 hover:border-amber-400/40 hover:bg-amber-500/5' }}">

                        @if($isDone)
                            <svg class="w-4 h-4 text-dark-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-[11px] font-bold text-dark-900 leading-tight">{{ $stageLabel }}</span>
                        @else
                            <svg class="w-4 h-4 text-dark-500 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                            <span class="text-[11px] font-semibold text-dark-300 group-hover:text-cream-100 leading-tight transition-colors">{{ $stageLabel }}</span>
                        @endif
                    </button>
                </form>
            @else
                {{-- نمایشی برای نقش‌های بدون دسترسی ویرایش --}}
                <div class="rounded-xl border-2 p-2.5 flex flex-col items-center gap-1 text-center
                    {{ $isDone ? 'bg-gradient-to-b from-amber-400/90 to-amber-500/80 border-amber-400/60' : 'bg-dark-800/30 border-dark-600/40' }}">
                    <svg class="w-4 h-4 {{ $isDone ? 'text-dark-900' : 'text-dark-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ $isDone ? 3 : 2 }}">
                        @if($isDone)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @else
                            <circle cx="12" cy="12" r="9" />
                        @endif
                    </svg>
                    <span class="text-[11px] font-bold leading-tight {{ $isDone ? 'text-dark-900' : 'text-dark-400 font-semibold' }}">{{ $stageLabel }}</span>
                </div>
            @endif
        @endforeach
    </div>
</div>