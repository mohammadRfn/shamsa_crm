<x-app-layout>

    <div class="max-w-5xl mx-auto p-8 space-y-10">

        <h2 class="text-2xl font-bold text-gray-800 border-b pb-3">ویرایش گزارش</h2>

        <form action="{{ route('reports.update', $report) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            {{-- بخش اطلاعات اصلی --}}
            <div class="bg-white shadow rounded-xl p-6 space-y-6">

                <div class="grid grid-cols-2 gap-6 text-[15px]">

                    <div>
                        <label class="font-semibold text-gray-700">نام قطعه:</label>
                        <input value="{{ $report->part_name }}" name="part_name"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">شماره درخواست:</label>
                        <input value="{{ $report->request_number }}" name="request_number"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">تاریخ درخواست:</label>
                        <input type="text" id="request_date"
                            name="request_date"
                            value="{{ $report->request_date }}"
                            class="input w-full mt-1" required>
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">تاریخ پایان:</label>
                        <input type="text" id="end_date"
                            name="end_date"
                            value="{{ $report->end_date }}"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">سریال دستگاه:</label>
                        <input value="{{ $report->serial_number }}" name="serial_number"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">مدل دستگاه:</label>
                        <input value="{{ $report->device_model }}" name="device_model"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">تعداد نیرو:</label>
                        <input type="number" value="{{ $report->workers_count }}"
                            name="workers_count" min="1"
                            class="input w-full mt-1">
                    </div>

                    <div>
                        <label class="font-semibold text-gray-700">ساعت هر نفر:</label>
                        <input type="number" step="0.5"
                            value="{{ $report->hours_per_worker }}"
                            name="hours_per_worker"
                            class="input w-full mt-1">
                    </div>

                </div>
            </div>

            {{-- توضیحات و فعالیت --}}
            <div class="bg-white shadow rounded-xl p-6 space-y-6">
                <div>
                    <label class="font-semibold text-gray-700">شرح ایراد:</label>
                    <textarea name="issue_description"
                        class="input mt-1 w-full h-28">{{ $report->issue_description }}</textarea>
                </div>

                <div>
                    <label class="font-semibold text-gray-700">گزارش فعالیت انجام‌شده:</label>
                    <textarea name="activity_report"
                        class="input mt-1 w-full h-28">{{ $report->activity_report }}</textarea>
                </div>
            </div>

            {{-- قطعات مصرف شده --}}
            <div class="bg-white shadow rounded-xl p-6">
                <label class="font-semibold text-gray-700 block mb-3">لیست قطعات مصرف‌شده:</label>

                <div id="parts_box" class="space-y-2">
                    @foreach(json_decode($report->used_parts_list)??[] as $p)
                    <input class="input w-full" name="used_parts_list[]" value="{{ $p }}">
                    @endforeach
                </div>

                <button type="button" id="addPart"
                    class="btn-add shadow transition duration-200 mt-4">
                    + افزودن قطعه جدید
                </button>
            </div>

            {{-- دکمه ثبت --}}
            <div class="flex justify-center pt-4">
                <button class="btn-submit shadow-md transition hover:-translate-y-[2px]">
                    ✔ ثبت تغییرات
                </button>
            </div>

        </form>
    </div>


    <script>
        document.getElementById('addPart').onclick = () => {
            document.getElementById('parts_box').innerHTML +=
                `<input name="used_parts_list[]" class="input w-full mt-2">`;
        }
    </script>
    <style>
        .btn-submit {
            background: #16a34a !important;
            /* green-600 */
            color: #fff !important;
            font-weight: 700;
            font-size: 1rem;
            padding: 12px 28px;
            border-radius: 10px;
            min-width: 200px;
        }

        .btn-submit:hover {
            background: #15803d !important;
            /* green-700 */
        }

        .btn-add {
            background: #2563eb !important;
            /* blue-600 */
            color: white !important;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
        }

        .btn-add:hover {
            background: #1d4ed8 !important;
            /* blue-700 */
        }
    </style>

</x-app-layout>