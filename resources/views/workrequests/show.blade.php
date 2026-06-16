<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('workrequests.index') }}" class="p-2 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                        <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                            جزئیات گردش کار
                        </h1>
                        <p class="text-dark-400 mt-1">شماره: {{ $workrequest->request_number }}</p>
                    </div>
                </div>

                <div class="flex flex-col gap-2 items-end">
                    @php
                    $statusConfig = match($workrequest->status) {
                    'approved' => ['badge-success', 'تایید شده', '✓'],
                    'rejected' => ['badge-danger', 'رد شده', '✕'],
                    'pending' => ['badge-warning', 'در انتظار', '⏱'],
                    'sent' => ['badge-info', 'ارسال شده', '📤'],
                    default => ['badge-info', 'جدید', '★']
                    };

                    $typeConfig = match($workrequest->request_type) {
                    'repair' => ['bg-red-500/20 text-red-400 border-red-500/30', '🔧 تعمیر'],
                    'service' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', '⚙️ سرویس'],
                    'install' => ['bg-green-500/20 text-green-400 border-green-500/30', '🔌 نصب'],
                    'sale' => ['bg-yellow-500/20 text-yellow-400 border-yellow-500/30', '💰 فروش'],
                    default => ['bg-dark-700 text-dark-400 border-dark-600', '📋']
                    };
                    @endphp
                    <span class="badge {{ $statusConfig[0] }} text-lg shadow-lg">
                        {{ $statusConfig[2] }} {{ $statusConfig[1] }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border-2 {{ $typeConfig[0] }}">
                        {{ $typeConfig[1] }}
                    </span>
                </div>
            </div>

            <!-- جدول اصلی فرم -->
            <div class="card-luxury p-4 md:p-6 overflow-x-auto">
                <div class="min-w-[800px]">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-dark-700/50">
                                <th class="border-2 border-dark-600 p-3 text-right text-cream-100 font-bold">شرح کار درخواستی</th>
                                <th class="border-2 border-dark-600 p-3 text-center text-cream-100 font-bold w-32">مدل</th>
                                <th class="border-2 border-dark-600 p-3 text-center text-cream-100 font-bold w-40">تاریخ درخواست/ورود</th>
                                <th class="border-2 border-dark-600 p-3 text-center text-cream-100 font-bold w-32">شماره درخواست</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border-2 border-dark-600 p-3">
                                    <div class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 min-h-[100px] flex items-center">
                                        {{ $workrequest->work_description ?: '---' }}
                                    </div>
                                </td>
                                <td class="border-2 border-dark-600 p-3">
                                    <div class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 text-center font-bold">
                                        {{ $workrequest->device_model ?: '---' }}
                                    </div>
                                </td>
                                <td class="border-2 border-dark-600 p-3">
                                    <div class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 text-center">
                                        {{ $workrequest->request_date_jalali }}
                                    </div>
                                </td>
                                <td class="border-2 border-dark-600 p-3">
                                    <div class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 text-center font-bold text-lg">
                                        {{ $workrequest->request_number ?: '---' }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- مشخصات دستگاه -->
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">مشخصات دستگاه</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">ثبت‌کننده</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->user->name ?? '---' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">شماره سریال دستگاه</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->serial_number ?: '---' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">نوع درخواست</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100 flex items-center justify-center">
                            @php
                            $types = [
                            'repair' => '🔧 تعمیرات',
                            'service' => '⚙️ سرویس و نصب',
                            'install' => '🔌 ساخت',
                            'sale' => '💰 فروش'
                            ];
                            @endphp
                            {{ $types[$workrequest->request_type] ?? '---' }}
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-cream-200 mb-2">واحد درخواست کننده</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->request_unit ?: '---' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- اطلاعات تماس -->
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">اطلاعات تماس</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">شماره تماس</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100 direction-ltr text-right">
                            {{ $workrequest->contact_phone ?: '---' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">مسئول پیگیری درخواست</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->contact_person ?: '---' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- شرح کار -->
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">شرح کار</h2>
                </div>

                <div class="space-y-6">
                    @if($workrequest->issue_description)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">شرح ایراد اعلامی</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100 min-h-[80px] flex items-start whitespace-pre-wrap">
                            {{ $workrequest->issue_description }}
                        </div>
                    </div>
                    @endif

                    @if($workrequest->workflow_description)
                    <div class="pt-6 border-t-2 border-dark-700/50">
                        <label class="block text-sm font-medium text-cream-200 mb-4">شرح گردش کار</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100 min-h-[160px] flex items-start whitespace-pre-wrap">
                            {{ $workrequest->workflow_description }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- اطلاعات مالی (نمایش) -->
            @if($workrequest->estimated_cost || $workrequest->final_cost || $workrequest->payment_status)
            <div class="card-luxury p-6 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-cream-100">اطلاعات مالی</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($workrequest->estimated_cost)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">هزینه براورد شده اولیه</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-yellow-400 font-bold text-lg">
                            {{ number_format($workrequest->estimated_cost) }} ریال
                        </div>
                    </div>
                    @endif

                    @if($workrequest->final_cost)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">هزینه نهایی</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-green-400 font-bold text-lg">
                            {{ number_format($workrequest->final_cost) }} ریال
                        </div>
                    </div>
                    @endif

                    @if($workrequest->payment_status)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">وضعیت پرداخت</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            @switch($workrequest->payment_status)
                            @case('credit') اعتباری @break
                            @case('cash') نقدی @break
                            @case('documents') اسنادی @break
                            @endswitch
                        </div>
                    </div>
                    @endif

                    @if($workrequest->initial_price_result)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">نتیجه اعلام قیمت اولیه</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->initial_price_result }}
                        </div>
                    </div>
                    @endif

                    @if($workrequest->invoice_number)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">شماره فاکتور</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->invoice_number }}
                        </div>
                    </div>
                    @endif

                    @if($workrequest->bank_name)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">نام بانک</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->bank_name }}
                        </div>
                    </div>
                    @endif
                    @if($workrequest->bank_payment_date)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ پرداخت بانک</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                            {{ $workrequest->bank_payment_date ? toJalali($workrequest->bank_payment_date) : '---' }}
                        </div>
                    </div>
                    @endif

                    @if($workrequest->bank_payment_amount)
                    <div>
                        <label class="block text-sm font-medium text-cream-200 mb-2">مبلغ پرداخت بانک</label>
                        <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100 font-bold text-lg">
                            {{ number_format($workrequest->bank_payment_amount) }} ریال
                        </div>
                    </div>
                    @endif

                    @if($workrequest->accounting_document || $workrequest->receipt_document)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t-2 divider">
                        @if($workrequest->accounting_document)
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">سند حسابداری</label>
                            <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                                {{ $workrequest->accounting_document }}
                            </div>
                        </div>
                        @endif

                        @if($workrequest->receipt_document)
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">سند دریافت</label>
                            <div class="input-luxury w-full bg-dark-900/50 px-3 py-2 rounded-lg text-cream-100">
                                {{ $workrequest->receipt_document }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                <!-- وضعیت تاییدها — فقط پذیرش و مدیر عامل -->
                <div class="card-luxury p-6 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">وضعیت تاییدها</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                        $approvals = [
                        ['label' => 'پذیرش', 'status' => $workrequest->request_approval, 'role' => 'reception'],
                        ['label' => 'مدیر عامل', 'status' => $workrequest->ceo_approval, 'role' => 'ceo'],
                        ];
                        @endphp

                        @foreach($approvals as $approval)
                        @php
                        $statusVal = $approval['status'];
                        if ($statusVal === 1 || $statusVal === '1' || $statusVal === true) {
                        $config = ['bg-green-500/25 border-green-500/40', 'text-green-300', '✓ تایید شده'];
                        } elseif ($statusVal === 0 || $statusVal === '0' || $statusVal === false) {
                        $config = ['bg-red-500/25 border-red-500/40', 'text-red-300', '✕ رد شده'];
                        } else {
                        $config = ['bg-dark-800/50 border-dark-600', 'text-dark-400', '⏱ در انتظار'];
                        }
                        @endphp
                        <div class="p-4 rounded-xl border-2 text-center {{ $config[0] }} transition-all duration-300">
                            <div class="text-lg font-bold {{ $config[1] }} mb-1">
                                {{ $approval['label'] }}
                            </div>
                            <div class="text-sm {{ $config[1] }}">
                                {{ $config[2] }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- تاریخچه تاییدها -->
                    @if($workrequest->approvals->count() > 0)
                    <div class="mt-6 space-y-3">
                        <h3 class="text-sm font-semibold text-cream-200">تاریخچه تاییدها:</h3>
                        @foreach($workrequest->approvals as $approval)
                        <div class="section-inner flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center text-cream-50 font-bold text-xs shadow-md">
                                    {{ mb_substr($approval->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-cream-100 font-medium">{{ $approval->user->name }}</p>
                                    <p class="text-xs text-dark-400">{{ $approval->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $approval->isApproved() ? 'badge-success' : 'badge-danger' }}">
                                    {{ $approval->isApproved() ? 'تایید' : 'رد' }}
                                </span>
                                @if($approval->comment)
                                <p class="text-xs text-dark-400 mt-1 max-w-xs">{{ $approval->comment }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- دکمه‌های تایید/رد — فقط رسپشن و CEO -->
                @if(auth()->user()->isReception() || auth()->user()->isCEO())
                <div class="card-luxury p-6">
                    <h3 class="text-lg font-bold text-cream-100 mb-4">اقدام شما:</h3>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form action="{{ route('workrequests.approve', $workrequest) }}" method="POST" class="flex-1">
                            @csrf
                            <textarea name="comment" rows="2" placeholder="نظر شما (اختیاری)"
                                class="input-luxury w-full mb-3 resize-none"></textarea>
                            <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                تایید درخواست
                            </button>
                        </form>

                        <form action="{{ route('workrequests.reject', $workrequest) }}" method="POST" class="flex-1">
                            @csrf
                            <textarea name="comment" rows="2" placeholder="دلیل رد *" required
                                class="input-luxury w-full mb-3 resize-none"></textarea>
                            <button type="submit" class="w-full px-6 py-3 rounded-xl font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center justify-center gap-2 shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                رد درخواست
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- فرم اطلاعات مالی — رسپشن و CEO -->
                @if(auth()->user()->isCEO() || auth()->user()->isReception())
                <div class="card-luxury p-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider mb-6">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-cream-100">تکمیل اطلاعات مالی</h3>
                    </div>

                    <form action="{{ route('workrequests.financial', $workrequest) }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">هزینه برآورد شده اولیه (ریال)</label>
                                <input type="number" name="estimated_cost"
                                    value="{{ old('estimated_cost', $workrequest->estimated_cost) }}"
                                    min="0" class="input-luxury w-full" placeholder="0">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">نتیجه اعلام قیمت اولیه</label>
                                <input type="text" name="initial_price_result"
                                    value="{{ old('initial_price_result', $workrequest->initial_price_result) }}"
                                    class="input-luxury w-full" placeholder="قبول / رد / در انتظار">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">هزینه نهایی (ریال)</label>
                                <input type="number" name="final_cost"
                                    value="{{ old('final_cost', $workrequest->final_cost) }}"
                                    min="0" class="input-luxury w-full" placeholder="0">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">وضعیت پرداخت</label>
                                <select name="payment_status" class="input-luxury w-full">
                                    <option value="">انتخاب کنید</option>
                                    <option value="credit" {{ old('payment_status', $workrequest->payment_status) == 'credit' ? 'selected' : '' }}>اعتباری</option>
                                    <option value="cash" {{ old('payment_status', $workrequest->payment_status) == 'cash' ? 'selected' : '' }}>نقدی</option>
                                    <option value="documents" {{ old('payment_status', $workrequest->payment_status) == 'documents' ? 'selected' : '' }}>اسنادی</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">شماره فاکتور</label>
                                <input type="text" name="invoice_number"
                                    value="{{ old('invoice_number', $workrequest->invoice_number) }}"
                                    class="input-luxury w-full">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">نام بانک</label>
                                <input type="text" name="bank_name"
                                    value="{{ old('bank_name', $workrequest->bank_name) }}"
                                    class="input-luxury w-full">
                            </div>
                            {{-- بعد از فیلد bank_name --}}
                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ پرداخت بانک</label>
                                <input type="text" name="bank_payment_date"
                                    value="{{ old('bank_payment_date', $workrequest->bank_payment_date ? toJalali($workrequest->bank_payment_date) : '') }}"
                                    class="jalali-datepicker input-luxury w-full" placeholder="۱۴۰۳/۱۱/۲۸">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">مبلغ پرداخت بانک (ریال)</label>
                                <input type="number" name="bank_payment_amount"
                                    value="{{ old('bank_payment_amount', $workrequest->bank_payment_amount) }}"
                                    min="0" class="input-luxury w-full" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">سند حسابداری</label>
                                <input type="text" name="accounting_document"
                                    value="{{ old('accounting_document', $workrequest->accounting_document) }}"
                                    class="input-luxury w-full">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-cream-200 mb-2">سند دریافت</label>
                                <input type="text" name="receipt_document"
                                    value="{{ old('receipt_document', $workrequest->receipt_document) }}"
                                    class="input-luxury w-full">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t-2 divider">
                            <button type="submit" class="btn-primary inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                ذخیره اطلاعات مالی
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- دانلود PDF -->
                <a href="{{ route('workrequests.pdf', $workrequest) }}"
                    class="btn-secondary inline-flex items-center gap-2" target="_blank">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    دانلود PDF
                </a>
                <x-attachments.panel
                    :model="$workrequest"
                    mode="show" />
                @include('workrequests.partials._stages', ['workrequest' => $workrequest])
                <!-- Comments Section -->
                <x-comments-section
                    :reportable="$workrequest"
                    reportableType="App\Models\WorkRequest" />
                <!-- دکمه‌های ویرایش/حذف — فقط رسپشن و وضعیت pending/new -->
                @if((auth()->user()->isReception() || auth()->user()->isCEO()) && in_array($workrequest->status, ['new', 'pending']))
                <div class="flex gap-4 justify-end">
                    <a href="{{ route('workrequests.edit', $workrequest) }}" class="btn-secondary inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        ویرایش درخواست
                    </a>

                    <form action="{{ route('workrequests.destroy', $workrequest) }}" method="POST" onsubmit="return confirm('آیا از حذف این درخواست اطمینان دارید؟')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-6 py-3 rounded-xl font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center gap-2 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            حذف درخواست
                        </button>
                    </form>
                </div>
                @endif

                <!-- اطلاعات اضافی -->
                @if($workrequest->last_action_at)
                <div class="card-luxury p-4">
                    <div class="flex items-center gap-3 text-sm text-dark-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>آخرین تغییر توسط <span class="text-cream-200 font-medium">{{ $workrequest->lastActionBy->name ?? 'سیستم' }}</span> در تاریخ {{ $workrequest->last_action_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
                @endif

            </div>
        </div>
</x-app-layout>