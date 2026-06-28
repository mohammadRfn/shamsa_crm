<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">

            <!-- Header -->
            <div class="flex items-center gap-4">
                <a href="{{ route('workrequests.show', $workrequest) }}" class="p-2 hover:bg-dark-700/70 rounded-lg transition-all border-2 border-transparent hover:border-dark-600">
                    <svg class="w-6 h-6 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        ویرایش فرم گردش کار گروه مهندسی شمسا الکترونیک
                    </h1>
                    <p class="text-dark-400 mt-1">ویرایش درخواست</p>
                </div>
            </div>

            <form action="{{ route('workrequests.update', $workrequest) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

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
                                        <textarea name="work_description" rows="4" required
                                            class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 focus:ring-2 focus:ring-primary-500/50 resize-none"
                                            placeholder="درخواست سرویس دوره ای 3دستگاه یو پی اس 80KVA...">{{ old('work_description', $workrequest->work_description) }}</textarea>
                                        @error('work_description')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="border-2 border-dark-600 p-3">
                                        <input type="text" name="device_model" required
                                            value="{{ old('device_model', $workrequest->device_model) }}"
                                            class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 focus:ring-2 focus:ring-primary-500/50 text-center"
                                            placeholder="FARAN">
                                        @error('device_model')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="border-2 border-dark-600 p-3">
                                        <input type="text"
                                            name="request_date"
                                            required
                                            value="{{ old('request_date', $workrequest->request_date_jalali) }}"
                                            class="jalali-datepicker w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 focus:ring-2 focus:ring-primary-500/50"
                                            placeholder="۱۴۰۳/۱۱/۲۸">
                                        @error('request_date')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="border-2 border-dark-600 p-3">
                                        <input type="text" name="request_number" required
                                            value="{{ old('request_number', $workrequest->request_number) }}"
                                            class="w-full bg-dark-900/50 border-0 text-cream-100 rounded-lg p-2 focus:ring-2 focus:ring-primary-500/50 text-center font-bold text-lg"
                                            placeholder="2960">
                                        @error('request_number')
                                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                                        @enderror
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
                            <label class="block text-sm font-medium text-cream-200 mb-2">شماره سریال دستگاه *</label>
                            <input type="text" name="serial_number" required
                                value="{{ old('serial_number', $workrequest->serial_number) }}"
                                class="input-luxury w-full" placeholder="2960">
                            @error('serial_number')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">نوع درخواست *</label>
                            <select name="request_type" required class="input-luxury w-full">
                                <option value="">تعمیرات / ساخت / سرویس و نصب / فروش</option>
                                <option value="repair" {{ old('request_type', $workrequest->request_type) == 'repair' ? 'selected' : '' }}>🔧 تعمیرات</option>
                                <option value="service" {{ old('request_type', $workrequest->request_type) == 'service' ? 'selected' : '' }}>⚙️ سرویس و نصب</option>
                                <option value="install" {{ old('request_type', $workrequest->request_type) == 'install' ? 'selected' : '' }}>🔌 ساخت</option>
                                <option value="sale" {{ old('request_type', $workrequest->request_type) == 'sale' ? 'selected' : '' }}>💰 فروش</option>
                            </select>
                            @error('request_type')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-cream-200 mb-2">واحد درخواست کننده *</label>
                            <div class="relative">
                                <input type="text" name="request_unit" required value="{{ old('request_unit', $workrequest->request_unit) }}"
                                    id="request_unit_input"
                                    class="input-luxury w-full" placeholder="شرکت اکسین ساحل خوزستان - آقای زارع زاده"
                                    autocomplete="off">
                                <div id="units_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="units_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('request_unit')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
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
                            <label class="block text-sm font-medium text-cream-200 mb-2">شماره تماس *</label>
                            <div class="relative">
                                <input type="text" name="contact_phone" required value="{{ old('contact_phone', $workrequest->contact_phone) }}"
                                    id="contact_phone_input"
                                    class="input-luxury w-full" placeholder="09177696112"
                                    autocomplete="off">
                                <div id="phones_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="phones_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('contact_phone')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">مسئول پیگیری درخواست *</label>
                            <div class="relative">
                                <input type="text" name="contact_person" required value="{{ old('contact_person', $workrequest->contact_person) }}"
                                    id="contact_person_input"
                                    class="input-luxury w-full" placeholder="خانم کجباف"
                                    autocomplete="off">
                                <div id="persons_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="persons_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('contact_person')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
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
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شرح ایراد اعلامی</label>
                            <textarea name="issue_description" rows="3" class="input-luxury w-full resize-none"
                                placeholder="شرح ایراد و مشکلات اعلام شده...">{{ old('issue_description', $workrequest->issue_description) }}</textarea>
                            @error('issue_description')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6 border-t-2 border-dark-700/50">
                            <label class="block text-sm font-medium text-cream-200 mb-4">شرح گردش کار</label>
                            <textarea name="workflow_description" rows="6"
                                class="input-luxury w-full resize-none h-40"
                                placeholder="شرح کامل مراحل انجام کار...">{{ old('workflow_description', $workrequest->workflow_description) }}</textarea>
                            @error('workflow_description')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- اطلاعات مالی -->
                <div class="card-luxury p-6 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b-2 divider">
                        <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-cream-100">اطلاعات مالی</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">هزینه برآورد شده اولیه (ریال)</label>
                            <input type="text" name="estimated_cost"
                                value="{{ old('estimated_cost') ? number_format(old('estimated_cost')) : ($workrequest->estimated_cost ? number_format($workrequest->estimated_cost) : '') }}"
                                class="money-input input-luxury w-full" placeholder="0">
                            @error('estimated_cost')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">نتیجه اعلام قیمت اولیه</label>
                            <div class="relative">
                                <input type="text" name="initial_price_result"
                                    value="{{ old('initial_price_result', $workrequest->initial_price_result) }}"
                                    id="initial_price_result"
                                    class="input-luxury w-full" placeholder="قبول / رد / در انتظار"
                                    autocomplete="off" readonly
                                    onfocus="this.removeAttribute('readonly')">
                                <div id="price_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl" style="background:#fff">
                                    <div class="p-1">
                                        <div onclick="selectPriceResult('قبول')" class="px-4 py-2 rounded-lg text-sm font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">قبول</div>
                                        <div onclick="selectPriceResult('رد')" class="px-4 py-2 rounded-lg text-sm font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">رد</div>
                                        <div onclick="selectPriceResult('در انتظار')" class="px-4 py-2 rounded-lg text-sm font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">در انتظار</div>
                                    </div>
                                </div>
                            </div>
                            @error('initial_price_result')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">هزینه نهایی (ریال)</label>
                            <input type="text" name="final_cost"
                                value="{{ old('final_cost') ? number_format(old('final_cost')) : ($workrequest->final_cost ? number_format($workrequest->final_cost) : '') }}"
                                class="money-input input-luxury w-full" placeholder="0">
                            @error('final_cost')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">وضعیت پرداخت</label>
                            <select name="payment_status" class="input-luxury w-full">
                                <option value="">انتخاب کنید</option>
                                <option value="credit" {{ old('payment_status', $workrequest->payment_status) == 'credit' ? 'selected' : '' }}>اعتباری</option>
                                <option value="cash" {{ old('payment_status', $workrequest->payment_status) == 'cash' ? 'selected' : '' }}>نقدی</option>
                                <option value="documents" {{ old('payment_status', $workrequest->payment_status) == 'documents' ? 'selected' : '' }}>اسنادی</option>
                            </select>
                            @error('payment_status')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">شماره فاکتور</label>
                            <input type="text" name="invoice_number"
                                value="{{ old('invoice_number', $workrequest->invoice_number) }}"
                                class="input-luxury w-full">
                            @error('invoice_number')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">نام بانک</label>
                            <input type="text" name="bank_name"
                                value="{{ old('bank_name', $workrequest->bank_name) }}"
                                class="input-luxury w-full">
                            @error('bank_name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">تاریخ پرداخت بانک</label>
                            <input type="text" name="bank_payment_date"
                                value="{{ old('bank_payment_date', $workrequest->bank_payment_date ? toJalali($workrequest->bank_payment_date) : '') }}"
                                class="jalali-datepicker input-luxury w-full" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('bank_payment_date')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">مبلغ پرداخت بانک (ریال)</label>
                            <input type="text" name="bank_payment_amount"
                                value="{{ old('bank_payment_amount') ? number_format(old('bank_payment_amount')) : ($workrequest->bank_payment_amount ? number_format($workrequest->bank_payment_amount) : '') }}"
                                class="money-input input-luxury w-full" placeholder="0">
                            @error('bank_payment_amount')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">سند حسابداری</label>
                            <input type="text" name="accounting_document"
                                value="{{ old('accounting_document', $workrequest->accounting_document) }}"
                                class="input-luxury w-full">
                            @error('accounting_document')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-cream-200 mb-2">سند دریافت</label>
                            <input type="text" name="receipt_document"
                                value="{{ old('receipt_document', $workrequest->receipt_document) }}"
                                class="input-luxury w-full">
                            @error('receipt_document')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- دکمه‌ها -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="{{ route('workrequests.show', $workrequest) }}" class="btn-secondary text-center">
                        انصراف
                    </a>
                    <button type="submit" class="btn-primary inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ذخیره تغییرات
                    </button>
                </div>

            </form>
            <x-attachments.panel :model="$workrequest" mode="edit" />
            @include('workrequests.partials._stages', ['workrequest' => $workrequest])
        </div>
    </div>
    
    {{-- قبل از script، داخل body --}}
    <meta name="previous-contacts" content='{{ json_encode($previousContacts) }}'>
    <script>
        const previousContacts = JSON.parse(
            document.querySelector('meta[name="previous-contacts"]').getAttribute('content')
        );

        function makeDropdown(inputEl, dropdownEl, listEl, getLabel, onSelect) {
            function renderList(filter) {
                const items = previousContacts
                    .map(c => getLabel(c))
                    .filter((v, i, a) => v && a.indexOf(v) === i) // unique
                    .filter(v => !filter || v.includes(filter));

                listEl.innerHTML = '';
                items.forEach(val => {
                    const div = document.createElement('div');
                    div.className = 'px-4 py-2 rounded-lg text-sm font-medium cursor-pointer hover:bg-stone-100';
                    div.style.color = '#1C1A18';
                    div.textContent = val;
                    div.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        inputEl.value = val;
                        onSelect(val);
                        dropdownEl.classList.add('hidden');
                    });
                    listEl.appendChild(div);
                });

                if (items.length === 0) dropdownEl.classList.add('hidden');
                else dropdownEl.classList.remove('hidden');
            }

            inputEl.addEventListener('focus', () => renderList(''));
            inputEl.addEventListener('input', () => renderList(inputEl.value));
            inputEl.addEventListener('blur', () => setTimeout(() => dropdownEl.classList.add('hidden'), 200));
        }

        // واحد درخواست کننده
        makeDropdown(
            document.getElementById('request_unit_input'),
            document.getElementById('units_dropdown'),
            document.getElementById('units_dropdown_list'),
            c => c.request_unit,
            (val) => {
                const selected = previousContacts.find(c => c.request_unit === val);
                if (selected) {
                    document.getElementById('contact_person_input').value = selected.contact_person ?? '';
                    document.getElementById('contact_phone_input').value = selected.contact_phone ?? '';
                }
            }
        );

        // شماره تماس
        makeDropdown(
            document.getElementById('contact_phone_input'),
            document.getElementById('phones_dropdown'),
            document.getElementById('phones_dropdown_list'),
            c => c.contact_phone,
            () => {}
        );

        // مسئول پیگیری
        makeDropdown(
            document.getElementById('contact_person_input'),
            document.getElementById('persons_dropdown'),
            document.getElementById('persons_dropdown_list'),
            c => c.contact_person,
            () => {}
        );
    </script>
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

        priceInput.addEventListener('focus', () => priceDropdown.classList.remove('hidden'));
        priceInput.addEventListener('blur', () => setTimeout(() => priceDropdown.classList.add('hidden'), 200));
    </script>
    <script>
        function selectPriceResult(val) {
            document.getElementById('initial_price_result').value = val;
            document.getElementById('price_dropdown').classList.add('hidden');
        }
    </script>
    <script>
        console.log(document.querySelector('meta[name="previous-contacts"]'));
    </script>
</x-app-layout>