{{--
    Bulk Action Bar Component
    =========================
    Usage:
        <x-bulk-action-bar
            :actions="[
                ['label' => 'حذف انتخاب‌شده‌ها',   'route' => 'reports.bulkDestroy',  'class' => 'danger',  'icon' => 'trash',  'method' => 'DELETE'],
                ['label' => 'تایید انتخاب‌شده‌ها',  'route' => 'reports.bulkApprove',  'class' => 'success', 'icon' => 'check'],
                ['label' => 'اکسپورت اکسل',         'route' => 'reports.exportExcel',  'class' => 'export',  'icon' => 'excel',  'type'   => 'export'],
            ]"
        />

    نوع 'export' → فرم با method POST بدون _method override.
    بقیه نوع‌ها → مثل قبل.
--}}

@props([
    'actions' => [],
])

{{-- فرم‌های مربوط به هر action --}}
@foreach($actions as $i => $action)
<form method="POST"
      action="{{ route($action['route']) }}"
      class="bulk-form hidden"
      id="bulk-form-{{ $i }}"
      @if(($action['type'] ?? '') !== 'export')
          onsubmit="return bulkInjectIds(this)"
      @else
          onsubmit="return bulkInjectIdsAsArray(this)"
      @endif
>
    @csrf
    @if(isset($action['method']) && strtoupper($action['method']) !== 'POST' && ($action['type'] ?? '') !== 'export')
        @method($action['method'])
    @endif

    @if(($action['type'] ?? '') === 'export')
        {{-- ids[] آرایه‌ای برای export — JS اینا رو پر می‌کنه --}}
        <div class="bulk-ids-array-container"></div>
    @else
        {{-- ids به صورت JSON string برای bulk actions قبلی --}}
        <input type="hidden" name="ids" class="bulk-ids-field" value="">
    @endif
</form>
@endforeach

<div id="bulk-action-bar"
     class="bulk-bar-root"
     style="display:none;"
     role="toolbar"
     aria-label="عملیات دسته‌جمعی">

    <div class="bulk-bar-inner">

        {{-- Count badge --}}
        <div class="flex items-center gap-3">
            <div class="bulk-count-badge">
                <span id="bulk-selected-count" class="font-bold text-white tabular-nums">0</span>
                <span class="text-primary-200 text-xs mr-1">مورد انتخاب شده</span>
            </div>

            <button type="button"
                    onclick="bulkDeselectAll()"
                    class="bulk-deselect-btn"
                    title="لغو انتخاب همه">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="h-6 w-px bg-dark-600 mx-1"></div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach($actions as $i => $action)
            @php
                $cls = match($action['class'] ?? 'default') {
                    'danger'  => 'bulk-btn-danger',
                    'success' => 'bulk-btn-success',
                    'warning' => 'bulk-btn-warning',
                    'export'  => 'bulk-btn-export',
                    default   => 'bulk-btn-default',
                };
                $icon = $action['icon'] ?? 'bolt';
            @endphp
            <button type="button"
                    class="bulk-btn {{ $cls }}"
                    @if(($action['type'] ?? '') !== 'export')
                        onclick="bulkSubmitForm('bulk-form-{{ $i }}')"
                    @else
                        onclick="bulkSubmitExport('bulk-form-{{ $i }}')"
                    @endif
            >
                @if($icon === 'trash')
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                @elseif($icon === 'check')
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                @elseif($icon === 'x')
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                @elseif($icon === 'excel')
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                @else
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                @endif
                {{ $action['label'] }}
            </button>
            @endforeach
        </div>

        {{-- Select all on page --}}
        <div class="mr-auto">
            <button type="button"
                    onclick="bulkSelectAll()"
                    class="bulk-select-all-btn">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                انتخاب همه صفحه
            </button>
        </div>

    </div>
</div>

<style>
.bulk-bar-root {
    position: fixed;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
    width: calc(100% - 2rem);
    max-width: 56rem;
    animation: bulkSlideUp .25s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes bulkSlideUp {
    from { opacity: 0; transform: translateX(-50%) translateY(1.5rem) scale(.97); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0)        scale(1);  }
}
.bulk-bar-inner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding: 0.75rem 1.25rem;
    border-radius: 1rem;
    background: rgba(15,15,20,.88);
    backdrop-filter: blur(18px) saturate(1.6);
    border: 1px solid rgba(139,92,246,.25);
    box-shadow:
        0 8px 32px rgba(0,0,0,.55),
        0 0 0 1px rgba(139,92,246,.1),
        inset 0 1px 0 rgba(255,255,255,.04);
}
.bulk-count-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    box-shadow: 0 0 12px rgba(124,58,237,.4);
    font-size: .8rem;
    white-space: nowrap;
}
.bulk-deselect-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.6rem;
    height: 1.6rem;
    border-radius: 50%;
    color: #9ca3af;
    background: transparent;
    border: 1px solid #374151;
    cursor: pointer;
    transition: all .18s;
}
.bulk-deselect-btn:hover {
    color: #ef4444;
    border-color: rgba(239,68,68,.4);
    background: rgba(239,68,68,.1);
}
.bulk-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .85rem;
    border-radius: .6rem;
    font-size: .78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .18s;
    border: 1px solid transparent;
    white-space: nowrap;
}
.bulk-btn-danger  { background: rgba(239,68,68,.15);   color: #f87171; border-color: rgba(239,68,68,.25); }
.bulk-btn-success { background: rgba(34,197,94,.15);   color: #4ade80; border-color: rgba(34,197,94,.25); }
.bulk-btn-warning { background: rgba(234,179,8,.15);   color: #facc15; border-color: rgba(234,179,8,.25); }
.bulk-btn-export  { background: rgba(16,185,129,.15);  color: #34d399; border-color: rgba(16,185,129,.25); }
.bulk-btn-default { background: rgba(139,92,246,.15);  color: #a78bfa; border-color: rgba(139,92,246,.25); }
.bulk-btn-danger:hover  { background: rgba(239,68,68,.28);  box-shadow: 0 0 10px rgba(239,68,68,.2); }
.bulk-btn-success:hover { background: rgba(34,197,94,.28);  box-shadow: 0 0 10px rgba(34,197,94,.2); }
.bulk-btn-warning:hover { background: rgba(234,179,8,.28);  box-shadow: 0 0 10px rgba(234,179,8,.2); }
.bulk-btn-export:hover  { background: rgba(16,185,129,.28); box-shadow: 0 0 10px rgba(16,185,129,.25); }
.bulk-btn-default:hover { background: rgba(139,92,246,.28); box-shadow: 0 0 10px rgba(139,92,246,.2); }
.bulk-select-all-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .75rem;
    border-radius: .6rem;
    font-size: .75rem;
    font-weight: 600;
    color: #9ca3af;
    background: rgba(255,255,255,.04);
    border: 1px solid #374151;
    cursor: pointer;
    transition: all .18s;
    white-space: nowrap;
}
.bulk-select-all-btn:hover {
    color: #d1d5db;
    background: rgba(255,255,255,.08);
    border-color: #4b5563;
}
</style>

<script>
window.__bulkSelectedIds = new Set();

function bulkUpdateBar() {
    const bar   = document.getElementById('bulk-action-bar');
    const count = document.getElementById('bulk-selected-count');
    const n     = window.__bulkSelectedIds.size;
    count.textContent = n;
    bar.style.display = n > 0 ? 'block' : 'none';
}

function bulkToggle(id, cb) {
    if (cb.checked) {
        window.__bulkSelectedIds.add(String(id));
    } else {
        window.__bulkSelectedIds.delete(String(id));
    }
    const row = cb.closest('[data-bulk-item]');
    if (row) row.classList.toggle('bulk-item-selected', cb.checked);
    bulkUpdateBar();
}

function bulkSelectAll() {
    document.querySelectorAll('.bulk-checkbox').forEach(cb => {
        cb.checked = true;
        window.__bulkSelectedIds.add(String(cb.dataset.id));
        const row = cb.closest('[data-bulk-item]');
        if (row) row.classList.add('bulk-item-selected');
    });
    bulkUpdateBar();
}

function bulkDeselectAll() {
    document.querySelectorAll('.bulk-checkbox').forEach(cb => {
        cb.checked = false;
        const row = cb.closest('[data-bulk-item]');
        if (row) row.classList.remove('bulk-item-selected');
    });
    window.__bulkSelectedIds.clear();
    bulkUpdateBar();
}

// ── برای bulk actions معمولی (حذف، تایید ...) ── ids به صورت JSON
function bulkSubmitForm(formId) {
    if (window.__bulkSelectedIds.size === 0) {
        alert('لطفاً حداقل یک مورد انتخاب کنید.');
        return;
    }
    const form  = document.getElementById(formId);
    const field = form.querySelector('.bulk-ids-field');
    field.value = JSON.stringify([...window.__bulkSelectedIds]);
    form.submit();
}

// ── برای export ── ids[] به صورت آرایه جداگانه (سازگار با Laravel validation)
function bulkSubmitExport(formId) {
    if (window.__bulkSelectedIds.size === 0) {
        alert('لطفاً حداقل یک مورد انتخاب کنید.');
        return;
    }
    const form      = document.getElementById(formId);
    const container = form.querySelector('.bulk-ids-array-container');

    // پاک کردن input های قبلی
    container.innerHTML = '';

    // هر id یه input مجزا
    window.__bulkSelectedIds.forEach(id => {
        const input   = document.createElement('input');
        input.type    = 'hidden';
        input.name    = 'ids[]';
        input.value   = id;
        container.appendChild(input);
    });

    form.submit();
}

// سازگاری با bulkInjectIds قدیمی (اگه جایی استفاده شده)
function bulkInjectIds(form) {
    if (window.__bulkSelectedIds.size === 0) {
        alert('لطفاً حداقل یک مورد انتخاب کنید.');
        return false;
    }
    const field = form.querySelector('.bulk-ids-field');
    if (field) field.value = JSON.stringify([...window.__bulkSelectedIds]);
    return true;
}
</script>