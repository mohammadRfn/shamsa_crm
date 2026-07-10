<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex items-center gap-4">
                <a href="{{ route('partorders.show', $partorder) }}" class="p-2 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                    <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        ویرایش سفارش
                    </h1>
                    <p class="text-dark-400 mt-1">شماره سفارش: {{ $partorder->order_number }}</p>
                </div>
            </div>

            <form id="partorder-edit-form" action="{{ route('partorders.update', $partorder) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- اطلاعات ثابت -->
                <div class="card-luxury p-6 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">مشخصات سفارش</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">نام تجهیز *</label>
                            <input type="text" name="equipment_name" value="{{ old('equipment_name', $partorder->equipment_name) }}" required
                                class="input-luxury w-full">
                            @error('equipment_name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ سفارش *</label>
                            <input type="text" name="order_date" required
                                value="{{ old('order_date', $partorder->order_date_jalali) }}"
                                class="jalali-datepicker input-luxury w-full">
                            @error('order_date')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
            <!-- جدول قطعات -->
            <div class="card-luxury p-6 space-y-4">
                <div class="flex items-center justify-between pb-4 border-b-2 divider">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">لیست قطعات</h2>
                    </div>
                    <button type="button" onclick="addRow()" class="btn-secondary text-sm px-4 py-2 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن ردیف
                    </button>
                </div>

                <p class="text-xs text-dark-400">
                    روی آیکون گیره کنار هر ردیف بزنید تا فایل‌های همان قطعه را مدیریت کنید.
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-dark-700/50">
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-center w-10">ردیف</th>
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-right">نام قطعه</th>
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-right">مشخصات</th>
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-right">پکیج</th>
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-center w-24">تعداد</th>
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-right">توضیحات</th>
                                <th class="border border-dark-600 px-3 py-2 text-xs text-cream-300 text-center w-14">فایل</th>
                                <th class="border border-dark-600 px-2 py-2 w-10"></th>
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
                                <td class="border border-dark-600 px-2 py-2 text-center text-cream-400 text-sm row-num">{{ $i + 1 }}</td>
                                <td class="border border-dark-600 px-1 py-1">
                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}" form="partorder-edit-form">
                                    <input type="text" name="part_name[]" value="{{ $item->part_name }}" form="partorder-edit-form" required class="input-luxury w-full text-sm py-1 min-w-[120px]">
                                </td>
                                <td class="border border-dark-600 px-1 py-1"><input type="text" name="specifications[]" value="{{ $item->specifications }}" form="partorder-edit-form" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                                <td class="border border-dark-600 px-1 py-1"><input type="text" name="package[]" value="{{ $item->package }}" form="partorder-edit-form" required class="input-luxury w-full text-sm py-1 min-w-[80px]"></td>
                                <td class="border border-dark-600 px-1 py-1"><input type="number" name="quantity[]" value="{{ $item->quantity }}" form="partorder-edit-form" min="1" required class="input-luxury w-full text-sm py-1"></td>
                                <td class="border border-dark-600 px-1 py-1"><input type="text" name="description[]" value="{{ $item->description }}" form="partorder-edit-form" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                                <td class="border border-dark-600 px-1 py-1 text-center">
                                    @php $realItem = $item->id ? $realItemsById->get($item->id) : null; @endphp
                                    @if($realItem)
                                    <button type="button"
                                        onclick="toggleAttachRow({{ $realItem->id }})"
                                        class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-dark-700/70 transition-colors">
                                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        @if($realItem->attachments->count() > 0)
                                        <span class="absolute -top-1 -left-1 w-4 h-4 bg-primary-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                            {{ $realItem->attachments->count() }}
                                        </span>
                                        @endif
                                    </button>
                                    @else
                                    <span class="text-xs text-dark-500" title="بعد از ذخیره سفارش قابل استفاده است">—</span>
                                    @endif
                                </td>
                                <td class="border border-dark-600 px-1 py-1 text-center">
                                    <button type="button" class="remove-row text-red-400 hover:text-red-300 {{ count($items) <= 1 ? 'hidden' : '' }}" onclick="removeRow(this)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @if($realItem)
                            <tr id="attach-row-{{ $realItem->id }}" class="attach-row hidden">
                                <td colspan="8" class="border border-dark-600 p-3 bg-dark-800/40">
                                    <x-attachments.panel :model="$realItem" mode="edit" />
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- دکمه‌های عملیات -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('partorders.show', $partorder) }}" class="btn-secondary text-center">
                    انصراف
                </a>
                <button type="submit" form="partorder-edit-form" class="btn-primary inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    ذخیره تغییرات
                </button>
            </div>


        </div>
    </div>

    <script>
        function addRow() {
            const tbody = document.getElementById('parts-body');
            const rowCount = tbody.querySelectorAll('.part-row').length;

            const newRow = document.createElement('tr');
            newRow.className = 'part-row';
            newRow.innerHTML = `
                <td class="border border-dark-600 px-2 py-2 text-center text-cream-400 text-sm row-num">${rowCount + 1}</td>
                <td class="border border-dark-600 px-1 py-1"><input type="text" name="part_name[]" required form="partorder-edit-form" class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                <td class="border border-dark-600 px-1 py-1"><input type="text" name="specifications[]" required form="partorder-edit-form" class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                <td class="border border-dark-600 px-1 py-1"><input type="text" name="package[]" required form="partorder-edit-form" class="input-luxury w-full text-sm py-1 min-w-[80px]"></td>
                <td class="border border-dark-600 px-1 py-1"><input type="number" name="quantity[]" min="1" required form="partorder-edit-form" class="input-luxury w-full text-sm py-1"></td>
                <td class="border border-dark-600 px-1 py-1"><input type="text" name="description[]" required form="partorder-edit-form" class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                <td class="border border-dark-600 px-1 py-1 text-center">
                    <span class="text-xs text-dark-500" title="بعد از ذخیره سفارش قابل استفاده است">—</span>
                </td>
                <td class="border border-dark-600 px-1 py-1 text-center">
                    <button type="button" class="remove-row text-red-400 hover:text-red-300" onclick="removeRow(this)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            tbody.querySelector('.remove-row').classList.remove('hidden');
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