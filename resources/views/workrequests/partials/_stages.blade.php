@php
    $stageLabels = \App\Models\WorkRequestStage::$stageLabels;
    $statusConfig = [
        'pending'  => ['bg-yellow-500/20 border-yellow-500/30 text-yellow-300', '⏱ در انتظار'],
        'done'     => ['bg-green-500/20 border-green-500/30 text-green-300',  '✓ انجام شده'],
        'rejected' => ['bg-red-500/20 border-red-500/30 text-red-300',        '✕ رد شده'],
    ];
    $canEdit = auth()->user()->isReception() || auth()->user()->isCEO();
    $stages = $workrequest->stages ? $workrequest->stages->keyBy('stage') : collect();

    // فقط برای رنگ نشانگرهای نقطه‌ای بالای اسلایدر (نمایشی، روی لاجیک اثر ندارد)
    $dotColors = [
        'pending'  => 'bg-yellow-400',
        'done'     => 'bg-green-400',
        'rejected' => 'bg-red-400',
    ];
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

    {{-- نشانگرهای نقطه‌ای: رنگ = وضعیت هر مرحله، بزرگ‌شدگی = مرحله فعلی --}}
    <div class="wr-stage-dots flex items-center justify-center gap-2 flex-wrap">
        @foreach($stageLabels as $stageKey => $stageLabel)
            @php
                $stageRow = $stages[$stageKey] ?? null;
                $currentStatus = $stageRow?->status ?? 'pending';
            @endphp
            <button type="button"
                onclick="wrStageGoTo({{ $loop->index }})"
                class="wr-stage-dot w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $dotColors[$currentStatus] }} {{ $loop->first ? 'wr-dot-active' : '' }}"
                title="{{ $stageLabel }}">
            </button>
        @endforeach
    </div>

    {{-- اسلایدر: فقط یک مرحله در هر لحظه --}}
    <div class="wr-stage-slider relative overflow-hidden rounded-2xl border-2 border-dark-700/60 bg-dark-900/30" style="direction:ltr;">
        <div id="wrStageTrack" class="wr-stage-track flex transition-transform duration-400 ease-out" style="direction:ltr; will-change:transform;">
            @foreach($stageLabels as $stageKey => $stageLabel)
                @php
                    $stageRow = $stages[$stageKey] ?? null;
                    $currentStatus = $stageRow?->status ?? 'pending';
                    [$cardClass, $statusText] = $statusConfig[$currentStatus];
                @endphp
                <div class="wr-stage-slide p-6 md:p-10" style="direction:rtl; flex:0 0 100%; width:100%; max-width:100%;">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-xs text-dark-400">مرحله {{ $loop->index + 1 }} از {{ $loop->count }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $cardClass }}">{{ $statusText }}</span>
                    </div>

                    <h3 class="text-2xl font-bold text-cream-100 text-center mb-6">{{ $stageLabel }}</h3>

                    @if($stageRow?->note)
                        <p class="text-sm text-dark-200 bg-dark-900/50 rounded-xl px-4 py-3 mb-3 text-center max-w-md mx-auto">
                            {{ $stageRow->note }}
                        </p>
                    @endif
                    @if($stageRow?->actioned_at)
                        <p class="text-xs text-dark-400 text-center mb-4">
                            {{ $stageRow->actioned_at->diffForHumans() }}
                            @if($stageRow->actionedBy)
                                توسط {{ $stageRow->actionedBy->name }}
                            @endif
                        </p>
                    @endif

                    {{-- فرم ویرایش — فقط رسپشن و CEO (دقیقاً همان فرم و لاجیک قبلی) --}}
                    @if($canEdit)
                        <form action="{{ route('workrequests.stage.update', [$workrequest, $stageKey]) }}"
                              method="POST" class="space-y-2 pt-4 mt-4 border-t border-white/10 max-w-md mx-auto">
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

        {{-- دکمه‌های قبلی/بعدی --}}
        <button type="button" id="wrStagePrev" onclick="wrStageMove(-1)" title="مرحله قبلی"
            class="absolute top-1/2 -translate-y-1/2 right-2 w-10 h-10 rounded-full bg-dark-800/80 hover:bg-dark-700 border border-dark-600 text-cream-200 flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <button type="button" id="wrStageNext" onclick="wrStageMove(1)" title="مرحله بعدی"
            class="absolute top-1/2 -translate-y-1/2 left-2 w-10 h-10 rounded-full bg-dark-800/80 hover:bg-dark-700 border border-dark-600 text-cream-200 flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>
</div>

<style>
    .wr-stage-dot { opacity: .45; }
    .wr-dot-active { opacity: 1; transform: scale(1.35); box-shadow: 0 0 0 3px rgba(255,255,255,.08); }
</style>

<script>
(function() {
    const track = document.getElementById('wrStageTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.wr-stage-slide');
    const total = slides.length;
    let current = 0;

    function render() {
        track.style.transform = 'translateX(-' + (current * 100) + '%)';

        document.querySelectorAll('.wr-stage-dot').forEach(function(dot, i) {
            dot.classList.toggle('wr-dot-active', i === current);
        });

        const prevBtn = document.getElementById('wrStagePrev');
        const nextBtn = document.getElementById('wrStageNext');
        if (prevBtn) prevBtn.disabled = (current === 0);
        if (nextBtn) nextBtn.disabled = (current === total - 1);
    }

    window.wrStageMove = function(delta) {
        current = Math.min(Math.max(current + delta, 0), total - 1);
        render();
    };

    window.wrStageGoTo = function(index) {
        current = Math.min(Math.max(index, 0), total - 1);
        render();
    };

    render();
})();
</script>