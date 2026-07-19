@props([
'model',
'mode' => 'show', // show | edit | create
])

@php
$user = auth()->user();

$modelType = match(true) {
$model instanceof \App\Models\Report => 'report',
$model instanceof \App\Models\PartOrder => 'part_order',
$model instanceof \App\Models\WorkRequest => 'work_request',
$model instanceof \App\Models\PartOrderItem => 'part_order_item', // ← جدید
$model instanceof \App\Models\SupplyProposal => 'supply_proposal', // ← جدید
};

$canUpload = match($modelType) {
'report', 'part_order' => $user->isTechnician() && $model->user_id === $user->id,
'work_request' => $user->isReception() || $user->isCeo(),
'part_order_item' => $user->isTechnician() && $model->partOrder->user_id === $user->id, // ← جدید
'supply_proposal' => ($user->isSupply() && $model->created_by === $user->id) || $user->isCeo(), // ← جدید
default => false,
};

$uploadRoute = $canUpload && $mode !== 'create' ? match($modelType) {
'report' => route('reports.attachments.store', $model),
'part_order' => route('part-orders.attachments.store', $model),
'work_request' => route('work-requests.attachments.store', $model),
'part_order_item' => route('part-order-items.attachments.store', $model), // ← جدید
'supply_proposal' => route('supply-proposals.attachments.store', $model), // ← جدید
} : null;

$attachments = $mode !== 'create'
? $model->attachments()->with('uploader')->get()
: collect();

$uid = 'ap_' . $model->id;
@endphp

<div class="card-luxury overflow-hidden">

    <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-primary-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-stone-800">فایل‌های ضمیمه</h3>
        </div>

        @if($attachments->count() > 0)
        <span class="text-xs font-semibold px-3 py-1 bg-primary-50 text-primary-700 border border-primary-200 rounded-full">
            {{ $attachments->count() }} فایل
        </span>
        @endif
    </div>

    @if($mode === 'create')
    <x-attachments.placeholder />

    @else

    @if($canUpload && $mode === 'edit')
    <div class="p-4 pb-0">
        <form action="{{ $uploadRoute }}"
            method="POST"
            enctype="multipart/form-data"
            id="form-{{ $uid }}">
            @csrf

            <label for="input-{{ $uid }}"
                class="group flex flex-col items-center justify-center w-full border-2 border-dashed border-stone-200 rounded-2xl py-6 px-4 cursor-pointer transition-all duration-200 hover:border-primary-300 hover:bg-rose-50/40"
                id="label-{{ $uid }}">

                <div class="w-11 h-11 bg-primary-50 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-primary-100 transition-colors">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>

                <p class="text-sm font-semibold text-stone-700 mb-1">فایل‌ها را اینجا رها کنید</p>
                {{-- ← جدید: متن راهنما --}}
                <p class="text-xs text-stone-400 mb-4">JPG، PNG، WEBP، PDF، Word، Excel — حداکثر ۵۰ مگابایت — تا ۵ فایل</p>

                <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white pointer-events-none"
                    style="background:linear-gradient(135deg,#E8476A,#D03058);box-shadow:0 2px 10px rgba(232,71,106,.3)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    انتخاب فایل
                </span>

                {{-- ← جدید: accept گسترش‌یافته --}}
                <input type="file"
                    id="input-{{ $uid }}"
                    name="files[]"
                    multiple
                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                    class="hidden"
                    onchange="apPreview('{{ $uid }}')">
            </label>

            <div id="preview-{{ $uid }}" class="hidden mt-3 space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span id="count-{{ $uid }}" class="text-xs text-stone-500"></span>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white"
                        style="background:linear-gradient(135deg,#E8476A,#D03058)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        آپلود فایل‌ها
                    </button>
                </div>
                <div id="list-{{ $uid }}" class="space-y-1.5"></div>
            </div>
        </form>
    </div>
    @endif

    @if($mode === 'show' || !$canUpload)
    <div class="mx-4 mt-4 flex items-center gap-2 bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-xs text-stone-500">
        <svg class="w-4 h-4 flex-shrink-0 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        فایل‌ها فقط قابل مشاهده و دانلود هستند
    </div>
    @endif

    <div class="p-4">
        @if($attachments->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($attachments as $attachment)
            @php
            $canDeleteThis = $canUpload && $mode === 'edit' && (
            $modelType === 'supply_proposal' || $attachment->uploaded_by === auth()->id()
            );
            @endphp
            <x-attachments.item
                :attachment="$attachment"
                :model="$model"
                :modelType="$modelType"
                :canDelete="$canDeleteThis" />
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-8 text-stone-400">
            <svg class="w-10 h-10 mb-2 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <p class="text-sm text-stone-400">هیچ فایلی ضمیمه نشده است</p>
        </div>
        @endif
    </div>

    @endif
</div>

@if($mode !== 'create' && $attachments->where('file_type','image')->count() > 0)
<div id="lb-{{ $uid }}"
    class="fixed inset-0 z-[99] hidden items-center justify-center p-4"
    style="background:rgba(0,0,0,.85)"
    onclick="apCloseLb('{{ $uid }}')">
    <button onclick="apCloseLb('{{ $uid }}')"
        class="absolute top-5 right-5 w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
        style="background:rgba(255,255,255,.15)">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
    <img id="lb-img-{{ $uid }}" src="" alt=""
        class="max-w-full max-h-[88vh] rounded-2xl object-contain"
        onclick="event.stopPropagation()">
</div>
@endif

<script>
    function apPreview(uid) {
        const input = document.getElementById('input-' + uid);
        const preview = document.getElementById('preview-' + uid);
        const list = document.getElementById('list-' + uid);
        const count = document.getElementById('count-' + uid);
        const files = Array.from(input.files);
        if (!files.length) return;

        list.innerHTML = '';
        count.textContent = files.length + ' فایل انتخاب شده';
        preview.classList.remove('hidden');

        files.forEach(f => {
            const isPdf = f.type === 'application/pdf';
            const size = f.size >= 1048576 ?
                (f.size / 1048576).toFixed(1) + ' MB' :
                Math.round(f.size / 1024) + ' KB';
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 bg-stone-50 border border-stone-200 rounded-xl px-3 py-2';
            row.innerHTML = `
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:${isPdf ? '#EFF6FF' : '#FFF1F4'}">
                ${isPdf
                    ? `<svg class="w-4 h-4" style="color:#3B82F6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`
                    : `<svg class="w-4 h-4" style="color:#E8476A" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15l-5-5L5 21"/></svg>`
                }
            </div>
            <span class="text-xs font-medium flex-1 truncate" style="color:#374151">${f.name}</span>
            <span class="text-xs flex-shrink-0" style="color:#9CA3AF">${size}</span>`;
            list.appendChild(row);
        });
    }

    function apOpenLb(uid, src) {
        const lb = document.getElementById('lb-' + uid);
        if (!lb) return;
        document.getElementById('lb-img-' + uid).src = src;
        lb.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function apCloseLb(uid) {
        const lb = document.getElementById('lb-' + uid);
        if (!lb) return;
        lb.style.display = 'none';
        document.body.style.overflow = '';
    }
</script>