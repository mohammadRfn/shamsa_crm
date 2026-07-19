<x-app-layout>{{-- resources/views/tasks/create.blade.php --}}
    <div class="py-4 px-3 sm:px-4 lg:px-5">
        <div class="max-w-3xl mx-auto space-y-2">

            {{-- ═══ هدر فشرده ═══ --}}
            <div class="card-luxury p-2 flex items-center gap-2">
                <a href="{{ route('tasks.index') }}" class="p-1 hover:bg-dark-700/70 rounded-lg transition-all border border-transparent hover:border-dark-600 shrink-0">
                    <svg class="w-4 h-4 text-cream-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-sm font-bold text-cream-100 truncate">ارسال تسک به تعمیرکار</h1>
                </div>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-2">
                @csrf

                {{-- درخواست کار + تخصیص (ادغام شده) --}}
                <div class="card-luxury p-3 space-y-2.5">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">شماره درخواست <span class="text-primary-400">*</span></label>
                            <select name="work_request_id" id="work_request_id" required
                                class="input-luxury w-full !py-1 !px-2 text-xs"
                                onchange="fillWorkRequestInfo(this)">
                                <option value="">انتخاب کنید...</option>
                                @foreach($workRequests as $wr)
                                <option value="{{ $wr->id }}"
                                    data-number="{{ $wr->request_number }}"
                                    data-model="{{ $wr->device_model }}"
                                    data-equipment="{{ $wr->equipment_name }}"
                                    data-serial="{{ $wr->serial_number }}"
                                    data-date="{{ $wr->request_date_jalali }}"
                                    data-type="{{ $wr->request_type_label ?? $wr->request_type }}"
                                    data-creator="{{ $wr->user->name ?? '' }}"
                                    data-work="{{ $wr->work_description }}"
                                    data-issue="{{ $wr->issue_description }}"
                                    data-workflow="{{ $wr->workflow_description }}"
                                    {{ old('work_request_id', $workRequest?->id) == $wr->id ? 'selected' : '' }}>
                                    {{ $wr->request_number }} — {{ $wr->equipment_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('work_request_id') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">تعمیرکار <span class="text-primary-400">*</span></label>
                            <select name="assigned_to" required class="input-luxury w-full !py-1 !px-2 text-xs">
                                <option value="">انتخاب تعمیرکار...</option>
                                @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ old('assigned_to') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_to') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-1 border-t border-dark-600/30">
                        <label class="block text-xs font-semibold text-dark-300 mb-0.5 text-right">یادداشت (اختیاری)</label>
                        <textarea name="note" rows="2"
                            placeholder="توضیح اضافه یا اولویت‌بندی..."
                            class="w-full border border-dark-600/40 bg-transparent text-cream-100 rounded-lg p-2 text-xs focus:ring-2 focus:ring-primary-500/50 resize-y overflow-auto text-right">{{ old('note') }}</textarea>
                    </div>

                    {{-- پیش‌نمایش اطلاعات ورک ریکوست --}}
                    <div id="wr-preview" class="hidden pt-1 border-t border-dark-600/30">
                        <p class="text-[10px] text-dark-400 mb-1.5">اطلاعاتی که برای تعمیرکار ارسال می‌شود:</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs mb-2">
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره درخواست</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right" id="prev-number"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">تاریخ درخواست</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right" id="prev-date"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">مدل دستگاه</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right" id="prev-model"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شماره سریال</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right" id="prev-serial"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">نوع درخواست</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right" id="prev-type"></div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">ثبت‌کننده</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg px-2 py-1 font-medium truncate text-right" id="prev-creator"></div>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <div>
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شرح کار درخواستی</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs whitespace-pre-wrap text-right" id="prev-work"></div>
                            </div>
                            <div id="prev-issue-wrap">
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شرح ایراد اعلامی</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs whitespace-pre-wrap text-right" id="prev-issue"></div>
                            </div>
                            <div id="prev-workflow-wrap">
                                <label class="block text-xs font-semibold text-dark-400 mb-0.5 text-right">شرح گردش کار</label>
                                <div class="border border-dark-600/40 text-cream-100 rounded-lg p-2 text-xs whitespace-pre-wrap text-right" id="prev-workflow"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- دکمه‌ها --}}
                <div class="flex gap-2 justify-end pt-1">
                    <a href="{{ route('tasks.index') }}" class="btn-secondary !py-1.5 !px-4 text-sm text-center">انصراف</a>
                    <button type="submit" class="btn-primary !py-1.5 !px-5 text-sm inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        ارسال تسک به تعمیرکار
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function fillWorkRequestInfo(select) {
            const opt = select.options[select.selectedIndex];
            const preview = document.getElementById('wr-preview');

            if (!opt.value) {
                preview.classList.add('hidden');
                return;
            }

            document.getElementById('prev-number').textContent = opt.dataset.number || '---';
            document.getElementById('prev-date').textContent = opt.dataset.date || '---';
            document.getElementById('prev-model').textContent = opt.dataset.model || '---';
            document.getElementById('prev-serial').textContent = opt.dataset.serial || '---';
            document.getElementById('prev-type').textContent = opt.dataset.type || '---';
            document.getElementById('prev-creator').textContent = opt.dataset.creator || '---';
            document.getElementById('prev-work').textContent = opt.dataset.work || '---';
            document.getElementById('prev-issue').textContent = opt.dataset.issue || '---';
            document.getElementById('prev-workflow').textContent = opt.dataset.workflow || '---';

            document.getElementById('prev-issue-wrap').classList.toggle('hidden', !opt.dataset.issue);
            document.getElementById('prev-workflow-wrap').classList.toggle('hidden', !opt.dataset.workflow);

            preview.classList.remove('hidden');
        }

        // اگه از قبل یه درخواست انتخاب شده (پارامتر URL)
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('work_request_id');
            if (sel.value) fillWorkRequestInfo(sel);
        });
    </script>
</x-app-layout>