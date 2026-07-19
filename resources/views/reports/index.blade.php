<x-app-layout>

    @include('components._list-view-styles')

    <div class="py-5 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-3">

            <!-- Toolbar یکپارچه: ثبت گزارش + سرچ/فیلتر + سوییچ گرید/لیست همه توی یه ردیف -->
            <div class="card-luxury p-2.5">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
                    @if(auth()->user()->isTechnician())
                    <a href="{{ route('reports.create') }}" class="btn-primary inline-flex items-center justify-center gap-2 !py-2 !px-4 text-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        ثبت گزارش جدید
                    </a>
                    @endif

                    <form method="GET" action="{{ route('reports.index') }}" class="flex-1 flex flex-col lg:flex-row gap-2">
                        <input type="hidden" name="view" value="{{ $viewMode }}">

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="جستجو در نام قطعه، سریال، مدل، شرح مشکل..."
                            class="input-luxury flex-1 !py-2 !px-3 text-sm">

                        <select name="status" class="input-luxury lg:w-40 !py-2 !px-3 text-sm">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="new" {{ request('status') == 'new'      ? 'selected' : '' }}>جدید</option>
                            <option value="pending" {{ request('status') == 'pending'  ? 'selected' : '' }}>در انتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تایید شده</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>رد شده</option>
                        </select>

                        <button type="submit" class="btn-primary lg:w-auto !py-2 !px-4 text-sm shrink-0">
                            جستجو
                        </button>

                        @if(request('search') || request('status'))
                        <a href="{{ route('reports.index', ['view' => $viewMode]) }}" class="btn-secondary lg:w-auto !py-2 !px-4 text-sm shrink-0">
                            حذف فیلتر
                        </a>
                        @endif
                    </form>

                    <x-view-toggle :current="$viewMode" route="reports.index" />
                </div>
            </div>

            @if($reports->count() > 0)

            {{-- ════ GRID MODE — فشرده ولی همه‌ی فیلدهای مشخصه حفظ شده ════ --}}
            @if($viewMode === 'grid')
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 view-grid-mode">
                @foreach($reports as $report)
                @php
                $statusConfig = match($report->status) {
                'approved' => ['badge-success', 'تایید شده', '✓'],
                'rejected' => ['badge-danger', 'رد شده', '✕'],
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                default => ['badge-info', 'جدید', '★'],
                };
                @endphp
                <div class="card-luxury p-3.5 hover:shadow-lg hover:shadow-primary-900/15 hover:-translate-y-0.5 transition-all duration-200 group {{ $report->isUnreadBy() ? 'card-unread' : '' }}"
                    data-bulk-item
                    data-id="{{ $report->id }}">

                    <!-- Header row -->
                    <div class="flex items-start justify-between gap-1.5 mb-2 pb-2 border-b border-dark-700">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="text-xs font-semibold text-dark-400 font-medium truncate">{{ $report->request_number }}</span>
                                <x-unread-badge :model="$report" />
                            </div>
                            <h3 class="text-sm font-bold text-cream-100 truncate group-hover:text-primary-400 transition-colors" title="{{ $report->part_name }}">
                                {{ $report->part_name }}
                            </h3>
                        </div>
                        <span class="badge {{ $statusConfig[0] }} !text-[10px] !px-2 !py-0.5 shrink-0">
                            {{ $statusConfig[2] }} {{ $statusConfig[1] }}
                        </span>
                    </div>

                    <!-- Info grid: تکنسین / تاریخ / سریال / نفرساعت -->
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 mb-2.5 text-[11px]">
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تکنسین">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $report->user->name }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تاریخ">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-cream-200 font-medium truncate">{{ $report->request_date_jalali }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="سریال">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $report->serial_number }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="نفر‌ساعت">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $report->workers_count }} × {{ $report->hours_per_worker }}</span>
                        </div>
                    </div>

                    <!-- Approval pills (کامل با لیبل) -->
                    <div class="flex gap-1.5 mb-2.5 flex-wrap">
                        @foreach([
                        ['label' => 'پذیرش', 'status' => $report->request_approval],
                        ['label' => 'تامین', 'status' => $report->supply_approval],
                        ['label' => 'مدیر', 'status' => $report->ceo_approval],
                        ] as $approval)
                        @php
                        $sv = $approval['status'];
                        if ($sv === 1 || $sv === '1' || $sv === true) { $ac = 'bg-green-500/20 text-green-400 border-green-500/30'; $ai = '✓'; }
                        elseif ($sv === 0 || $sv === '0' || $sv === false) { $ac = 'bg-red-500/20 text-red-400 border-red-500/30'; $ai = '✕'; }
                        else { $ac = 'bg-stone-100 text-stone-500 border-stone-300'; $ai = '⏱'; }
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $ac }}">{{ $ai }} {{ $approval['label'] }}</span>
                        @endforeach
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-1.5 pt-2 border-t border-dark-700">
                        <a href="{{ route('reports.show', $report) }}" class="flex-1 btn-secondary text-center !py-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            مشاهده
                        </a>
                        @if(auth()->id() == $report->user_id && in_array($report->status, ['new', 'pending']))
                        <a href="{{ route('reports.edit', $report) }}" class="px-3 py-1.5 bg-yellow-500/20 text-yellow-400 rounded-lg hover:bg-yellow-500/30 transition-all border border-yellow-500/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('آیا از حذف این گزارش اطمینان دارید؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-all border border-red-500/30">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @else
            {{-- ════ LIST MODE ════ (بدون تغییر، از استایل‌های خارجی list-* استفاده می‌کنه) --}}
            <div class="items-list-mode view-list-mode">
                @foreach($reports as $report)
                @php
                $statusConfig = match($report->status) {
                'approved' => ['badge-success', 'تایید شده', '✓'],
                'rejected' => ['badge-danger', 'رد شده', '✕'],
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                default => ['badge-info', 'جدید', '★'],
                };
                @endphp

                <div class="card-luxury list-card group hover:shadow-xl hover:shadow-primary-900/15 transition-all duration-200 {{ $report->isUnreadBy() ? 'card-unread' : '' }}"
                    data-bulk-item
                    data-id="{{ $report->id }}">

                    <div class="bulk-checkbox-col">
                        <input type="checkbox"
                            class="bulk-checkbox"
                            data-id="{{ $report->id }}"
                            onchange="bulkToggle('{{ $report->id }}', this)">
                    </div>

                    <div class="list-main-content">
                        <div class="list-title-group">
                            <div class="list-title group-hover:text-primary-400">
                            <span>{{ $report->request_number }}</span>
                                <x-unread-badge :model="$report" /></div>
                            <div class="list-subtitle flex items-center gap-1.5">
                                {{ $report->part_name }}
                            </div>
                        </div>

                        <div class="list-meta-group">
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="list-meta-val">{{ $report->user->name }}</span>
                            </div>
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="list-meta-val">{{ $report->request_date_jalali }}</span>
                            </div>
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="list-meta-val">{{ $report->serial_number }}</span>
                            </div>
                            <div class="list-meta-item hidden lg:flex">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="list-meta-val">{{ $report->workers_count }} × {{ $report->hours_per_worker }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="list-approvals">
                        @foreach([
                        ['label' => 'پذیرش', 'status' => $report->request_approval],
                        ['label' => 'تامین', 'status' => $report->supply_approval],
                        ['label' => 'مدیر', 'status' => $report->ceo_approval],
                        ] as $approval)
                        @php
                        $sv = $approval['status'];
                        if ($sv === 1 || $sv === '1' || $sv === true) { $ac = 'bg-green-500/20 text-green-400 border-green-500/30'; $ai = '✓'; }
                        elseif ($sv === 0 || $sv === '0' || $sv === false) { $ac = 'bg-red-500/20 text-red-400 border-red-500/30'; $ai = '✕'; }
                        else { $ac = 'bg-stone-100 text-stone-500 border-stone-300'; $ai = '⏱'; }
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ac }}">{{ $ai }} {{ $approval['label'] }}</span>
                        @endforeach
                    </div>

                    <div class="list-actions">
                        <span class="badge {{ $statusConfig[0] }} text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>

                        <a href="{{ route('reports.show', $report) }}"
                            class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-primary-400 hover:bg-dark-700 transition-all border border-dark-700"
                            title="مشاهده">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        @if(auth()->id() == $report->user_id && in_array($report->status, ['new', 'pending']))
                        <a href="{{ route('reports.edit', $report) }}"
                            class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25"
                            title="ویرایش">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('آیا از حذف این گزارش اطمینان دارید؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25"
                                title="حذف">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $reports->links() }}
            </div>

            @else
            <!-- Empty State -->
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">گزارشی یافت نشد</h3>
                <p class="text-dark-400 mb-6">هنوز هیچ گزارشی ثبت نشده است</p>
                @if(auth()->user()->isTechnician())
                <a href="{{ route('reports.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اولین گزارش را ثبت کنید
                </a>
                @endif
            </div>
            @endif

        </div>
    </div>

    @if($viewMode === 'list')
    <x-bulk-action-bar :actions="[
        [
            'label' => 'اکسپورت اکسل',
            'route' => 'reports.exportExcel',
            'class' => 'export',
            'icon'  => 'excel',
            'type'  => 'export',
        ],
        [
            'label'  => 'حذف انتخاب‌شده‌ها',
            'route'  => 'reports.index',
            'class'  => 'danger',
            'icon'   => 'trash',
            'method' => 'DELETE',
        ],
    ]" />
    @endif

</x-app-layout>