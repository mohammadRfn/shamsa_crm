<x-app-layout>
    <div class="py-2 px-3 sm:px-4 lg:px-5">
        <div class="max-w-6xl mx-auto space-y-2">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2 flex items-center gap-2">
                <a href="{{ route('workrequests.index') }}" class="p-1 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-4 h-4 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-cream-100 truncate">فرم گردش کار گروه مهندسی شمسا الکترونیک — ثبت درخواست جدید</h1>
                </div>
            </div>

            <form action="{{ route('workrequests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                @csrf

                {{-- اطلاعات اصلی + شرح کار (ادغام شده) --}}
                <div class="card-luxury p-3 space-y-2.5">
                    
                    {{-- فیلدهای کوچک (۴ ستونه) --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">مدل *</label>
                            <input type="text" name="device_model" required value="{{ old('device_model') }}"
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-primary-500/50 text-right"
                                placeholder="FARAN">
                            @error('device_model') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">تاریخ درخواست/ورود *</label>
                            <input type="text" name="request_date" required value="{{ old('request_date', jalaliToday()) }}"
                                class="jalali-datepicker w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg px-2 py-1 text-xs focus:ring-2 focus:ring-primary-500/50"
                                placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('request_date') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شماره درخواست *</label>
                            <input type="text" name="request_number" required value="{{ old('request_number') }}"
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg px-2 py-1 text-xs font-bold focus:ring-2 focus:ring-primary-500/50 text-right"
                                placeholder="2960">
                            @error('request_number') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شماره سریال دستگاه *</label>
                            <input type="text" name="serial_number" required value="{{ old('serial_number') }}"
                                class="input-luxury w-full !py-1 !px-2 text-xs" placeholder="2960">
                            @error('serial_number') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">نوع درخواست *</label>
                            <select name="request_type" required class="input-luxury w-full !py-1 !px-2 text-xs">
                                <option value="">انتخاب کنید...</option>
                                <option value="repair" {{ old('request_type') == 'repair' ? 'selected' : '' }}>🔧 تعمیرات</option>
                                <option value="service" {{ old('request_type') == 'service' ? 'selected' : '' }}>⚙️ سرویس و نصب</option>
                                <option value="install" {{ old('request_type') == 'install' ? 'selected' : '' }}>🔌 ساخت</option>
                                <option value="sale" {{ old('request_type') == 'sale' ? 'selected' : '' }}>💰 فروش</option>
                            </select>
                            @error('request_type') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">واحد درخواست کننده *</label>
                            <div class="relative">
                                <input type="text" name="request_unit" required value="{{ old('request_unit') }}"
                                    id="request_unit_input"
                                    class="input-luxury w-full !py-1 !px-2 text-xs" placeholder="شرکت اکسین..."
                                    autocomplete="off">
                                <div id="units_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-lg border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="units_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('request_unit') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شماره تماس *</label>
                            <div class="relative">
                                <input type="text" name="contact_phone" required value="{{ old('contact_phone') }}"
                                    id="contact_phone_input"
                                    class="input-luxury w-full !py-1 !px-2 text-xs" placeholder="09177696112"
                                    autocomplete="off">
                                <div id="phones_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-lg border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="phones_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('contact_phone') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">مسئول پیگیری *</label>
                            <div class="relative">
                                <input type="text" name="contact_person" required value="{{ old('contact_person') }}"
                                    id="contact_person_input"
                                    class="input-luxury w-full !py-1 !px-2 text-xs" placeholder="نام مسئول..."
                                    autocomplete="off">
                                <div id="persons_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-lg border-2 border-stone-200 shadow-xl overflow-y-auto max-h-48" style="background:#fff">
                                    <div id="persons_dropdown_list" class="p-1"></div>
                                </div>
                            </div>
                            @error('contact_person') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- فیلدهای متنی بزرگ (۳ ستونه) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pt-1 border-t border-dark-600/30">
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شرح کار درخواستی *</label>
                            <textarea name="work_description" rows="2" required
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                placeholder="درخواست سرویس دوره‌ای...">{{ old('work_description') }}</textarea>
                            @error('work_description') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شرح ایراد اعلامی</label>
                            <textarea name="issue_description" rows="2"
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                placeholder="شرح ایراد و مشکلات...">{{ old('issue_description') }}</textarea>
                            @error('issue_description') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شرح گردش کار</label>
                            <textarea name="workflow_description" rows="2"
                                class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right"
                                placeholder="شرح کامل مراحل انجام کار...">{{ old('workflow_description') }}</textarea>
                            @error('workflow_description') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- اطلاعات مالی --}}
                <div class="card-luxury p-3 space-y-2">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">برآورد اولیه (ریال)</label>
                            <input type="text" name="estimated_cost"
                                value="{{ old('estimated_cost') ? number_format(old('estimated_cost')) : '' }}"
                                class="money-input input-luxury w-full !py-1 !px-2 text-xs" placeholder="0">
                            @error('estimated_cost') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">نتیجه قیمت اولیه</label>
                            <div class="relative">
                                <input type="text" name="initial_price_result" value="{{ old('initial_price_result') }}"
                                    id="initial_price_result"
                                    class="input-luxury w-full !py-1 !px-2 text-xs" placeholder="قبول / رد / انتظار" autocomplete="off">
                                <div id="price_dropdown" class="hidden absolute z-50 w-full mt-1 rounded-lg border-2 border-stone-200 shadow-xl" style="background:#fff">
                                    <div class="p-1">
                                        <div onclick="selectPriceResult('قبول')" class="px-2 py-1.5 rounded text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">قبول</div>
                                        <div onclick="selectPriceResult('رد')" class="px-2 py-1.5 rounded text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">رد</div>
                                        <div onclick="selectPriceResult('در انتظار')" class="px-2 py-1.5 rounded text-xs font-medium cursor-pointer hover:bg-stone-100" style="color:#1C1A18">در انتظار</div>
                                    </div>
                                </div>
                            </div>
                            @error('initial_price_result') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">هزینه نهایی (ریال)</label>
                            <input type="text" name="final_cost"
                                value="{{ old('final_cost') ? number_format(old('final_cost')) : '' }}"
                                class="money-input input-luxury w-full !py-1 !px-2 text-xs" placeholder="0">
                            @error('final_cost') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">وضعیت پرداخت</label>
                            <select name="payment_status" class="input-luxury w-full !py-1 !px-2 text-xs">
                                <option value="">انتخاب کنید</option>
                                <option value="credit" {{ old('payment_status') == 'credit' ? 'selected' : '' }}>اعتباری</option>
                                <option value="cash" {{ old('payment_status') == 'cash' ? 'selected' : '' }}>نقدی</option>
                                <option value="documents" {{ old('payment_status') == 'documents' ? 'selected' : '' }}>اسنادی</option>
                            </select>
                            @error('payment_status') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">شماره فاکتور</label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number') }}"
                                class="input-luxury w-full !py-1 !px-2 text-xs">
                            @error('invoice_number') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">نام بانک</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                                class="input-luxury w-full !py-1 !px-2 text-xs">
                            @error('bank_name') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">تاریخ پرداخت بانک</label>
                            <input type="text" name="bank_payment_date"
                                value="{{ old('bank_payment_date', ($workrequest->bank_payment_date ?? null) ? toJalali($workrequest->bank_payment_date) : '') }}"
                                class="jalali-datepicker input-luxury w-full !py-1 !px-2 text-xs" placeholder="۱۴۰۳/۱۱/۲۸">
                            @error('bank_payment_date') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">مبلغ پرداخت بانک (ریال)</label>
                            <input type="text" name="bank_payment_amount"
                                value="{{ old('bank_payment_amount') ? number_format(old('bank_payment_amount')) : '' }}"
                                class="money-input input-luxury w-full !py-1 !px-2 text-xs" placeholder="0">
                            @error('bank_payment_amount') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- اسناد در یک ستون کامل‌تر یا در ادامه --}}
                        <div class="col-span-2 md:col-span-2">
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">سند حسابداری</label>
                            <input type="text" name="accounting_document" value="{{ old('accounting_document') }}"
                                class="input-luxury w-full !py-1 !px-2 text-xs">
                            @error('accounting_document') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-span-2 md:col-span-2">
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5">سند دریافت</label>
                            <input type="text" name="receipt_document" value="{{ old('receipt_document') }}"
                                class="input-luxury w-full !py-1 !px-2 text-xs">
                            @error('receipt_document') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- فایل‌های ضمیمه اختیاری (Collapsible / آکاردئون) --}}
                <details class="card-luxury group">
                    <summary class="p-2.5 cursor-pointer font-bold text-sm text-cream-100 flex justify-between items-center list-none [&::-webkit-details-marker]:hidden">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            فایل‌های ضمیمه (اختیاری)
                        </span>
                        <svg class="w-4 h-4 text-dark-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <div class="p-3 pt-0 border-t border-dark-600/40 mt-1 space-y-2">
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

                {{-- دکمه‌ها --}}
                <div class="flex gap-2 justify-end pt-1">
                    <a href="{{ route('workrequests.index') }}" class="btn-secondary !py-1.5 !px-4 text-sm text-center">
                        انصراف
                    </a>
                    <button type="submit" class="btn-primary !py-1.5 !px-5 text-sm inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ثبت درخواست
                    </button>
                </div>
            </form>

        </div>
    </div>

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
                    div.className = 'px-3 py-1.5 rounded text-xs font-medium cursor-pointer hover:bg-stone-100';
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

        function selectPriceResult(val) {
            priceInput.value = val;
            priceDropdown.classList.add('hidden');
        }
    </script>
</x-app-layout>