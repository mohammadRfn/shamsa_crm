<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.index') }}" class="p-2 hover:bg-dark-700 rounded-lg transition-all">
                    <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        ثبت گزارش جدید
                    </h1>
                    <p class="text-dark-400 mt-1">اطلاعات فنی و جزئیات تعمیرات را وارد کنید</p>
                </div>
            </div>

            <form action="{{ route('reports.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- اطلاعات اصلی -->
                <div class="card-luxury p-6 space-y-6">
                    <!-- همان کد قبلی بدون تغییر -->
                    <div class="flex items-center gap-3 pb-4 border-b border-dark-700">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">اطلاعات اولیه</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- فیلدهای قبلی بدون تغییر -->
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شرح کار درخواستی *</label>
                            <input type="text" name="part_name" required class="input-luxury w-full" placeholder="مثال: مادربرد اینورتر">
                            @error('part_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شماره درخواست *</label>
                            <input type="text" name="request_number" required class="input-luxury w-full" placeholder="مثال: REQ-2026-001">
                            @error('request_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ درخواست *</label>
                            <input type="text" name="request_date" required
                                value="{{ old('request_date', jalaliToday()) }}"
                                class="jalali-datepicker input-luxury w-full" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('request_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ پایان *</label>
                            <input type="text" name="end_date" required
                                value="{{ old('end_date') }}"
                                class="jalali-datepicker input-luxury w-full" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('end_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شماره سریال دستگاه *</label>
                            <input type="text" name="serial_number" required class="input-luxury w-full" placeholder="مثال: SN-123456">
                            @error('serial_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">مدل دستگاه *</label>
                            <input type="text" name="device_model" required class="input-luxury w-full" placeholder="مثال: Siemens S7-1200">
                            @error('device_model') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">تعداد نیروی کار *</label>
                            <input type="number" name="workers_count" min="1" required class="input-luxury w-full" placeholder="مثال: 2">
                            @error('workers_count') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">ساعت کار هر نفر *</label>
                            <input type="number" name="hours_per_worker" step="0.5" min="0.5" required class="input-luxury w-full" placeholder="مثال: 3.5">
                            @error('hours_per_worker') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- توضیحات فنی - textarea های ساده و کششی -->
                <div class="card-luxury p-6 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-dark-700">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">شرح فعالیت</h2>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شرح ایراد اعلامی *</label>
                            <textarea
                                name="issue_description"
                                rows="4"
                                required
                                class="input-luxury w-full resize-y min-h-[100px] transition-all duration-200 hover:cursor-ns-resize focus:cursor-ns-resize"
                                placeholder="ایراد و مشکلات گزارش شده را به طور دقیق شرح دهید..."></textarea>
                            @error('issue_description')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">گزارش فعالیت انجام‌شده *</label>
                            <textarea
                                name="activity_report"
                                rows="4"
                                required
                                class="input-luxury w-full resize-y min-h-[100px] transition-all duration-200 hover:cursor-ns-resize focus:cursor-ns-resize"
                                placeholder="اقدامات انجام شده و نتیجه کار را شرح دهید..."></textarea>
                            @error('activity_report')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- قطعات مصرف‌شده -->
                <div class="card-luxury p-6 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-dark-700">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">قطعات مصرف‌شده</h2>
                    </div>

                    <div id="parts_container" class="space-y-3">
                        <input type="text" name="used_parts_list[]" class="input-luxury w-full" placeholder="نام قطعه مصرفی">
                    </div>

                    <button type="button" id="add_part" class="btn-secondary w-full sm:w-auto inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        افزودن قطعه جدید
                    </button>
                </div>

                <!-- دکمه‌های عملیات -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="{{ route('reports.index') }}" class="btn-secondary text-center">انصراف</a>
                    <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ثبت گزارش
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        // فقط اسکریپت ساده برای قطعات
        document.getElementById('add_part').addEventListener('click', function() {
            const container = document.getElementById('parts_container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'used_parts_list[]';
            input.className = 'input-luxury w-full';
            input.placeholder = 'نام قطعه مصرفی';
            container.appendChild(input);
        });
    </script>
    <script src="https://unpkg.com/persian-date@latest/dist/persian-date.min.js"></script>
    <script src="https://unpkg.com/persian-datepicker@latest/dist/js/persian-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.jalali-datepicker').persianDatepicker({
                format: 'YYYY/MM/DD',
                autoClose: true,
                initialValue: true
            });
        });
    </script>
</x-app-layout>