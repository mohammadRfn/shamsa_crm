<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-3">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2.5 flex items-center gap-3">
                <a href="{{ route('reports.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-cream-100 truncate">ثبت گزارش جدید</h1>
                </div>
            </div>

            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="task_id" value="{{ $taskId ?? '' }}">

                {{-- اطلاعات اولیه + شرح فعالیت (ادغام‌شده) --}}
                <div class="card-luxury p-3.5 space-y-3">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح کار درخواستی *</label>
                            <input type="text" name="part_name" required class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="مثال: مادربرد اینورتر">
                            @error('part_name') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شماره درخواست *</label>
                            <input type="text" name="request_number" required class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="REQ-2026-001">
                            @error('request_number') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">تاریخ درخواست *</label>
                            <input type="text" name="request_date" required
                                value="{{ old('request_date', jalaliToday()) }}"
                                class="jalali-datepicker input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('request_date') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">تاریخ پایان *</label>
                            <input type="text" name="end_date" required
                                value="{{ old('end_date') }}"
                                class="jalali-datepicker input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('end_date') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شماره سریال دستگاه *</label>
                            <input type="text" name="serial_number" required class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="SN-123456">
                            @error('serial_number') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">مدل دستگاه *</label>
                            <input type="text" name="device_model" required class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="Siemens S7-1200">
                            @error('device_model') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">تعداد نیروی کار *</label>
                            <input type="number" name="workers_count" min="1" required class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="2">
                            @error('workers_count') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">ساعت کار هر نفر *</label>
                            <input type="number" name="hours_per_worker" step="0.5" min="0.5" required class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="3.5">
                            @error('hours_per_worker') <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- شرح ایراد + گزارش فعالیت (دو‌ستونه، داخل همین کارت) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-2 border-t border-dark-700/60">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح ایراد اعلامی *</label>
                            <textarea
                                name="issue_description"
                                rows="2"
                                required
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                placeholder="ایراد و مشکلات گزارش شده را به طور دقیق شرح دهید..."></textarea>
                            @error('issue_description')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">گزارش فعالیت انجام‌شده *</label>
                            <textarea
                                name="activity_report"
                                rows="2"
                                required
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                placeholder="اقدامات انجام شده و نتیجه کار را شرح دهید..."></textarea>
                            @error('activity_report')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- قطعات مصرف‌شده — گرید ۴ ستونه --}}
                <div class="card-luxury p-3.5 space-y-2.5">
                    <h2 class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        قطعات مصرف‌شده
                    </h2>

                    <div id="parts_container" class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <input type="text" name="used_parts_list[]" class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="نام قطعه مصرفی">
                    </div>

                    <button type="button" id="add_part" class="btn-secondary !py-1 !px-3 text-xs w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن قطعه جدید
                    </button>
                </div>

                {{-- فایل‌های ضمیمه اختیاری (جمع‌وبازشو) --}}
                <details class="card-luxury group">
                    <summary class="p-2.5 cursor-pointer font-bold text-sm text-cream-100 flex justify-between items-center list-none [&::-webkit-details-marker]:hidden">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            فایل‌های ضمیمه (اختیاری)
                        </span>
                        <svg class="w-4 h-4 text-dark-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="p-3 pt-0 border-t border-dark-700/60 mt-1 space-y-2">
                        <div id="file_inputs" class="space-y-2 mt-2">
                            <input type="file" name="attachments[]"
                                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                                class="input-luxury w-full !py-1 !px-2 text-xs">
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <button type="button" onclick="addFileInput()"
                                class="btn-secondary !py-1 !px-3 text-xs inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                افزودن فایل دیگر
                            </button>
                            <p class="text-[10px] text-dark-400">حداکثر ۵۰ مگابایت — تا ۵ فایل</p>
                        </div>
                    </div>
                </details>

                {{-- دکمه‌های عملیات --}}
                <div class="flex flex-col sm:flex-row gap-2.5 justify-end">
                    <a href="{{ route('reports.index') }}" class="btn-secondary !py-2 !px-4 text-sm text-center">انصراف</a>
                    <button type="submit" class="btn-primary !py-2 !px-4 text-sm inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ثبت گزارش
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.getElementById('add_part').addEventListener('click', function() {
            const container = document.getElementById('parts_container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'used_parts_list[]';
            input.className = 'input-luxury w-full !py-1.5 !px-2 text-xs';
            input.placeholder = 'نام قطعه مصرفی';
            container.appendChild(input);
        });
    </script>

    <script>
        function addFileInput() {
            const div = document.getElementById('file_inputs');
            if (div.children.length >= 5) return;
            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'attachments[]';
            input.accept = '.jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx';
            input.className = 'input-luxury w-full !py-1 !px-2 text-xs';
            div.appendChild(input);
        }
    </script>
</x-app-layout>