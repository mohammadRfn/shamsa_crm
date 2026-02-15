<x-app-layout>
    <div class="max-w-6xl mx-auto p-4 sm:p-6 md:p-8 space-y-8 text-[1.05rem] leading-relaxed">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between pb-3 border-b">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800">📄 جزئیات گزارش</h2>

            <span class="px-4 py-1 text-sm font-bold rounded-full w-fit mx-auto sm:mx-0
            @if($report->status=='approved') bg-green-100 text-green-700
            @elseif($report->status=='rejected') bg-red-100 text-red-700
            @else bg-yellow-100 text-yellow-700 @endif">
                {{ __(match($report->status){
                'approved'=>'تایید شده',
                'rejected'=>'رد شده',
                'pending'=>'در انتظار بررسی',
                default=>'جدید'
            }) }}
            </span>
        </div>


        {{-- اطلاعات فنی --}}
        <section class="bg-white shadow-sm hover:shadow-md duration-200 rounded-xl p-6 sm:p-7 space-y-5 border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center gap-2">🔧 اطلاعات فنی</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700">
                <p><b>تعمیرکار:</b> {{ $report->user->name ?? 'نامشخص' }}</p>
                <p><b>نام قطعه:</b> {{ $report->part_name }}</p>
                <p><b>تاریخ درخواست:</b> {{ $report->request_date }}</p>
                <p><b>تاریخ پایان:</b> {{ $report->end_date }}</p>
                <p><b>شماره درخواست:</b> {{ $report->request_number }}</p>
                <p><b>شماره سریال:</b> {{ $report->serial_number }}</p>
                <p><b>مدل دستگاه:</b> {{ $report->device_model }}</p>
                <p><b>نفر/ساعت:</b> {{ $report->workers_count ?? '-' }} / {{ $report->hours_per_worker ?? '-' }}</p>
                <!-- <p><b>ساعت :</b> {{ $report->hours_per_worker ?? '-' }} ساعت</p> -->

            </div>
            <div>
                <p class="font-bold text-gray-800 mb-1">شرح ایراد:</p>
                <div class="bg-gray-50 p-4 rounded-lg border text-gray-700 whitespace-pre-line">{{ $report->issue_description }}</div>
            </div>
        </section>


        {{-- شرح گزارش --}}
        <section class="bg-white shadow-sm hover:shadow-md duration-200 rounded-xl p-6 sm:p-7 space-y-6 border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center gap-2">📝 شرح و فعالیت</h3>



            <div>
                <p class="font-bold text-gray-800 mb-1">گزارش فعالیت:</p>
                <div class="bg-gray-50 p-4 rounded-lg border text-gray-700 whitespace-pre-line">{{ $report->activity_report }}</div>
            </div>

            <div>
                <p class="font-bold text-gray-800 mb-1">قطعات مصرف‌شده:</p>
                <ul class="list-disc pr-6 space-y-1 text-gray-700">
                    @foreach(json_decode($report->used_parts_list) ?? [] as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </section>


        {{-- تایید نقش‌ها --}}
        <section class="bg-white shadow-sm hover:shadow-md duration-200 rounded-xl p-6 sm:p-7 border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">👤 وضعیت تایید نقش‌ها</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-sm">

                @php
                $statusClasses = [
                1 => ['bg-green-50', 'text-green-700', 'border-green-500', '✔ تایید'],
                0 => ['bg-red-50', 'text-red-700', 'border-red-500', '✖ رد'],
                null => ['bg-gray-50', 'text-gray-600', 'border-gray-300', 'در انتظار']
                ];
                @endphp

                @foreach([
                ['label' => 'پذیرش', 'status' => $report->request_approval],
                ['label' => 'تامین', 'status' => $report->supply_approval],
                ['label' => 'مدیر', 'status' => $report->ceo_approval]
                ] as $approval)
                @php
                $classes = $statusClasses[$approval['status']] ?? $statusClasses[null];
                @endphp
                <div class="p-4 rounded-xl text-center border transition {{ $classes[2] }} {{ $classes[0] }} {{ $classes[1] }}">
                    <b>{{ $approval['label'] }}</b><br>
                    {{ $classes[3] }}
                </div>
                @endforeach

            </div>
        </section>



        {{-- دکمه‌ها --}}
        @if(in_array(auth()->user()->role,['reception','supply','ceo']))
        <div class="flex flex-row flex-wrap justify-center items-center gap-4 pt-6">

            <form action="{{ route('reports.approve',$report->id) }}" method="POST">
                @csrf
                <button
                    class="approve-btn shadow-lg transition hover:-translate-y-[2px] 
                   min-w-[160px] px-6 py-3 text-center">
                    ✔ تایید گزارش
                </button>
            </form>

            <form action="{{ route('reports.reject',$report->id) }}" method="POST">
                @csrf
                <button
                    class="reject-btn shadow-lg transition hover:-translate-y-[2px] 
                   min-w-[160px] px-6 py-3 text-center">
                    ✖ رد گزارش
                </button>
            </form>

        </div>
        @endif





    </div>
</x-app-layout>