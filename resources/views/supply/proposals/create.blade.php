<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('supply-proposals.index') }}"
                    class="p-2 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                    <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        {{ isset($proposal) ? 'ویرایش پیشنهاد' : 'ثبت پیشنهاد جدید' }}
                    </h1>
                    <p class="text-dark-400 mt-1">{{ isset($proposal) ? 'بروزرسانی اطلاعات پیشنهاد تامین' : 'ارائه پیشنهاد قیمت برای قطعات درخواستی' }}</p>
                </div>
            </div>

            @if($errors->any())
            <div class="card-luxury p-4 border-red-500/40 bg-red-500/10">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-sm text-red-400 flex items-center gap-2">
                        <span>✕</span> {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST"
                action="{{ isset($proposal) ? route('supply-proposals.update', $proposal) : route('supply-proposals.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($proposal)) @method('PUT') @endif

                {{-- انتخاب سفارش قطعه --}}
                <div class="card-luxury p-6 space-y-6 mb-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">انتخاب سفارش قطعه</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-cream-200">نام تجهیز <span class="text-primary-400">*</span></label>
                            <select name="part_order_id" id="part_order_id" class="input-luxury w-full" required>
                                <option value="">انتخاب...</option>
                                @foreach($partOrders as $po)
                                <option value="{{ $po->id }}"
                                    data-parts="{{ json_encode($po->part_name ?? []) }}"
                                    {{ old('part_order_id', $proposal->part_order_id ?? $selectedPartOrder?->id) == $po->id ? 'selected' : '' }}>
                                    {{ $po->order_number }} — {{ $po->equipment_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-cream-200">نام قطعه <span class="text-primary-400">*</span></label>
                            <select name="part_name" id="part_name" class="input-luxury w-full" required>
                                <option value="">ابتدا سفارش را انتخاب کنید...</option>
                                @if(isset($proposal))
                                <option value="{{ $proposal->part_name }}" selected>{{ $proposal->part_name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- اطلاعات پیشنهاد --}}
                <div class="card-luxury p-6 space-y-6 mb-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">اطلاعات پیشنهاد</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-cream-200">نام فروشنده / تامین‌کننده <span class="text-primary-400">*</span></label>
                            <input type="text" name="supplier_name"
                                value="{{ old('supplier_name', $proposal->supplier_name ?? '') }}"
                                placeholder="مثلاً: شرکت الکترونیک پارس"
                                class="input-luxury w-full" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-cream-200">قیمت واحد (تومان) <span class="text-primary-400">*</span></label>
                            <input type="number" name="unit_price"
                                value="{{ old('unit_price', $proposal->unit_price ?? '') }}"
                                placeholder="0"
                                min="0" step="1000"
                                class="input-luxury w-full" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-cream-200">تعداد <span class="text-primary-400">*</span></label>
                            <input type="number" name="quantity"
                                value="{{ old('quantity', $proposal->quantity ?? 1) }}"
                                min="1"
                                class="input-luxury w-full" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-cream-200">تاریخ تحویل تخمینی</label>
                            <input type="text" name="estimated_delivery" id="estimated_delivery"
                                value="{{ old('estimated_delivery', $proposal->estimated_delivery_jalali ?? '') }}"
                                placeholder="انتخاب تاریخ..."
                                class="input-luxury w-full jalali-datepicker" autocomplete="off">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-cream-200">یادداشت / توضیحات</label>
                        <textarea name="note" rows="3"
                            placeholder="توضیحات تکمیلی، شرایط پرداخت، ضمانت و..."
                            class="input-luxury w-full resize-none">{{ old('note', $proposal->note ?? '') }}</textarea>
                    </div>
                </div>

                {{-- پیش‌نمایش جمع کل --}}
                <div class="card-luxury p-4 mb-6 flex items-center justify-between" id="price-preview" style="display:none!important">
                    <span class="text-sm text-dark-400">پیش‌نمایش جمع کل:</span>
                    <span class="text-primary-400 font-bold text-lg" id="total-price">---</span>
                </div>
                <div class="card-luxury p-6 space-y-4 mb-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">فایل‌های ضمیمه (اختیاری)</h2>
                    </div>

                    <div id="file_inputs" class="space-y-2">
                        <div class="flex items-center gap-3">
                            <label for="attachment_0" title="افزودن فایل"
                                class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-dark-700/70 transition-colors cursor-pointer flex-shrink-0">
                                <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </label>
                            <input type="file" id="attachment_0" name="attachments[]"
                                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                class="hidden" onchange="document.getElementById('attachment_name_0').textContent = this.files[0] ? this.files[0].name : 'فایلی انتخاب نشده';">
                            <span id="attachment_name_0" class="text-xs text-dark-400">فایلی انتخاب نشده</span>
                        </div>
                    </div>

                    <button type="button" onclick="addFileInput()" class="btn-secondary text-sm inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن فایل دیگر
                    </button>
                    <p class="text-xs text-dark-400">JPG، PNG، WEBP، PDF، Word، Excel — حداکثر ۵۰ مگابایت — تا ۵ فایل</p>
                </div>

                {{-- دکمه‌ها --}}
                <div class="flex gap-4 justify-end">
                    <a href="{{ route('supply-proposals.index') }}" class="btn-secondary">انصراف</a>
                    <button type="submit" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ isset($proposal) ? 'بروزرسانی پیشنهاد' : 'ثبت پیشنهاد' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ── dropdown داینامیک قطعات ──────────────────────────────────────
        const partOrderSelect = document.getElementById('part_order_id');
        const partNameSelect = document.getElementById('part_name');
        const oldPartName = "{{ old('part_name', $proposal->part_name ?? '') }}";

        function loadParts(selectedId) {
            const option = partOrderSelect.querySelector(`option[value="${selectedId}"]`);
            partNameSelect.innerHTML = '<option value="">انتخاب قطعه...</option>';
            if (!option || !selectedId) return;

            let parts = [];
            try {
                parts = JSON.parse(option.dataset.parts || '[]');
            } catch (e) {}

            parts.forEach(part => {
                const opt = document.createElement('option');
                opt.value = part;
                opt.textContent = part;
                if (part === oldPartName) opt.selected = true;
                partNameSelect.appendChild(opt);
            });
        }

        partOrderSelect.addEventListener('change', function() {
            loadParts(this.value);
        });

        // لود اولیه (برای ویرایش)
        if (partOrderSelect.value) loadParts(partOrderSelect.value);

        // ── پیش‌نمایش قیمت کل ───────────────────────────────────────────
        const unitPriceInput = document.querySelector('[name="unit_price"]');
        const quantityInput = document.querySelector('[name="quantity"]');
        const pricePreview = document.getElementById('price-preview');
        const totalPriceEl = document.getElementById('total-price');

        function updateTotal() {
            const price = parseFloat(unitPriceInput.value) || 0;
            const qty = parseInt(quantityInput.value) || 0;
            if (price > 0 && qty > 0) {
                pricePreview.style.removeProperty('display');
                totalPriceEl.textContent = (price * qty).toLocaleString('fa-IR') + ' تومان';
            } else {
                pricePreview.style.display = 'none';
            }
        }

        unitPriceInput.addEventListener('input', updateTotal);
        quantityInput.addEventListener('input', updateTotal);
        updateTotal();
    </script>
    <script>
        let attachmentCounter = 1;

        function addFileInput() {
            const div = document.getElementById('file_inputs');
            if (div.children.length >= 5) return;
            const idx = attachmentCounter;
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-center gap-3';
            wrapper.innerHTML = `
            <label for="attachment_${idx}" title="افزودن فایل"
                class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-dark-700/70 transition-colors cursor-pointer flex-shrink-0">
                <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
            </label>
            <input type="file" id="attachment_${idx}" name="attachments[]"
                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                class="hidden" onchange="document.getElementById('attachment_name_${idx}').textContent = this.files[0] ? this.files[0].name : 'فایلی انتخاب نشده';">
            <span id="attachment_name_${idx}" class="text-xs text-dark-400">فایلی انتخاب نشده</span>
        `;
            div.appendChild(wrapper);
            attachmentCounter++;
        }
    </script>
</x-app-layout>