<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-3">

            {{-- ═══ هدر فشرده ═══ --}}
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
            $types = ['repair' => '🔧 تعمیرات', 'service' => '⚙️ سرویس و نصب', 'install' => '🔌 ساخت', 'sale' => '💰 فروش'];
            @endphp
            <div class="card-luxury p-2.5 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <a href="{{ route('workrequests.index') }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                        <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold text-cream-100 truncate">جزئیات گردش کار — {{ $workrequest->request_number }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <!-- <span class="badge {{ $statusConfig[0] }} !text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span> -->
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $typeConfig[0] }}">{{ $typeConfig[1] }}</span>
                    <a href="{{ route('workrequests.pdf', $workrequest) }}" target="_blank"
                        class="btn-secondary !py-1.5 !px-3 text-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF
                    </a>
                    @if((auth()->user()->isReception() || auth()->user()->isCEO()) && in_array($workrequest->status, ['new', 'pending']))
                    <a href="{{ route('workrequests.edit', $workrequest) }}" class="p-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form action="{{ route('workrequests.destroy', $workrequest) }}" method="POST" onsubmit="return confirm('آیا از حذف این درخواست اطمینان دارید؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25" title="حذف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- اطلاعات کلی: شرح کار + مشخصات + تماس همه توی یه کارت --}}
            <div class="card-luxury p-3.5 space-y-3">
                <div>
                    <label class="block text-xs font-semibold font-medium text-dark-400 mb-1 text-right">شرح کار درخواستی</label>
                    <div class="w-full border border-dark-600/40 text-cream-100 rounded-lg p-2 text-sm min-h-[52px] whitespace-pre-wrap text-right resize-y overflow-auto">{{ $workrequest->work_description ?: '---' }}</div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 text-xs">
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">مدل دستگاه</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $workrequest->device_model ?: '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">سریال دستگاه</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $workrequest->serial_number ?: '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ درخواست</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $workrequest->request_date_jalali }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">نوع درخواست</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $types[$workrequest->request_type] ?? '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ثبت‌کننده</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $workrequest->user->name ?? '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">واحد درخواست‌کننده</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $workrequest->request_unit ?: '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره تماس</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium direction-ltr text-right truncate">{{ $workrequest->contact_phone ?: '---' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">مسئول پیگیری</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1.5 font-medium truncate text-right">{{ $workrequest->contact_person ?: '---' }}</div>
                    </div>
                </div>

                @if($workrequest->issue_description || $workrequest->workflow_description)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-1">
                    @if($workrequest->issue_description)
                    <div>
                        <label class="block text-xs font-semibold font-medium text-dark-400 mb-1 text-right">شرح ایراد اعلامی</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs min-h-[44px] whitespace-pre-wrap text-right resize-y overflow-auto">{{ $workrequest->issue_description }}</div>
                    </div>
                    @endif
                    @if($workrequest->workflow_description)
                    <div>
                        <label class="block text-xs font-semibold font-medium text-dark-400 mb-1 text-right">شرح گردش کار</label>
                        <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs min-h-[44px] whitespace-pre-wrap text-right resize-y overflow-auto">{{ $workrequest->workflow_description }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- اطلاعات مالی --}}
            @if(auth()->user()->isCEO() || auth()->user()->isReception())
            <div class="card-luxury p-3.5">
                <h3 class="text-sm font-bold text-cream-100 mb-2.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    اطلاعات مالی
                </h3>
                <form action="{{ route('workrequests.financial', $workrequest) }}" method="POST" class="space-y-2.5">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">برآورد اولیه (ریال)</label>
                            <input type="text" name="estimated_cost"
                                value="{{ old('estimated_cost') ? number_format(old('estimated_cost')) : ($workrequest->estimated_cost ? number_format($workrequest->estimated_cost) : '') }}"
                                class="money-input input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">نتیجه قیمت اولیه</label>
                            <div class="relative">
                                <input type="text" name="initial_price_result"
                                    value="{{ old('initial_price_result', $workrequest->initial_price_result) }}"
                                    id="initial_price_result"
                                    class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="قبول / رد / انتظار"
                                    autocomplete="off" readonly
                                    onfocus="this.removeAttribute('readonly')">
                                <div id="price_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl" style="background:#fff">
                                    <div class="p-1">
                                        <div onclick="selectPriceResult('قبول')" class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">قبول</div>
                                        <div onclick="selectPriceResult('رد')" class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">رد</div>
                                        <div onclick="selectPriceResult('در انتظار')" class="px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">در انتظار</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">هزینه نهایی (ریال)</label>
                            <input type="text" name="final_cost"
                                value="{{ old('final_cost') ? number_format(old('final_cost')) : ($workrequest->final_cost ? number_format($workrequest->final_cost) : '') }}"
                                class="money-input input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">وضعیت پرداخت</label>
                            <select name="payment_status" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                                <option value="">انتخاب کنید</option>
                                <option value="credit" {{ old('payment_status', $workrequest->payment_status) == 'credit' ? 'selected' : '' }}>اعتباری</option>
                                <option value="cash" {{ old('payment_status', $workrequest->payment_status) == 'cash' ? 'selected' : '' }}>نقدی</option>
                                <option value="documents" {{ old('payment_status', $workrequest->payment_status) == 'documents' ? 'selected' : '' }}>اسنادی</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">شماره فاکتور</label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number', $workrequest->invoice_number) }}" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">نام بانک</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $workrequest->bank_name) }}" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">تاریخ پرداخت بانک</label>
                            <input type="text" name="bank_payment_date"
                                value="{{ old('bank_payment_date', $workrequest->bank_payment_date ? toJalali($workrequest->bank_payment_date) : '') }}"
                                class="jalali-datepicker input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="۱۴۰۳/۱۱/۲۸">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">مبلغ پرداخت بانک (ریال)</label>
                            <input type="text" name="bank_payment_amount"
                                value="{{ old('bank_payment_amount') ? number_format(old('bank_payment_amount')) : ($workrequest->bank_payment_amount ? number_format($workrequest->bank_payment_amount) : '') }}"
                                class="money-input input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">سند حسابداری</label>
                            <input type="text" name="accounting_document" value="{{ old('accounting_document', $workrequest->accounting_document) }}" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">سند دریافت</label>
                            <input type="text" name="receipt_document" value="{{ old('receipt_document', $workrequest->receipt_document) }}" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                        </div>
                    </div>
                    <div class="flex justify-end pt-1">
                        <button type="submit" class="btn-primary !py-1.5 !px-4 text-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            ذخیره اطلاعات مالی
                        </button>
                    </div>
                </form>
            </div>
            @elseif($workrequest->estimated_cost || $workrequest->final_cost || $workrequest->payment_status || $workrequest->initial_price_result || $workrequest->invoice_number || $workrequest->bank_name || $workrequest->bank_payment_date || $workrequest->bank_payment_amount || $workrequest->accounting_document || $workrequest->receipt_document)
            {{-- نمایش فقط‌خواندنی برای سایر نقش‌ها --}}
            <div class="card-luxury p-3.5">
                <h3 class="text-sm font-bold text-cream-100 mb-2.5">اطلاعات مالی</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 text-xs">
                    @if($workrequest->estimated_cost)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">برآورد اولیه</label>
                        <div class="bg-dark-900/50 text-yellow-400 font-bold rounded-lg px-2 py-1.5">{{ number_format($workrequest->estimated_cost) }} ریال</div>
                    </div>
                    @endif
                    @if($workrequest->final_cost)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">هزینه نهایی</label>
                        <div class="bg-dark-900/50 text-green-400 font-bold rounded-lg px-2 py-1.5">{{ number_format($workrequest->final_cost) }} ریال</div>
                    </div>
                    @endif
                    @if($workrequest->payment_status)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">وضعیت پرداخت</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5">
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
                        <label class="block text-[10px] text-dark-400 mb-0.5">نتیجه قیمت اولیه</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5">{{ $workrequest->initial_price_result }}</div>
                    </div>
                    @endif
                    @if($workrequest->invoice_number)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">شماره فاکتور</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5">{{ $workrequest->invoice_number }}</div>
                    </div>
                    @endif
                    @if($workrequest->bank_name)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">نام بانک</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5">{{ $workrequest->bank_name }}</div>
                    </div>
                    @endif
                    @if($workrequest->bank_payment_date)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">تاریخ پرداخت بانک</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5">{{ toJalali($workrequest->bank_payment_date) }}</div>
                    </div>
                    @endif
                    @if($workrequest->bank_payment_amount)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">مبلغ پرداخت بانک</label>
                        <div class="bg-dark-900/50 text-cream-100 font-bold rounded-lg px-2 py-1.5">{{ number_format($workrequest->bank_payment_amount) }} ریال</div>
                    </div>
                    @endif
                    @if($workrequest->accounting_document)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">سند حسابداری</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5 truncate">{{ $workrequest->accounting_document }}</div>
                    </div>
                    @endif
                    @if($workrequest->receipt_document)
                    <div>
                        <label class="block text-[10px] text-dark-400 mb-0.5">سند دریافت</label>
                        <div class="bg-dark-900/50 text-cream-100 rounded-lg px-2 py-1.5 truncate">{{ $workrequest->receipt_document }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- آخرین تغییر --}}
            @if($workrequest->last_action_at)
            <div class="text-[11px] text-dark-400 flex items-center gap-1.5 px-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>آخرین تغییر توسط <span class="text-cream-200 font-medium">{{ $workrequest->lastActionBy->name ?? 'سیستم' }}</span> — {{ $workrequest->last_action_at->format('Y-m-d H:i') }}</span>
            </div>
            @endif

            {{-- ═══ بخش‌های جمع‌وبازشو: پیوست‌ها / مراحل کار / نظرات ═══ --}}

            {{-- پیوست‌ها --}}
            <details class="card-luxury p-3 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                        </svg>
                        پیوست‌ها
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-3 mt-2 border-t border-dark-700">
                    <x-attachments.panel :model="$workrequest" mode="show" />
                </div>
            </details>

            {{-- مراحل کار --}}
            <details class="card-luxury p-3 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        مراحل کار
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-3 mt-2 border-t border-dark-700">
                    @include('workrequests.partials._stages', ['workrequest' => $workrequest])
                </div>
            </details>

            {{-- نظرات و مکالمات --}}
            <details class="card-luxury p-3 group">
                <summary class="flex items-center justify-between gap-1.5 cursor-pointer select-none list-none [&::-webkit-details-marker]:hidden">
                    <span class="text-sm font-bold text-cream-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        نظرات و مکالمات
                    </span>
                    <svg class="w-4 h-4 text-dark-400 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pt-3 mt-2 border-t border-dark-700">
                    <x-comments-section :reportable="$workrequest" reportableType="App\Models\WorkRequest" />
                </div>
            </details>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatNumber(val) {
                val = String(val).replace(/[^0-9]/g, '');
                return val ? parseInt(val).toLocaleString('en-US') : '';
            }

            document.querySelectorAll('.money-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    const raw = this.value.replace(/,/g, '');
                    if (!raw) {
                        this.value = '';
                        return;
                    }
                    const cursor = this.selectionStart;
                    const prevLen = this.value.length;
                    this.value = formatNumber(raw);
                    const diff = this.value.length - prevLen;
                    this.setSelectionRange(cursor + diff, cursor + diff);
                });
            });

            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function() {
                    form.querySelectorAll('.money-input').forEach(function(input) {
                        input.value = input.value.replace(/,/g, '');
                    });
                });
            });
        });
    </script>
    <script>
        const priceInput = document.getElementById('initial_price_result');
        const priceDropdown = document.getElementById('price_dropdown');
        if (priceInput && priceDropdown) {
            priceInput.addEventListener('focus', () => priceDropdown.classList.remove('hidden'));
            priceInput.addEventListener('blur', () => setTimeout(() => priceDropdown.classList.add('hidden'), 200));
        }
    </script>
    <script>
        function selectPriceResult(val) {
            document.getElementById('initial_price_result').value = val;
            document.getElementById('price_dropdown').classList.add('hidden');
        }
    </script>
</x-app-layout>