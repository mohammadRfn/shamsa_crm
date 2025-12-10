<x-app-layout>
    <div class="max-w-5xl mx-auto p-8 space-y-10">

        <h2 class="text-2xl font-bold text-gray-800 border-b pb-3">ثبت گزارش جدید</h2>

        <form action="{{ route('reports.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- اطلاعات اصلی --}}
            <div class="bg-white shadow rounded-xl p-6 space-y-6">
                <h3 class="font-semibold text-lg text-gray-700">مشخصات اولیه</h3>

                <div class="grid grid-cols-2 gap-6 text-[15px]">

                    <div>
                        <label class="font-medium">نام قطعه:</label>
                        <input type="text" name="part_name" class="input w-full mt-1" required>
                    </div>

                    <div>
                        <label class="font-medium">شماره درخواست:</label>
                        <input type="text" name="request_number" class="input w-full mt-1" required>
                    </div>

                    <div>
                        <label class="font-medium">تاریخ درخواست (شمسی):</label>
                        <input type="text" name="request_date" placeholder="1402/11/18"
                            class="input w-full mt-1" required>
                    </div>

                    <div>
                        <label class="font-medium">تاریخ پایان (شمسی):</label>
                        <input type="text" name="end_date"
                            placeholder="1402/11/20"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-medium">شماره سریال دستگاه:</label>
                        <input type="text" name="serial_number" class="input w-full mt-1" required>
                    </div>

                    <div>
                        <label class="font-medium">مدل دستگاه:</label>
                        <input type="text" name="device_model" class="input w-full mt-1" required>
                    </div>

                    <div>
                        <label class="font-medium">تعداد نیرو:</label>
                        <input type="number" min="1"
                            name="workers_count"
                            class="input w-full mt-1"
                            placeholder="مثال: 3" required>
                    </div>

                    <div>
                        <label class="font-medium">ساعت هر نفر:</label>
                        <input type="number" step="0.5"
                            name="hours_per_worker"
                            class="input w-full mt-1"
                            placeholder="مثال: 2.5" required>
                    </div>

                </div>
            </div>

            {{-- توضیحات فنی --}}
            <div class="bg-white shadow rounded-xl p-6 space-y-6">
                <h3 class="font-semibold text-lg text-gray-700">شرح و فعالیت انجام‌شده</h3>

                <div>
                    <label class="font-medium">شرح ایراد اعلامی:</label>
                    <textarea name="issue_description" class="input mt-2 w-full h-28" required></textarea>
                </div>

                <div>
                    <label class="font-medium">گزارش فعالیت انجام‌شده:</label>
                    <textarea name="activity_report" class="input mt-2 w-full h-28" required></textarea>
                </div>
            </div>

            {{-- قطعات مصرف‌شده --}}
            <div class="bg-white shadow rounded-xl p-6 space-y-4">
                <h3 class="font-semibold text-lg text-gray-700 mb-3">لیست قطعات مصرف‌شده</h3>

                <div id="parts_box" class="space-y-2">
                    <input type="text" name="used_parts_list[]" class="input w-full" placeholder="نام قطعه">
                </div>

                <button type="button" id="addPart"
                    class="btn-add shadow transition duration-200">
                    + افزودن قطعه جدید
                </button>

            </div>

            {{-- ثبت --}}
            <div class="flex justify-center pt-5">
                <button class="btn-submit shadow-md transition hover:-translate-y-[2px]">
                    ✔ ثبت گزارش
                </button>
            </div>

        </form>

    </div>


    <script>
        document.getElementById('addPart').onclick = () => {
            document.getElementById('parts_box').innerHTML +=
                `<input name="used_parts_list[]" class="input w-full mt-2" placeholder="نام قطعه">`;
        }
    </script>
    <style>
        .btn-submit {
            background: #16a34a !important;
            color: #fff !important;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 10px;
            min-width: 200px;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #15803d !important;
        }

        .btn-add {
            background: #2563eb !important;
            color: #fff !important;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
            min-width: 160px;
            font-size: .95rem;
        }

        .btn-add:hover {
            background: #1d4ed8 !important;
        }
    </style>

</x-app-layout>