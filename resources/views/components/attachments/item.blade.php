@props([
    'attachment',
    'model',
    'modelType',
    'canDelete',
])

@php
    $uid = 'ap_' . $model->id;

    $deleteRoute = match($modelType) {
        'report'       => route('reports.attachments.destroy',        [$model, $attachment]),
        'part_order'   => route('part-orders.attachments.destroy',    [$model, $attachment]),
        'work_request' => route('work-requests.attachments.destroy',  [$model, $attachment]),
    };
@endphp

<div class="group relative bg-stone-50 border border-stone-200 rounded-2xl overflow-hidden transition-all duration-200 hover:border-primary-300 hover:shadow-md hover:-translate-y-0.5">

    @if($attachment->isImage())
        <button type="button"
            onclick="apOpenLb('{{ $uid }}', '{{ $attachment->url }}')"
            class="block w-full">
            <img src="{{ $attachment->url }}"
                 alt="{{ $attachment->file_name }}"
                 class="w-full h-24 object-cover">
        </button>
    @else
        <a href="{{ $attachment->url }}" target="_blank"
            class="flex items-center justify-center w-full h-24 bg-blue-50 hover:bg-blue-100 transition-colors">
            <svg class="w-10 h-10 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 3v6h6"/>
            </svg>
        </a>
    @endif

    <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-lg border
        {{ $attachment->isImage()
            ? 'bg-white/90 text-primary-600 border-primary-200'
            : 'bg-white/90 text-blue-600 border-blue-200' }}">
        {{ $attachment->isImage() ? 'عکس' : 'PDF' }}
    </span>

    @if($canDelete)
        <form action="{{ $deleteRoute }}" method="POST"
            class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity"
            onsubmit="return confirm('فایل حذف شود؟')">
            @csrf @method('DELETE')
            <button type="submit"
                class="w-7 h-7 bg-white/90 hover:bg-red-50 border border-stone-200 hover:border-red-300 rounded-lg flex items-center justify-center transition-all">
                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </form>
    @endif

    <div class="px-3 py-2">
        <p class="text-xs font-semibold text-stone-700 truncate" title="{{ $attachment->file_name }}">
            {{ $attachment->file_name }}
        </p>
        <div class="flex items-center justify-between mt-0.5">
            <span class="text-xs text-stone-400">{{ $attachment->file_size_human }}</span>
            <span class="text-xs text-stone-400">{{ $attachment->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>