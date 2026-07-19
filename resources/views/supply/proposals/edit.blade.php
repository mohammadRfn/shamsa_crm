<x-app-layout>
    <div class="py-4 px-3 sm:px-4 lg:px-5">
        <div class="max-w-4xl mx-auto space-y-2">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2 flex items-center gap-2">
                <a href="{{ route('supply-proposals.show', $proposal) }}" class="p-1 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-4 h-4 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-cream-100 truncate">ویرایش پیشنهاد — {{ $proposal->part_name }}</h1>
                </div>
            </div>

            @if($errors->any())
            <div class="card-luxury p-2.5 border-red-500/40 bg-red-500/10">
                <ul class="space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li class="text-xs text-red-400 flex items-center gap-1.5">
                        <span>✕</span> {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('supply-proposals.update', $proposal) }}" class="space-y-2">
                @csrf @method('PUT')

                {{-- انتخاب سفارش قطعه + اطلاعات پیشنهاد (ادغام شده) --}}
                <div class="card-luxury p-3 space-y-2.5">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">سفارش قطعه <span class="text-primary-400">*</span></label>
                            <select name="part_order_id" id="part_order_id" required class="input-luxury w-full !py-1 !px-2 text-xs">
                                <option value="">انتخاب سفارش...</option>
                                @foreach($partOrders as $po)
                                <option value="{{ $po->id }}"
                                    data-parts="{{ json_encode($po->part_name ?? []) }}"
                                    {{ old('part_order_id', $proposal->part_order_id) == $po->id ? 'selected' : '' }}>
                                    {{ $po->order_number }} — {{ $po->equipment_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('part_order_id') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">نام قطعه <span class="text-primary-400">*</span></label>
                            <select name="part_name" id="part_name" required class="input-luxury w-full !py-1 !px-2 text-xs">
                                <option value="{{ $proposal->part_name }}" selected>{{ $proposal->part_name }}</option>
                            </select>
                            @error('part_name') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-1 border-t border-dark-600/30">
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">فروشنده / تامین‌کننده <span class="text-primary-400">*</span></label>
                            <input type="text" name="supplier_name" value="{{ old('supplier_name', $proposal->supplier_name) }}"
                                placeholder="شرکت الکترونیک پارس"
                                class="input-luxury w-full !py-1 !px-2 text-xs" required>
                            @error('supplier_name') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">قیمت واحد (تومان) <span class="text-primary-400">*</span></label>
                            <input type="number" name="unit_price" value="{{ old('unit_price', $proposal->unit_price) }}"
                                min="0" step="1"
                                class="input-luxury w-full !py-1 !px-2 text-xs" required>
                            @error('unit_price') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">تعداد <span class="text-primary-400">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity', $proposal->quantity) }}" min="1"
                                class="input-luxury w-full !py-1 !px-2 text-xs" required>
                            @error('quantity') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">تحویل تخمینی</label>
                            <input type="text" name="estimated_delivery" id="estimated_delivery"
                                value="{{ old('estimated_delivery', $proposal->estimated_delivery_jalali ?? '') }}"
                                placeholder="۱۴۰۳/۱۱/۲۸" autocomplete="off"
                                class="jalali-datepicker input-luxury w-full !py-1 !px-2 text-xs">
                            @error('estimated_delivery') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-1 border-t border-dark-600/30">
                        <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">یادداشت / توضیحات</label>
                        <textarea name="note" rows="2"
                            placeholder="توضیحات تکمیلی، شرایط پرداخت، ضمانت و..."
                            class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right">{{ old('note', $proposal->note) }}</textarea>
                        @error('note') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- پیش‌نمایش جمع کل --}}
                    <div id="price-preview" class="flex items-center justify-between px-2.5 py-1.5 rounded-lg border border-dark-600/40 bg-dark-900/30">
                        <span class="text-xs text-dark-400">پیش‌نمایش جمع کل:</span>
                        <span class="text-primary-400 font-bold text-sm" id="total-price">---</span>
                    </div>
                </div>

                {{-- دکمه‌ها --}}
                <div class="flex gap-2 justify-end pt-1">
                    <a href="{{ route('supply-proposals.show', $proposal) }}" class="btn-secondary !py-1.5 !px-4 text-sm text-center">
                        انصراف
                    </a>
                    <button type="submit" class="btn-primary !py-1.5 !px-5 text-sm inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        بروزرسانی پیشنهاد
                    </button>
                </div>
            </form>

            {{-- پیوست‌ها (مدیریت فایل‌های موجود) --}}
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
                    <x-attachments.panel :model="$proposal" mode="edit" />
                </div>
            </details>

        </div>
    </div>

    <script>
        const partOrderSelect = document.getElementById('part_order_id');
        const partNameSelect = document.getElementById('part_name');
        const oldPartName = "{{ old('part_name', $proposal->part_name) }}";

        function loadParts(selectedId) {
            const option = partOrderSelect.querySelector(`option[value="${selectedId}"]`);
            partNameSelect.innerHTML = '<option value="">انتخاب قطعه...</option>';
            if (!option || !selectedId) return;

            let parts = [];
            try { parts = JSON.parse(option.dataset.parts || '[]'); } catch (e) {}

            parts.forEach(part => {
                const opt = document.createElement('option');
                opt.value = part;
                opt.textContent = part;
                if (part === oldPartName) opt.selected = true;
                partNameSelect.appendChild(opt);
            });
        }

        partOrderSelect.addEventListener('change', function() { loadParts(this.value); });
        if (partOrderSelect.value) loadParts(partOrderSelect.value);
    </script>
    <script>
        const unitPriceInput = document.querySelector('[name="unit_price"]');
        const quantityInput = document.querySelector('[name="quantity"]');
        const totalPriceEl = document.getElementById('total-price');

        function updateTotal() {
            const price = parseFloat(unitPriceInput.value) || 0;
            const qty = parseInt(quantityInput.value) || 0;
            totalPriceEl.textContent = price > 0 && qty > 0
                ? (price * qty).toLocaleString('fa-IR') + ' تومان'
                : '---';
        }

        unitPriceInput.addEventListener('input', updateTotal);
        quantityInput.addEventListener('input', updateTotal);
        updateTotal();
    </script>
</x-app-layout>