<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-6xl mx-auto space-y-3">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2.5 flex items-center gap-3">
                <a href="{{ route('partorders.show', $partorder) }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-cream-100 truncate">ویرایش سفارش — {{ $partorder->order_number }}</h1>
                </div>
            </div>

            <form id="partorder-edit-form" action="{{ route('partorders.update', $partorder) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @method('PUT')

                {{-- اطلاعات ثابت --}}
                <div class="card-luxury p-3.5 space-y-2.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">نام تجهیز *</label>
                            <input type="text" name="equipment_name" value="{{ old('equipment_name', $partorder->equipment_name) }}" required
                                class="input-luxury w-full !py-1.5 !px-2 text-xs">
                            @error('equipment_name')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">تاریخ سفارش *</label>
                            <input type="text" name="order_date" required
                                value="{{ old('order_date', $partorder->order_date_jalali) }}"
                                class="jalali-datepicker input-luxury w-full !py-1.5 !px-2 text-xs">
                            @error('order_date')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>

            {{-- جدول قطعات — شکل و رنگ حفظ شده، فقط فشرده‌تر --}}
            <div class="card-luxury p-3.5 space-y-2.5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        لیست قطعات
                    </h2>
                    <button type="button" onclick="addRow()" class="btn-secondary !py-1 !px-3 text-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن ردیف
                    </button>
                </div>

                <p class="text-[11px] text-dark-400">
                    روی آیکون گیره کنار هر ردیف بزنید تا فایل‌های همان قطعه را مدیریت کنید.
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-dark-700/50">
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-center w-8">ردیف</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">نام قطعه</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">مشخصات</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">پکیج</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-center w-16">تعداد</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-right">توضیحات</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-[11px] text-cream-300 text-center w-10">فایل</th>
                                <th class="border border-dark-600 px-1.5 py-1.5 w-8"></th>
                            </tr>
                        </thead>
                        <tbody id="parts-body">
                            @php
                            $items = old('part_name')
                            ? collect(old('part_name'))->map(fn($name, $i) => (object)[
                            'id' => old('item_id')[$i] ?? null,
                            'part_name' => $name,
                            'specifications' => old('specifications')[$i] ?? '',
                            'package' => old('package')[$i] ?? '',
                            'quantity' => old('quantity')[$i] ?? '',
                            'description' => old('description')[$i] ?? '',
                            ])
                            : $partorder->items;

                            $realItemsById = $partorder->items->keyBy('id');
                            @endphp
                            @foreach($items as $i => $item)
                            <tr class="part-row">
                                <td class="border border-dark-600 px-1.5 py-1.5 text-center text-cream-400 text-xs row-num">{{ $i + 1 }}</td>
                                <td class="border border-dark-600 px-1 py-1">
                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}" form="partorder-edit-form">
                                    <input type="hidden" name="row_key[]" value="{{ $item->id ?? 'new_'.$i }}" form="partorder-edit-form">
                                    <input type="text" name="part_name[]" value="{{ $item->part_name }}" form="partorder-edit-form" required class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[110px]">
                                </td>
                                <td class="border border-dark-600 px-1 py-1"><input type="text" name="specifications[]" value="{{ $item->specifications }}" form="partorder-edit-form" required class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[110px]"></td>
                                <td class="border border-dark-600 px-1 py-1"><input type="text" name="package[]" value="{{ $item->package }}" form="partorder-edit-form" required class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[70px]"></td>
                                <td class="border border-dark-600 px-1 py-1"><input type="number" name="quantity[]" value="{{ $item->quantity }}" form="partorder-edit-form" min="1" required class="input-luxury w-full !py-1 !px-1.5 text-xs"></td>
                                <td class="border border-dark-600 px-1 py-1"><input type="text" name="description[]" value="{{ $item->description }}" form="partorder-edit-form" required class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[110px]"></td>
                                <td class="border border-dark-600 px-1 py-1 text-center">
                                    @php $realItem = $item->id ? $realItemsById->get($item->id) : null; @endphp
                                    @if($realItem)
                                    <button type="button"
                                        onclick="toggleAttachRow({{ $realItem->id }})"
                                        class="relative inline-flex items-center justify-center w-7 h-7 rounded-lg hover:bg-dark-700/70 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        @if($realItem->attachments->count() > 0)
                                        <span class="absolute -top-1 -left-1 w-3.5 h-3.5 bg-primary-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                                            {{ $realItem->attachments->count() }}
                                        </span>
                                        @endif
                                    </button>
                                    @else
                                    <span class="text-[11px] text-dark-500" title="بعد از ذخیره سفارش قابل استفاده است">—</span>
                                    @endif
                                </td>
                                <td class="border border-dark-600 px-1 py-1 text-center">
                                    <button type="button" class="remove-row text-red-400 hover:text-red-300 {{ count($items) <= 1 ? 'hidden' : '' }}" onclick="removeRow(this)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @if($realItem)
                            <tr id="attach-row-{{ $realItem->id }}" class="attach-row hidden">
                                <td colspan="8" class="border border-dark-600 p-2.5 bg-dark-800/40">
                                    <x-attachments.panel :model="$realItem" mode="edit" />
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- دکمه‌های عملیات --}}
            <div class="flex flex-col sm:flex-row gap-2.5 justify-end">
                <a href="{{ route('partorders.show', $partorder) }}" class="btn-secondary !py-2 !px-4 text-sm text-center">
                    انصراف
                </a>
                <button type="submit" form="partorder-edit-form" class="btn-primary !py-2 !px-4 text-sm inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    ذخیره تغییرات
                </button>
            </div>

        </div>
    </div>

    <script>
        let newItemCounter = 0;
        const rowFiles = {};

        function handleFileSelect(input, rowKey, badgeId) {
            const newFiles = Array.from(input.files);
            if (newFiles.length === 0) return;
            const existing = rowFiles[rowKey] || [];
            const merged = [...existing, ...newFiles].filter((file, index, arr) =>
                arr.findIndex(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified) === index
            );
            rowFiles[rowKey] = merged;
            const dt = new DataTransfer();
            merged.forEach(file => dt.items.add(file));
            input.files = dt.files;
            const badge = document.getElementById(badgeId);
            if (!badge) return;
            if (merged.length > 0) {
                badge.textContent = merged.length;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        function addRow() {
            const tbody = document.getElementById('parts-body');
            const rowCount = tbody.querySelectorAll('.part-row').length;

            newItemCounter++;
            const rk = newItemCounter;

            const newRow = document.createElement('tr');
            newRow.className = 'part-row';
            newRow.innerHTML = `
        <td class="border border-dark-600 px-1.5 py-1.5 text-center text-cream-400 text-xs row-num">${rowCount + 1}</td>
        <td class="border border-dark-600 px-1 py-1">
    <input type="hidden" name="row_key[]" value="new_${rk}" form="partorder-edit-form">
    <input type="text" name="part_name[]" required form="partorder-edit-form" class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[110px]">
</td>
<td class="border border-dark-600 px-1 py-1"><input type="text" name="specifications[]" required form="partorder-edit-form" class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[110px]"></td>
        <td class="border border-dark-600 px-1 py-1"><input type="text" name="package[]" required form="partorder-edit-form" class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[70px]"></td>
        <td class="border border-dark-600 px-1 py-1"><input type="number" name="quantity[]" min="1" required form="partorder-edit-form" class="input-luxury w-full !py-1 !px-1.5 text-xs"></td>
        <td class="border border-dark-600 px-1 py-1"><input type="text" name="description[]" required form="partorder-edit-form" class="input-luxury w-full !py-1 !px-1.5 text-xs min-w-[110px]"></td>
        <td class="border border-dark-600 px-1 py-1 text-center">
            <label for="item_file_new_${rk}" title="افزودن فایل" form="partorder-edit-form"
                class="relative inline-flex items-center justify-center w-7 h-7 rounded-lg hover:bg-dark-700/70 transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                <span id="file_badge_new_${rk}" class="hidden absolute -top-1 -left-1 w-3.5 h-3.5 bg-primary-500 text-white text-[9px] font-bold rounded-full items-center justify-center">0</span>
            </label>
            <input type="file" id="item_file_new_${rk}" name="item_files[new_${rk}][]" multiple
                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                class="hidden" form="partorder-edit-form" onchange="handleFileSelect(this, 'new_${rk}', 'file_badge_new_${rk}')">
        </td>
        
        <td class="border border-dark-600 px-1 py-1 text-center">
            <button type="button" class="remove-row text-red-400 hover:text-red-300" onclick="removeRow(this)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </td>
    `;
            tbody.appendChild(newRow);

            // اگر دکمه حذف سطر اول پنهان است، آن را نمایش بده
            const firstRemoveBtn = tbody.querySelector('.remove-row');
            if (firstRemoveBtn) {
                firstRemoveBtn.classList.remove('hidden');
            }
        }

        function removeRow(btn) {
            const tbody = document.getElementById('parts-body');
            if (tbody.querySelectorAll('.part-row').length > 1) {
                btn.closest('.part-row').remove();
                updateRowNumbers();
                if (tbody.querySelectorAll('.part-row').length === 1) {
                    tbody.querySelector('.remove-row').classList.add('hidden');
                }
            }
        }

        function updateRowNumbers() {
            document.querySelectorAll('.row-num').forEach((el, i) => {
                el.textContent = i + 1;
            });
        }

        function toggleAttachRow(id) {
            const row = document.getElementById('attach-row-' + id);
            if (row) row.classList.toggle('hidden');
        }
    </script>
</x-app-layout>