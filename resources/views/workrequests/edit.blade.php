<x-app-layout>
    <div class="py-4 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-3">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2.5 flex items-center gap-3">
                <a href="{{ route('workrequests.show', $workrequest) }}" class="p-1.5 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-5 h-5 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-base font-bold text-cream-100 truncate">
                        ویرایش گردش کار — {{ $workrequest->request_number }}
                    </h1>
                </div>
            </div>

            <form action="{{ route('workrequests.update', $workrequest) }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                {{-- اطلاعات اصلی + شرح کار (ادغام‌شده مثل create) --}}
                <div class="card-luxury p-3.5 space-y-3">

                    {{-- ردیف‌های فیلدهای کوچک --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">مدل *</label>
                            <input type="text" name="device_model" required
                                   value="{{ old('device_model', $workrequest->device_model) }}"
                                   class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-primary-500/50 text-right"
                                   placeholder="FARAN">
                            @error('device_model')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">تاریخ درخواست/ورود *</label>
                            <input type="text" name="request_date" required
                                   value="{{ old('request_date', $workrequest->request_date_jalali) }}"
                                   class="jalali-datepicker w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-primary-500/50"
                                   placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('request_date')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شماره درخواست *</label>
                            <input type="text" name="request_number" required
                                   value="{{ old('request_number', $workrequest->request_number) }}"
                                   class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg px-2 py-1.5 text-xs font-bold focus:ring-2 focus:ring-primary-500/50 text-right"
                                   placeholder="2960">
                            @error('request_number')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شماره سریال دستگاه *</label>
                            <input type="text" name="serial_number" required
                                   value="{{ old('serial_number', $workrequest->serial_number) }}"
                                   class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="2960">
                            @error('serial_number')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">نوع درخواست *</label>
                            <select name="request_type" required class="input-luxury w-full !py-1.5 !px-2 text-xs">
                                <option value="">تعمیرات / ساخت / سرویس و نصب / فروش</option>
                                <option value="repair"  {{ old('request_type', $workrequest->request_type) == 'repair' ? 'selected' : '' }}>🔧 تعمیرات</option>
                                <option value="service" {{ old('request_type', $workrequest->request_type) == 'service' ? 'selected' : '' }}>⚙️ سرویس و نصب</option>
                                <option value="install" {{ old('request_type', $workrequest->request_type) == 'install' ? 'selected' : '' }}>🔌 ساخت</option>
                                <option value="sale"    {{ old('request_type', $workrequest->request_type) == 'sale' ? 'selected' : '' }}>💰 فروش</option>
                            </select>
                            @error('request_type')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">واحد درخواست کننده *</label>
                            <div class="relative">
                                <input type="text" name="request_unit" required
                                       value="{{ old('request_unit', $workrequest->request_unit) }}"
                                       id="request_unit_input"
                                       class="input-luxury w-full !py-1.5 !px-2 text-xs"
                                       placeholder="شرکت اکسین ساحل خوزستان - آقای زارع زاده"
                                       autocomplete="off">
                                <div id="units_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="units_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('request_unit')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شماره تماس *</label>
                            <div class="relative">
                                <input type="text" name="contact_phone" required
                                       value="{{ old('contact_phone', $workrequest->contact_phone) }}"
                                       id="contact_phone_input"
                                       class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="09177696112"
                                       autocomplete="off">
                                <div id="phones_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="phones_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('contact_phone')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">مسئول پیگیری درخواست *</label>
                            <div class="relative">
                                <input type="text" name="contact_person" required
                                       value="{{ old('contact_person', $workrequest->contact_person) }}"
                                       id="contact_person_input"
                                       class="input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="خانم کجباف"
                                       autocomplete="off">
                                <div id="persons_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="persons_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('contact_person')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- شرح کار (سه‌ستونه، داخل همین کارت مثل create) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 pt-2 border-t border-dark-700/60">
                        <div>
                            <label class="block text-xs font-semibold font-semibold text-dark-400 mb-1 text-right">شرح کار درخواستی *</label>
                            <textarea name="work_description" rows="2" required
                                      class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                      placeholder="درخواست سرویس دوره ای 3 دستگاه یو پی اس 80KVA...">{{ old('work_description', $workrequest->work_description) }}</textarea>
                            @error('work_description')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح ایراد اعلامی</label>
                            <textarea name="issue_description" rows="2"
                                      class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                      placeholder="شرح ایراد و مشکلات اعلام شده...">{{ old('issue_description', $workrequest->issue_description) }}</textarea>
                            @error('issue_description')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1 text-right">شرح گردش کار</label>
                            <textarea name="workflow_description" rows="3"
                                      class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                      placeholder="شرح کامل مراحل انجام کار...">{{ old('workflow_description', $workrequest->workflow_description) }}</textarea>
                            @error('workflow_description')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- اطلاعات مالی (بدون تغییر) --}}
                <div class="card-luxury p-3.5 space-y-3">
                    <h3 class="text-sm font-bold text-cream-100">اطلاعات مالی</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">برآورد اولیه (ریال)</label>
                            <input type="text" name="estimated_cost"
                                   value="{{ old('estimated_cost') ? number_format(old('estimated_cost')) : ($workrequest->estimated_cost ? number_format($workrequest->estimated_cost) : '') }}"
                                   class="money-input input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="0">
                            @error('estimated_cost')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">نتیجه قیمت اولیه</label>
                            <div class="relative">
                                <input type="text" name="initial_price_result"
                                       value="{{ old('initial_price_result', $workrequest->initial_price_result) }}"
                                       id="initial_price_result"
                                       class="input-luxury w-full !py-1.5 !px-2 text-xs cursor-pointer"
                                       placeholder="قبول / رد / انتظار" autocomplete="off" readonly>
                                <div id="price_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-xl border-2 border-stone-200 shadow-xl" style="background:#fff">
                                    <div class="p-1">
                                        <div data-value="قبول" class="price-option px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">قبول</div>
                                        <div data-value="رد" class="price-option px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">رد</div>
                                        <div data-value="در انتظار" class="price-option px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">در انتظار</div>
                                    </div>
                                </div>
                            </div>
                            @error('initial_price_result')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">هزینه نهایی (ریال)</label>
                            <input type="text" name="final_cost"
                                   value="{{ old('final_cost') ? number_format(old('final_cost')) : ($workrequest->final_cost ? number_format($workrequest->final_cost) : '') }}"
                                   class="money-input input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="0">
                            @error('final_cost')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">وضعیت پرداخت</label>
                            <select name="payment_status" class="input-luxury w-full !py-1.5 !px-2 text-xs">
                                <option value="">انتخاب کنید</option>
                                <option value="credit"    {{ old('payment_status', $workrequest->payment_status) == 'credit' ? 'selected' : '' }}>اعتباری</option>
                                <option value="cash"      {{ old('payment_status', $workrequest->payment_status) == 'cash' ? 'selected' : '' }}>نقدی</option>
                                <option value="documents" {{ old('payment_status', $workrequest->payment_status) == 'documents' ? 'selected' : '' }}>اسنادی</option>
                            </select>
                            @error('payment_status')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">شماره فاکتور</label>
                            <input type="text" name="invoice_number"
                                   value="{{ old('invoice_number', $workrequest->invoice_number) }}"
                                   class="input-luxury w-full !py-1.5 !px-2 text-xs">
                            @error('invoice_number')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">نام بانک</label>
                            <input type="text" name="bank_name"
                                   value="{{ old('bank_name', $workrequest->bank_name) }}"
                                   class="input-luxury w-full !py-1.5 !px-2 text-xs">
                            @error('bank_name')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">تاریخ پرداخت بانک</label>
                            <input type="text" name="bank_payment_date"
                                   value="{{ old('bank_payment_date', $workrequest->bank_payment_date ? toJalali($workrequest->bank_payment_date) : '') }}"
                                   class="jalali-datepicker input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('bank_payment_date')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">مبلغ پرداخت بانک (ریال)</label>
                            <input type="text" name="bank_payment_amount"
                                   value="{{ old('bank_payment_amount') ? number_format(old('bank_payment_amount')) : ($workrequest->bank_payment_amount ? number_format($workrequest->bank_payment_amount) : '') }}"
                                   class="money-input input-luxury w-full !py-1.5 !px-2 text-xs" placeholder="0">
                            @error('bank_payment_amount')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">سند حسابداری</label>
                            <input type="text" name="accounting_document"
                                   value="{{ old('accounting_document', $workrequest->accounting_document) }}"
                                   class="input-luxury w-full !py-1.5 !px-2 text-xs">
                            @error('accounting_document')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-400 mb-1">سند دریافت</label>
                            <input type="text" name="receipt_document"
                                   value="{{ old('receipt_document', $workrequest->receipt_document) }}"
                                   class="input-luxury w-full !py-1.5 !px-2 text-xs">
                            @error('receipt_document')
                            <p class="text-red-400 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- دکمه‌ها --}}
                <div class="flex flex-col sm:flex-row gap-2.5 justify-end">
                    <a href="{{ route('workrequests.show', $workrequest) }}" class="btn-secondary !py-2 !px-4 text-sm text-center">
                        انصراف
                    </a>
                    <button type="submit" class="btn-primary !py-2 !px-4 text-sm inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ذخیره تغییرات
                    </button>
                </div>
            </form>

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
                    <x-attachments.panel :model="$workrequest" mode="edit" />
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

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const previousContacts = @json($previousContacts);

            function makeDropdown(inputEl, dropdownEl, listEl, getLabel, onSelect) {
                function renderList(filter) {
                    const items = previousContacts
                        .map(c => getLabel(c))
                        .filter((v, i, a) => v && a.indexOf(v) === i)
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
                inputEl.addEventListener('blur', () => dropdownEl.classList.add('hidden'));
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

            const priceInput = document.getElementById('initial_price_result');
            const priceDropdown = document.getElementById('price_dropdown');

            if (priceInput && priceDropdown) {
                priceInput.addEventListener('focus', () => priceDropdown.classList.remove('hidden'));
                priceInput.addEventListener('blur', () => priceDropdown.classList.add('hidden'));

                document.querySelectorAll('.price-option').forEach(option => {
                    option.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        priceInput.value = this.getAttribute('data-value');
                        priceDropdown.classList.add('hidden');
                    });
                });
            }
        });
    </script>
</x-app-layout>