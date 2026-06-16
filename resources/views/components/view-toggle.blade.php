{{--
    View Toggle Component
    =====================
    Usage:
        <x-view-toggle :current="$viewMode" route="reports.index" />

    Props:
        current  — 'grid' | 'list'   (از کنترلر یا session بیاد)
        route    — نام route برای پارامتر view
        :params  — (اختیاری) array پارامترهای اضافه مثل search و status
--}}

@props([
    'current' => 'grid',
    'route'   => '',
    'params'  => [],
])

@php
    $base   = $params + array_filter(request()->only(['search', 'status', 'request_type']));
    $toGrid = array_merge($base, ['view' => 'grid']);
    $toList = array_merge($base, ['view' => 'list']);
@endphp

<div class="view-toggle-wrap inline-flex items-center gap-1 p-1 rounded-xl bg-dark-900/60 border border-dark-700 shadow-inner backdrop-blur-sm">

    {{-- Grid button --}}
    <a href="{{ route($route, $toGrid) }}"
       title="نمایش شبکه‌ای"
       class="view-toggle-btn {{ $current === 'grid' ? 'view-toggle-active' : 'view-toggle-idle' }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3"  y="3"  width="7" height="7" rx="1.5"/>
            <rect x="14" y="3"  width="7" height="7" rx="1.5"/>
            <rect x="3"  y="14" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>
        <span class="text-xs font-semibold hidden sm:inline">شبکه</span>
    </a>

    {{-- Divider --}}
    <span class="w-px h-5 bg-dark-700 mx-0.5 rounded-full"></span>

    {{-- List button --}}
    <a href="{{ route($route, $toList) }}"
       title="نمایش لیستی"
       class="view-toggle-btn {{ $current === 'list' ? 'view-toggle-active' : 'view-toggle-idle' }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="9"  y1="6"  x2="20" y2="6"  stroke-linecap="round"/>
            <line x1="9"  y1="12" x2="20" y2="12" stroke-linecap="round"/>
            <line x1="9"  y1="18" x2="20" y2="18" stroke-linecap="round"/>
            <circle cx="4.5" cy="6"  r="1.5" fill="currentColor" stroke="none"/>
            <circle cx="4.5" cy="12" r="1.5" fill="currentColor" stroke="none"/>
            <circle cx="4.5" cy="18" r="1.5" fill="currentColor" stroke="none"/>
        </svg>
        <span class="text-xs font-semibold hidden sm:inline">لیست</span>
    </a>
</div>

<style>
.view-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.75rem;
    border-radius: 0.6rem;
    transition: all 0.22s cubic-bezier(.4,0,.2,1);
    text-decoration: none;
}
.view-toggle-active {
    background: linear-gradient(135deg, var(--color-primary-600, #7c3aed), var(--color-primary-500, #8b5cf6));
    color: #fff;
    box-shadow: 0 2px 12px 0 rgb(124 58 237 / 0.35);
}
.view-toggle-idle {
    color: #6b7280;
}
.view-toggle-idle:hover {
    background: rgb(255 255 255 / 0.05);
    color: #d1d5db;
}
</style>