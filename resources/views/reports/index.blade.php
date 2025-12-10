<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">

        <!-- TITLE -->
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">📋 لیست گزارش‌ها</h2>

            @if(auth()->user()->role=="technician")
            <a href="{{ route('reports.create') }}"
                class="w-46 px-4 py-2 rounded-lg font-semibold shadow text-white text-center transition-all duration-200"
                style="background:#2563EB;"
                onmouseover="this.style.background='#1edbc5ff'"
                onmouseout="this.style.background='#5fbe66ff'">
                + ثبت گزارش جدید
            </a>
            @endif
        </div>

        <!-- SEARCH BAR -->
        <form method="GET" action="{{ route('reports.index') }}"
            class="mb-6 flex flex-wrap gap-3 items-center">

            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="جستجو (قطعه، شماره سریال، مدل، نام تعمیرکار)"
                class="border border-gray-300 focus:ring-[#00509d] focus:border-[#00509d] p-3 rounded-lg 
               w-full md:w-1/2 text-gray-700 shadow-sm">

            <button
                class="w-48 px-4 py-2 rounded-lg font-semibold shadow text-white text-center transition-all duration-200"
                style="background:#2563EB;"
                onmouseover="this.style.background='#1E4FDB'"
                onmouseout="this.style.background='#2563EB'">
                🔍 جستجو
            </button>

            @if(request('search'))
            <a href="{{ route('reports.index') }}"
                class="w-48 px-4 py-2 rounded-lg font-semibold shadow text-white text-center transition-all duration-200"
                style="background:#EF4444;"
                onmouseover="this.style.background='#D83434'"
                onmouseout="this.style.background='#EF4444'">
                ✖ حذف فیلتر
            </a>
            @endif


        </form>

        <!-- TABLE -->
        <div class="overflow-x-auto bg-white shadow-sm rounded-xl border border-gray-200">

            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 text-gray-700 border-b">
                    <tr class="text-right">
                        <th class="p-3">قطعه</th>
                        <th class="p-3">تاریخ درخواست</th>
                        <th class="p-3">تاریخ پایان</th>
                        <th class="p-3">شماره درخواست</th>
                        <th class="p-3">تعمیرکار</th>
                        <th class="p-3">وضعیت</th>
                        <th class="p-3">عملیات</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($reports as $report)
                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="p-3">{{ $report->part_name }}</td>
                        <td class="p-3">{{ $report->request_date }}</td>
                        <td class="p-3">{{ $report->end_date }}</td>
                        <td class="p-3">{{ $report->request_number }}</td>

                        <!-- Technician Name -->
                        <td class="p-3 font-medium text-gray-800">
                            {{ $report->user->name ?? '-' }}
                        </td>

                        <!-- STATUS BADGE -->
                        <td class="p-3">
                            @php
                            $statusColor = match($report->status){
                            'approved' => 'bg-green-100 text-green-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            default => 'bg-gray-100 text-gray-700'
                            };
                            @endphp
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{$statusColor}}">
                                {{ $report->status }}
                            </span>
                        </td>

                        <td class="p-3 flex gap-2 items-center">

                            <a href="{{ route('reports.show',$report) }}"
                                class="px-3 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                👁 مشاهده
                            </a>

                            @if(auth()->id() == $report->user_id)
                            <a href="{{ route('reports.edit',$report) }}"
                                class="px-3 py-1 rounded bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">
                                ✏ ویرایش
                            </a>

                            <form action="{{ route('reports.destroy',$report) }}"
                                method="POST"
                                onsubmit="return confirm('حذف شود؟')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100 transition">
                                    🗑 حذف
                                </button>
                            </form>
                            @endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- PAGINATION -->
            <div class="p-4 border-t">
                {{ $reports->links('pagination::tailwind') }}
            </div>

        </div>
    </div>
</x-app-layout>