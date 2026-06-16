<x-app-layout>{{-- resources/views/tasks/create.blade.php --}}
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">

            {{-- Header --}}
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                    ارسال تسک به تعمیرکار
                </h1>
                <p class="text-dark-400 mt-1">انتخاب درخواست کار و تخصیص به تعمیرکار</p>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" class="space-y-6">
                @csrf

                {{-- انتخاب شماره درخواست --}}
                <div class="card-luxury p-6 space-y-4">
                    <h2 class="text-lg font-bold text-cream-100 border-b-2 divider pb-3">درخواست کار</h2>

                    <div>
                        <label class="block text-sm text-dark-400 mb-1">شماره درخواست</label>
                        <select name="work_request_id" id="work_request_id"
                            class="input-luxury w-full" required
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
                        @error('work_request_id')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- پیش‌نمایش اطلاعات ورک ریکوست --}}
                    <div id="wr-preview" class="section-inner hidden">
                        <p class="text-xs text-dark-400 mb-3">اطلاعاتی که برای تعمیرکار ارسال می‌شود:</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-dark-400">شماره درخواست:</span>
                                <span id="prev-number" class="text-cream-100 font-medium mr-1"></span>
                            </div>
                            <div>
                                <span class="text-dark-400">تاریخ درخواست:</span>
                                <span id="prev-date" class="text-cream-100 font-medium mr-1"></span>
                            </div>
                            <div>
                                <span class="text-dark-400">مدل دستگاه:</span>
                                <span id="prev-model" class="text-cream-100 font-medium mr-1"></span>
                            </div>
                            <div>
                                <span class="text-dark-400">شماره سریال:</span>
                                <span id="prev-serial" class="text-cream-100 font-medium mr-1"></span>
                            </div>
                            <div>
                                <span class="text-dark-400">نوع درخواست:</span>
                                <span id="prev-type" class="text-cream-100 font-medium mr-1"></span>
                            </div>
                            <div>
                                <span class="text-dark-400">ثبت‌کننده:</span>
                                <span id="prev-creator" class="text-cream-100 font-medium mr-1"></span>
                            </div>
                        </div>
                        <div class="mt-3 space-y-2">
                            <div>
                                <span class="text-xs text-dark-400">شرح کار درخواستی:</span>
                                <p id="prev-work" class="text-cream-200 text-sm mt-0.5 bg-dark-800 rounded p-2"></p>
                            </div>
                            <div id="prev-issue-wrap">
                                <span class="text-xs text-dark-400">شرح ایراد اعلامی:</span>
                                <p id="prev-issue" class="text-cream-200 text-sm mt-0.5 bg-dark-800 rounded p-2"></p>
                            </div>
                            <div id="prev-workflow-wrap">
                                <span class="text-xs text-dark-400">شرح گردش کار:</span>
                                <p id="prev-workflow" class="text-cream-200 text-sm mt-0.5 bg-dark-800 rounded p-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- انتخاب تعمیرکار + یادداشت --}}
                <div class="card-luxury p-6 space-y-4">
                    <h2 class="text-lg font-bold text-cream-100 border-b-2 divider pb-3">تخصیص</h2>

                    <div>
                        <label class="block text-sm text-dark-400 mb-1">تعمیرکار</label>
                        <select name="assigned_to" class="input-luxury w-full" required>
                            <option value="">انتخاب تعمیرکار...</option>
                            @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('assigned_to') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm text-dark-400 mb-1">یادداشت (اختیاری)</label>
                        <textarea name="note" rows="3"
                            placeholder="توضیح اضافه یا اولویت‌بندی..."
                            class="input-luxury w-full resize-none">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary flex-1">
                        ارسال تسک به تعمیرکار
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn-secondary px-6">انصراف</a>
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