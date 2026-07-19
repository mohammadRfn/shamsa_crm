<x-app-layout>

    @include('components._list-view-styles')

    <div class="py-5 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-3">

            <!-- Toolbar یکپارچه: ثبت درخواست + سرچ/فیلتر + سوییچ گرید/لیست همه توی یه ردیف -->
            <div class="card-luxury p-2.5">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
                    @if(auth()->user()->isReception() || auth()->user()->isCEO())
                    <a href="{{ route('workrequests.create') }}" class="btn-primary inline-flex items-center justify-center gap-2 !py-2 !px-4 text-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        ثبت درخواست جدید
                    </a>
                    @endif

                    <form method="GET" action="{{ route('workrequests.index') }}" class="flex-1 flex flex-col lg:flex-row gap-2">
                        <input type="hidden" name="view" value="{{ $viewMode }}">

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="جستجو در شماره درخواست، سریال، مدل، تجهیز، شرح کار یا نام ثبت‌کننده..."
                            class="input-luxury flex-1 !py-2 !px-3 text-sm">

                        <select name="request_type" class="input-luxury lg:w-36 !py-2 !px-3 text-sm">
                            <option value="">همه انواع</option>
                            <option value="repair" {{ request('request_type') == 'repair'  ? 'selected' : '' }}>تعمیر</option>
                            <option value="service" {{ request('request_type') == 'service' ? 'selected' : '' }}>سرویس</option>
                            <option value="install" {{ request('request_type') == 'install' ? 'selected' : '' }}>نصب</option>
                            <option value="sale" {{ request('request_type') == 'sale'    ? 'selected' : '' }}>فروش</option>
                        </select>

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

                        @if(request('search') || request('status') || request('request_type'))
                        <a href="{{ route('workrequests.index', ['view' => $viewMode]) }}" class="btn-secondary lg:w-auto !py-2 !px-4 text-sm shrink-0">
                            حذف فیلتر
                        </a>
                        @endif
                    </form>

                    <x-view-toggle :current="$viewMode" route="workrequests.index" />
                </div>
            </div>

            @if($workRequests->count() > 0)

            {{-- ════ GRID MODE — فشرده ════ --}}
            @if($viewMode === 'grid')
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 view-grid-mode">
                @foreach($workRequests as $request)
                @php
                $typeConfig = match($request->request_type) {
                'repair' => ['bg-red-500/20 text-red-400 border-red-500/30', '🔧 تعمیر'],
                'service' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', '⚙️ سرویس'],
                'install' => ['bg-green-500/20 text-green-400 border-green-500/30', '🔌 نصب'],
                'sale' => ['bg-yellow-500/20 text-yellow-400 border-yellow-500/30','💰 فروش'],
                default => ['bg-dark-700 text-dark-400 border-dark-600', '📋'],
                };
                @endphp
                <div class="card-luxury p-3.5 hover:shadow-lg hover:shadow-primary-900/15 hover:-translate-y-0.5 transition-all duration-200 group {{ $request->isUnreadBy() ? 'card-unread' : '' }}"
                    data-bulk-item
                    data-id="{{ $request->id }}">

                    <!-- Header row -->
                    <div class="flex items-start justify-between gap-1.5 mb-2 pb-2 border-b border-dark-700">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="text-xs font-semibold text-dark-400 font-medium truncate">{{ $request->request_number }}</span>
                                <x-unread-badge :model="$request" />
                            </div>
                            <h3 class="text-sm font-bold text-cream-100 truncate group-hover:text-primary-400 transition-colors" title="{{ $request->equipment_name }}">
                                {{ $request->equipment_name }}
                            </h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border shrink-0 {{ $typeConfig[0] }}">{{ $typeConfig[1] }}</span>
                    </div>

                    <!-- Info grid: ثبت‌کننده / تاریخ / سریال / مدل دستگاه -->
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 mb-2.5 text-[11px]">
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="ثبت‌کننده">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $request->user->name }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تاریخ">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-cream-200 font-medium truncate">{{ $request->request_date_jalali }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="سریال">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $request->serial_number }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="مدل دستگاه">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $request->device_model }}</span>
                        </div>
                    </div>

                    <!-- مسئول تماس / تلفن -->
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1 mb-2.5 text-[11px] section-inner !p-2">
                        <div class="min-w-0">
                            <div class="text-dark-400">مسئول تماس:</div>
                            <div class="text-cream-100 font-medium truncate">{{ $request->contact_person }}</div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-dark-400">تلفن:</div>
                            <div class="text-cream-100 font-medium direction-ltr text-right truncate">{{ $request->contact_phone }}</div>
                        </div>
                    </div>

                    <!-- Approval pills -->
                    <!-- <div class="flex gap-1.5 mb-2.5 flex-wrap">
                        @foreach([
                        ['label' => 'پذیرش', 'status' => $request->request_approval],
                        ['label' => 'مدیر عامل', 'status' => $request->ceo_approval],
                        ] as $approval)
                        @php
                        $sv = $approval['status'];
                        if ($sv === 1 || $sv === '1' || $sv === true) { $ac = 'bg-green-500/20 text-green-400 border-green-500/30'; $ai = '✓'; }
                        elseif ($sv === 0 || $sv === '0' || $sv === false) { $ac = 'bg-red-500/20 text-red-400 border-red-500/30'; $ai = '✕'; }
                        else { $ac = 'bg-stone-100 text-stone-500 border-stone-300'; $ai = '⏱'; }
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $ac }}">{{ $ai }} {{ $approval['label'] }}</span>
                        @endforeach
                    </div> -->

                    <!-- Actions -->
                    <div class="flex gap-1.5 pt-2 border-t border-dark-700">
                        <a href="{{ route('workrequests.show', $request) }}" class="flex-1 btn-secondary text-center !py-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            مشاهده
                        </a>
                        @if((auth()->user()->isReception() || auth()->user()->isCEO()) && in_array($request->status, ['new', 'pending']))
                        <a href="{{ route('workrequests.edit', $request) }}" class="px-3 py-1.5 bg-yellow-500/20 text-yellow-400 rounded-lg hover:bg-yellow-500/30 transition-all border border-yellow-500/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('workrequests.destroy', $request) }}" method="POST" onsubmit="return confirm('آیا از حذف این درخواست اطمینان دارید؟')">
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
            {{-- ════ LIST MODE ════ (بدون تغییر) --}}
            <div class="items-list-mode view-list-mode">
                @foreach($workRequests as $request)
                @php
                $statusConfig = match($request->status) {
                'approved' => ['badge-success', 'تایید شده', '✓'],
                'rejected' => ['badge-danger', 'رد شده', '✕'],
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'sent' => ['badge-info', 'ارسال شده', '📤'],
                default => ['badge-info', 'جدید', '★'],
                };
                $typeConfig = match($request->request_type) {
                'repair' => ['bg-red-500/20 text-red-400 border-red-500/30', '🔧 تعمیر'],
                'service' => ['bg-blue-500/20 text-blue-400 border-blue-500/30', '⚙️ سرویس'],
                'install' => ['bg-green-500/20 text-green-400 border-green-500/30', '🔌 نصب'],
                'sale' => ['bg-yellow-500/20 text-yellow-400 border-yellow-500/30','💰 فروش'],
                default => ['bg-dark-700 text-dark-400 border-dark-600', '📋'],
                };
                @endphp
                <div class="card-luxury list-card group hover:shadow-xl hover:shadow-primary-900/15 transition-all duration-200 {{ $request->isUnreadBy() ? 'card-unread' : '' }}"
                    data-bulk-item data-id="{{ $request->id }}">

                    <div class="bulk-checkbox-col">
                        <input type="checkbox" class="bulk-checkbox" data-id="{{ $request->id }}" onchange="bulkToggle('{{ $request->id }}', this)">
                    </div>

                    <div class="list-main-content">
                        <div class="list-title-group">
                            <div class="list-title group-hover:text-primary-400">{{ $request->equipment_name }}</div>
                            <div class="list-subtitle flex items-center gap-1.5">
                                <span class="font-bold" style="font-size:0.95rem; color:#292524;">{{ $request->request_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs border {{ $typeConfig[0] }}">{{ $typeConfig[1] }}</span>
                                <x-unread-badge :model="$request" />
                            </div>
                        </div>
                        <div class="list-meta-group">
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="list-meta-val">{{ $request->user->name }}</span>
                            </div>
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="list-meta-val">{{ $request->request_date_jalali }}</span>
                            </div>
                            <div class="list-meta-item hidden lg:flex">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="list-meta-val">{{ Str::limit($request->device_model, 12) }}</span>
                            </div>
                            <div class="list-meta-item hidden xl:flex">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="list-meta-val">{{ $request->contact_phone }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="list-approvals">
                        @foreach([
                        ['label' => 'پذیرش', 'status' => $request->request_approval],
                        ['label' => 'مدیر', 'status' => $request->ceo_approval],
                        ] as $approval)
                        @php
                        $sv = $approval['status'];
                        if ($sv === 1 || $sv === '1' || $sv === true) { $ac='bg-green-500/20 text-green-400 border-green-500/30'; $ai='✓'; }
                        elseif ($sv === 0 || $sv === '0' || $sv === false) { $ac='bg-red-500/20 text-red-400 border-red-500/30'; $ai='✕'; }
                        else { $ac = 'bg-stone-100 text-stone-500 border-stone-300'; $ai = '⏱'; }
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $ac }}">{{ $ai }} {{ $approval['label'] }}</span>
                        @endforeach
                    </div>

                    <div class="list-actions">
                        <a href="{{ route('workrequests.show', $request) }}" class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-primary-400 hover:bg-dark-700 transition-all border border-dark-700" title="مشاهده">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @if((auth()->user()->isReception() || auth()->user()->isCEO()) && in_array($request->status, ['new', 'pending']))
                        <a href="{{ route('workrequests.edit', $request) }}" class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('workrequests.destroy', $request) }}" method="POST" onsubmit="return confirm('آیا از حذف این درخواست اطمینان دارید؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25" title="حذف">
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

            <div class="flex justify-center">{{ $workRequests->links() }}</div>

            @else
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">درخواستی یافت نشد</h3>
                <p class="text-dark-400 mb-6">هنوز هیچ درخواستی ثبت نشده است</p>
                @if(auth()->user()->isReception() || auth()->user()->isCEO())
                <a href="{{ route('workrequests.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اولین درخواست را ثبت کنید
                </a>
                @endif
            </div>
            @endif

        </div>
    </div>

    @php
    $bulkActions = [['label' => 'اکسپورت اکسل', 'route' => 'workrequests.exportExcel', 'class' => 'export', 'icon' => 'excel', 'type' => 'export']];
    if (auth()->user()->isReception() || auth()->user()->isCEO()) {
    $bulkActions[] = ['label' => 'حذف انتخاب‌شده‌ها', 'route' => 'workrequests.index', 'class' => 'danger', 'icon' => 'trash', 'method' => 'DELETE'];
    }
    @endphp
    <x-bulk-action-bar :actions="$bulkActions" />

</x-app-layout>