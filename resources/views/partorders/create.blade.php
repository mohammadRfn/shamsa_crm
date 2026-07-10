<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex items-center gap-4">
                <a href="{{ route('partorders.index') }}" class="p-2 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                    <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        ثبت سفارش قطعه
                    </h1>
                    <p class="text-dark-400 mt-1">اطلاعات سفارش قطعه یدکی را وارد کنید</p>
                </div>
            </div>

            <form action="{{ route('partorders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">

                @csrf
                <input type="hidden" name="task_id" value="{{ $taskId ?? '' }}">

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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">نام تجهیز *</label>
                            <input type="text" name="equipment_name" required
                                class="input-luxury w-full"
                                placeholder="مثال: دستگاه جوش اینورتر">
                            @error('equipment_name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شماره سفارش *</label>
                            <input type="text" name="order_number" required
                                class="input-luxury w-full"
                                placeholder="مثال: ORD-2026-001">
                            @error('order_number')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ سفارش *</label>
                            <input type="text" name="order_date" required
                                value="{{ old('order_date', jalaliToday()) }}"
                                class="jalali-datepicker input-luxury w-full" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('order_date')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

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
                        روی آیکون گیره کنار هر ردیف بزنید تا فایل مربوط به همان قطعه را انتخاب کنید (اختیاری).
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
                                <tr class="part-row">
                                    <td class="border border-dark-600 px-2 py-2 text-center text-cream-400 text-sm row-num">1</td>
                                    <td class="border border-dark-600 px-1 py-1"><input type="text" name="part_name[]" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                                    <td class="border border-dark-600 px-1 py-1"><input type="text" name="specifications[]" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                                    <td class="border border-dark-600 px-1 py-1"><input type="text" name="package[]" required class="input-luxury w-full text-sm py-1 min-w-[80px]"></td>
                                    <td class="border border-dark-600 px-1 py-1"><input type="number" name="quantity[]" min="1" required class="input-luxury w-full text-sm py-1"></td>
                                    <td class="border border-dark-600 px-1 py-1"><input type="text" name="description[]" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
                                    <td class="border border-dark-600 px-1 py-1 text-center">
                                        <input type="hidden" name="row_key[]" value="0">
                                        <label for="item_file_0" title="افزودن فایل"
                                            class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-dark-700/70 transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span id="file_badge_0" class="hidden absolute -top-1 -left-1 w-4 h-4 bg-primary-500 text-white text-[10px] font-bold rounded-full items-center justify-center">0</span>
                                        </label>
                                        <input type="file" id="item_file_0" name="item_files[0][]" multiple
                                            accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                            class="hidden" onchange="handleFileSelect(this, 0, 'file_badge_0')">
                                    </td>
                                    <td class="border border-dark-600 px-1 py-1 text-center">
                                        <button type="button" class="remove-row text-red-400 hover:text-red-300 hidden" onclick="removeRow(this)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @error('part_name')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- فایل‌های ضمیمه عمومی (سطح کل سفارش)
                <div class="card-luxury p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">فایل‌های ضمیمه عمومی سفارش (اختیاری)</h2>
                    </div>

                    <div id="file_inputs" class="space-y-2">
                        <div class="flex items-center gap-3">
                            <label for="attachment_0" title="افزودن فایل"
                                class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-dark-700/70 transition-colors cursor-pointer flex-shrink-0">
                                <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <span id="attachment_badge_0" class="hidden absolute -top-1 -left-1 w-4 h-4 bg-primary-500 text-white text-[10px] font-bold rounded-full items-center justify-center">0</span>
                            </label>
                            <input type="file" id="attachment_0" name="attachments[]"
                                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                class="hidden" onchange="updateFileBadge(this, 'attachment_badge_0')">
                            <span id="attachment_name_0" class="text-xs text-dark-400">فایلی انتخاب نشده</span>
                        </div>
                    </div>

                    <button type="button" onclick="addFileInput()"
                        class="btn-secondary text-sm inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن فایل دیگر
                    </button>
                    <p class="text-xs text-dark-400">JPG، PNG، WEBP، PDF، Word، Excel — حداکثر ۵۰ مگابایت — تا ۵ فایل</p>
                </div> -->

                <!-- دکمه‌های عملیات -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="{{ route('partorders.index') }}" class="btn-secondary text-center">
                        انصراف
                    </a>
                    <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ثبت سفارش
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        let rowKeyCounter = 1;
        let attachmentCounter = 1;

        function addRow() {
            const tbody = document.getElementById('parts-body');
            const rowCount = tbody.querySelectorAll('.part-row').length;
            const rk = rowKeyCounter;

            const newRow = document.createElement('tr');
            newRow.className = 'part-row';
            newRow.innerHTML = `
            <td class="border border-dark-600 px-2 py-2 text-center text-cream-400 text-sm row-num">${rowCount + 1}</td>
            <td class="border border-dark-600 px-1 py-1"><input type="text" name="part_name[]" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
            <td class="border border-dark-600 px-1 py-1"><input type="text" name="specifications[]" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
            <td class="border border-dark-600 px-1 py-1"><input type="text" name="package[]" required class="input-luxury w-full text-sm py-1 min-w-[80px]"></td>
            <td class="border border-dark-600 px-1 py-1"><input type="number" name="quantity[]" min="1" required class="input-luxury w-full text-sm py-1"></td>
            <td class="border border-dark-600 px-1 py-1"><input type="text" name="description[]" required class="input-luxury w-full text-sm py-1 min-w-[120px]"></td>
            <td class="border border-dark-600 px-1 py-1 text-center">
                <input type="hidden" name="row_key[]" value="${rk}">
                <label for="item_file_${rk}" title="افزودن فایل"
                    class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-dark-700/70 transition-colors cursor-pointer">
                    <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <span id="file_badge_${rk}" class="hidden absolute -top-1 -left-1 w-4 h-4 bg-primary-500 text-white text-[10px] font-bold rounded-full items-center justify-center">0</span>
                </label>
                <input type="file" id="item_file_${rk}" name="item_files[${rk}][]" multiple
                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                    class="hidden" onchange="handleFileSelect(this, ${rk}, 'file_badge_${rk}')">
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
            rowKeyCounter++;

            tbody.querySelector('.remove-row').classList.remove('hidden');
        }

        function removeRow(btn) {
            const tbody = document.getElementById('parts-body');
            if (tbody.querySelectorAll('.part-row').length > 1) {
                const row = btn.closest('.part-row');
                const rowKeyInput = row.querySelector('input[name="row_key[]"]');
                if (rowKeyInput) delete rowFiles[rowKeyInput.value];
                row.remove();
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
    </script>

    <script>
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
                    <span id="attachment_badge_${idx}" class="hidden absolute -top-1 -left-1 w-4 h-4 bg-primary-500 text-white text-[10px] font-bold rounded-full items-center justify-center">0</span>
                </label>
                <input type="file" id="attachment_${idx}" name="attachments[]"
                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                    class="hidden" onchange="if(this.files.length){document.getElementById('attachment_badge_${idx}').textContent='1';document.getElementById('attachment_badge_${idx}').classList.remove('hidden');document.getElementById('attachment_badge_${idx}').classList.add('flex');} document.getElementById('attachment_name_${idx}').textContent = this.files[0] ? this.files[0].name : 'فایلی انتخاب نشده';">
                <span id="attachment_name_${idx}" class="text-xs text-dark-400">فایلی انتخاب نشده</span>
            `;
            div.appendChild(wrapper);
            attachmentCounter++;
        }
    </script>
</x-app-layout>