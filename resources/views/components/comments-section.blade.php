@php
    $user = auth()->user();

    $comments = $reportable->comments()
        ->where('status', 'active')
        ->whereNull('parent_id')
        ->forUser($user, $reportableType)
        ->with(['user', 'replies' => function($q) {
            $q->where('status', 'active')->with('user')->orderBy('created_at', 'asc');
        }])
        ->latest()
        ->get();

    $roleLabelMap = [
        'technician' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', '🔧 تعمیرکار'],
        'reception'  => ['bg-purple-500/20 text-purple-400 border-purple-500/30', '📋 پذیرش'],
        'supply'     => ['bg-orange-500/20 text-orange-400 border-orange-500/30', '📦 تامین'],
        'ceo'        => ['bg-yellow-500/20 text-yellow-400 border-yellow-500/30', '👑 مدیرعامل'],
    ];
@endphp

<div class="space-y-4">

    {{-- فرم ارسال کامنت جدید --}}
    <form action="{{ route('comments.store') }}" method="POST" class="space-y-2">
        @csrf
        <input type="hidden" name="reportable_type" value="{{ $reportableType }}">
        <input type="hidden" name="reportable_id" value="{{ $reportable->id }}">
        <input type="hidden" name="parent_id" value="">
        <textarea
            name="comment"
            rows="2"
            placeholder="نظر خود را بنویسید..."
            class="input-luxury w-full resize-none !py-1.5 !px-2 text-xs"
            required></textarea>
        <div class="flex justify-end">
            <button type="submit" class="btn-primary !py-1.5 !px-3 text-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                ارسال نظر
            </button>
        </div>
    </form>

    {{-- لیست کامنت‌ها --}}
    @if($comments->isEmpty())
    <div class="text-center py-6">
        <svg class="w-9 h-9 text-dark-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <p class="text-xs text-dark-400">هنوز نظری ثبت نشده است</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($comments as $comment)
        <div class="space-y-2">
            {{-- کامنت اصلی --}}
            <div class="section-inner p-3">
                <div class="flex items-start gap-2.5">
                    {{-- Avatar --}}
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-xs shadow-md flex-shrink-0">
                        {{ mb_substr($comment->user->name, 0, 1) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1.5">
                            <span class="font-semibold text-cream-100 text-xs">{{ $comment->user->name }}</span>
                            @php $rl = $roleLabelMap[$comment->role] ?? ['bg-dark-700 text-dark-400 border-dark-600', $comment->role]; @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rl[0] }}">
                                {{ $rl[1] }}
                            </span>
                            <span class="text-[10px] text-dark-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>

                        <p class="text-cream-100 text-sm leading-relaxed">{{ $comment->comment }}</p>

                        <div class="flex items-center gap-3 mt-2.5">
                            <button
                                type="button"
                                onclick="toggleReplyForm('reply-form-{{ $comment->id }}')"
                                class="text-xs text-primary-400 hover:text-primary-300 transition-colors flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                پاسخ
                            </button>

                            @if($comment->user_id === $user->id || $user->isCEO())
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    حذف
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- فرم ریپلای --}}
                <div id="reply-form-{{ $comment->id }}" class="hidden mt-3 mr-10 pr-3 border-r-2 border-primary-500/30">
                    <form action="{{ route('comments.store') }}" method="POST" class="space-y-1.5">
                        @csrf
                        <input type="hidden" name="reportable_type" value="{{ $reportableType }}">
                        <input type="hidden" name="reportable_id" value="{{ $reportable->id }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <div class="flex items-center gap-1.5 mb-1.5 text-[11px] text-primary-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            پاسخ به {{ $comment->user->name }}
                        </div>
                        <textarea
                            name="comment"
                            rows="2"
                            placeholder="پاسخ خود را بنویسید..."
                            class="input-luxury w-full resize-none !py-1 !px-2 text-xs"
                            required></textarea>
                        <div class="flex gap-2 justify-end">
                            <button
                                type="button"
                                onclick="toggleReplyForm('reply-form-{{ $comment->id }}')"
                                class="px-2.5 py-1 text-[11px] rounded-lg bg-dark-700/70 border border-dark-600 text-cream-200 hover:bg-dark-600/70 hover:text-cream-100 transition-colors">
                                انصراف
                            </button>
                            <button type="submit" class="px-2.5 py-1 text-[11px] rounded-lg bg-primary-500/20 text-primary-300 border border-primary-500/40 hover:bg-primary-500/30 transition-colors">
                                ارسال پاسخ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ریپلای‌ها --}}
            @if($comment->replies->isNotEmpty())
            <div class="mr-5 space-y-2 border-r-2 border-dark-600 pr-3">
                @foreach($comment->replies as $reply)
                <div class="section-inner p-2.5">
                    <div class="flex items-start gap-2.5">
                        <div class="w-7 h-7 bg-gradient-to-br from-dark-600 to-dark-700 rounded-lg flex items-center justify-center text-cream-50 font-bold text-[11px] shadow-md flex-shrink-0">
                            {{ mb_substr($reply->user->name, 0, 1) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                <span class="font-semibold text-cream-100 text-[11px]">{{ $reply->user->name }}</span>
                                @php $rrl = $roleLabelMap[$reply->role] ?? ['bg-dark-700 text-dark-400 border-dark-600', $reply->role]; @endphp
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold border {{ $rrl[0] }}">
                                    {{ $rrl[1] }}
                                </span>
                                <span class="text-[10px] text-dark-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    در پاسخ به {{ $comment->user->name }}
                                </span>
                                <span class="text-[10px] text-dark-400">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="text-cream-200 text-[11px] leading-relaxed">{{ $reply->comment }}</p>

                            @if($reply->user_id === $user->id || $user->isCEO())
                            <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="mt-1.5" onsubmit="return confirm('حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[10px] text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    حذف
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById(id);
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.querySelector('textarea')?.focus();
        }
    }
}
</script>