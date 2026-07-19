<x-app-layout>

    @include('components._list-view-styles')

    <div class="py-5 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-3">

            <!-- Toolbar یکپارچه: ثبت سفارش + سرچ/فیلتر + سوییچ گرید/لیست همه توی یه ردیف -->
            <div class="card-luxury p-2.5">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2">
                    @if(auth()->user()->isTechnician())
                    <a href="{{ route('partorders.create') }}" class="btn-primary inline-flex items-center justify-center gap-2 !py-2 !px-4 text-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        ثبت سفارش جدید
                    </a>
                    @endif

                    <form method="GET" action="{{ route('partorders.index') }}" class="flex-1 flex flex-col lg:flex-row gap-2">
                        <input type="hidden" name="view" value="{{ $viewMode }}">

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="جستجو در نام قطعه، شماره سفارش، تجهیزات یا نام تکنسین..."
                            class="input-luxury flex-1 !py-2 !px-3 text-sm">

                        <select name="status" class="input-luxury lg:w-40 !py-2 !px-3 text-sm">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="new" {{ request('status') == 'new'      ? 'selected' : '' }}>جدید</option>
                            <option value="pending" {{ request('status') == 'pending'  ? 'selected' : '' }}>در انتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تایید شده</option>
                            <option value="failed" {{ request('status') == 'failed'   ? 'selected' : '' }}>رد شده</option>
                        </select>

                        <button type="submit" class="btn-primary lg:w-auto !py-2 !px-4 text-sm shrink-0">
                            جستجو
                        </button>

                        @if(request('search') || request('status'))
                        <a href="{{ route('partorders.index', ['view' => $viewMode]) }}" class="btn-secondary lg:w-auto !py-2 !px-4 text-sm shrink-0">
                            حذف فیلتر
                        </a>
                        @endif
                    </form>

                    <x-view-toggle :current="$viewMode" route="partorders.index" />
                </div>
            </div>

            @if($partOrders->count() > 0)

            {{-- ════ GRID MODE — فشرده ════ --}}
            @if($viewMode === 'grid')
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 view-grid-mode">
                @foreach($partOrders as $order)
                @php
                $statusConfig = match($order->status) {
                'approved' => ['badge-success', 'تایید شده', '✓'],
                'failed' => ['badge-danger', 'رد شده', '✕'],
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'sent' => ['badge-info', 'ارسال شده', '📦'],
                default => ['badge-info', 'جدید', '★'],
                };
                @endphp
                <div class="card-luxury p-3.5 hover:shadow-lg hover:shadow-primary-900/15 hover:-translate-y-0.5 transition-all duration-200 group {{ $order->isUnreadBy() ? 'card-unread' : '' }}"
                    data-bulk-item
                    data-id="{{ $order->id }}">

                    <!-- Header row -->
                    <div class="flex items-start justify-between gap-1.5 mb-2 pb-2 border-b border-dark-700">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <span class="text-xs font-semibold text-dark-400 font-medium truncate">{{ $order->order_number }}</span>
                                <x-unread-badge :model="$order" />
                            </div>
                            <h3 class="text-sm font-bold text-cream-100 break-words group-hover:text-primary-400 transition-colors">
                                {{ implode('، ', $order->part_name ?? []) }}
                            </h3>
                        </div>
                        <span class="badge {{ $statusConfig[0] }} !text-[10px] !px-2 !py-0.5 shrink-0">
                            {{ $statusConfig[2] }} {{ $statusConfig[1] }}
                        </span>
                    </div>

                    <!-- Info grid: تکنسین / تاریخ / تجهیز / تعداد -->
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 mb-2.5 text-[11px]">
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تکنسین">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $order->user->name }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تاریخ">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-cream-200 font-medium truncate">{{ $order->order_date_jalali }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تجهیز">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <span class="text-cream-200 font-medium">{{ $order->equipment_name }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-dark-400 min-w-0" title="تعداد">
                            <svg class="w-3 h-3 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="text-cream-200 font-medium truncate">{{ implode('، ', array_map('strval', $order->quantity ?? [])) }}</span>
                        </div>
                    </div>

                    <!-- Approval pills -->
                    <div class="flex gap-1.5 mb-2.5 flex-wrap">
                        @foreach([
                        ['label' => 'پذیرش', 'status' => $order->reception_approval],
                        ['label' => 'تامین', 'status' => $order->supply_approval],
                        ['label' => 'مدیر', 'status' => $order->ceo_approval],
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
                        <a href="{{ route('partorders.show', $order) }}" class="flex-1 btn-secondary text-center !py-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            مشاهده
                        </a>
                        @if(auth()->id() == $order->user_id && in_array($order->status, ['new', 'pending']))
                        <a href="{{ route('partorders.edit', $order) }}" class="px-3 py-1.5 bg-yellow-500/20 text-yellow-400 rounded-lg hover:bg-yellow-500/30 transition-all border border-yellow-500/30">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('partorders.destroy', $order) }}" method="POST" onsubmit="return confirm('آیا از حذف این سفارش اطمینان دارید؟')">
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
                @foreach($partOrders as $order)
                @php
                $statusConfig = match($order->status) {
                'approved' => ['badge-success', 'تایید شده', '✓'],
                'failed' => ['badge-danger', 'رد شده', '✕'],
                'pending' => ['badge-warning', 'در انتظار', '⏱'],
                'sent' => ['badge-info', 'ارسال شده', '📦'],
                default => ['badge-info', 'جدید', '★'],
                };
                @endphp
                <div class="card-luxury list-card group hover:shadow-xl hover:shadow-primary-900/15 transition-all duration-200 {{ $order->isUnreadBy() ? 'card-unread' : '' }}"
                    data-bulk-item data-id="{{ $order->id }}">

                    <div class="bulk-checkbox-col">
                        <input type="checkbox" class="bulk-checkbox" data-id="{{ $order->id }}" onchange="bulkToggle('{{ $order->id }}', this)">
                    </div>

                    <div class="list-main-content">
                        <div class="list-title-group">
                            <div class="list-title group-hover:text-primary-400">{{ $order->order_number }}</div>
                            <div class="list-subtitle flex items-center gap-1.5">
                                <span>{{ implode('، ', $order->part_name ?? []) }}</span>
                                <x-unread-badge :model="$order" />
                            </div>
                        </div>
                        <div class="list-meta-group">
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="list-meta-val">{{ $order->user->name }}</span>
                            </div>
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="list-meta-val">{{ $order->order_date_jalali }}</span>
                            </div>
                            <div class="list-meta-item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                                <span class="list-meta-val">{{ $order->equipment_name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="list-approvals">
                        @foreach([
                        ['label' => 'پذیرش', 'status' => $order->reception_approval],
                        ['label' => 'تامین', 'status' => $order->supply_approval],
                        ['label' => 'مدیر', 'status' => $order->ceo_approval],
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
                        <span class="badge {{ $statusConfig[0] }} text-xs">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>

                        <button type="button" onclick="togglePartOrderItemsRow('{{ $order->id }}', this)"
                            class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-primary-400 hover:bg-dark-700 transition-all border border-dark-700"
                            title="نمایش قطعات">
                            <svg class="w-4 h-4 po-toggle-chevron transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <a href="{{ route('partorders.show', $order) }}" class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-primary-400 hover:bg-dark-700 transition-all border border-dark-700" title="مشاهده">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @if(auth()->id() == $order->user_id && in_array($order->status, ['new', 'pending']))
                        <a href="{{ route('partorders.edit', $order) }}" class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25" title="ویرایش">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('partorders.destroy', $order) }}" method="POST" onsubmit="return confirm('آیا از حذف این سفارش اطمینان دارید؟')">
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

                {{-- ردیف باز/بسته‌شونده: لیست قطعات همین سفارش، چسبیده به کارت --}}
                <div id="po-items-row-{{ $order->id }}" class="po-items-row hidden -mt-2 mb-2">
                    <div class="card-luxury border border-dark-700/70 rounded-t-none p-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="text-dark-400 text-xs">
                                        <th class="text-right py-1.5 px-2">نام قطعه</th>
                                        <th class="text-right py-1.5 px-2">پکیج</th>
                                        <th class="text-center py-1.5 px-2 w-20">تعداد</th>
                                        <th class="text-right py-1.5 px-2">توضیحات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->part_name ?? [] as $i => $pname)
                                    <tr class="border-t border-dark-700/50">
                                        <td class="py-1.5 px-2 text-cream-100">{{ $pname }}</td>
                                        <td class="py-1.5 px-2 text-cream-200">{{ ($order->package ?? [])[$i] ?? '-' }}</td>
                                        <td class="py-1.5 px-2 text-center text-primary-400 font-bold">{{ ($order->quantity ?? [])[$i] ?? '-' }}</td>
                                        <td class="py-1.5 px-2 text-dark-300">{{ ($order->description ?? [])[$i] ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-dark-400 py-2">قطعه‌ای ثبت نشده</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="flex justify-center">{{ $partOrders->links() }}</div>

            @else
            <div class="card-luxury p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-dark-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-cream-200 mb-2">سفارشی یافت نشد</h3>
                <p class="text-dark-400 mb-6">هنوز هیچ سفارشی ثبت نشده است</p>
                @if(auth()->user()->isTechnician())
                <a href="{{ route('partorders.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    اولین سفارش را ثبت کنید
                </a>
                @endif
            </div>
            @endif

        </div>
    </div>

    @if($viewMode === 'list')
    <x-bulk-action-bar :actions="[
        ['label' => 'اکسپورت اکسل', 'route' => 'partorders.exportExcel', 'class' => 'export', 'icon' => 'excel', 'type' => 'export'],
        ['label' => 'حذف انتخاب‌شده‌ها', 'route' => 'partorders.index', 'class' => 'danger', 'icon' => 'trash', 'method' => 'DELETE'],
    ]" />
    @endif

    <script>
        // فقط مخصوص صفحه PartOrder - باز/بسته کردن ردیف قطعات زیر هر سفارش در حالت لیست
        function togglePartOrderItemsRow(id, btnEl) {
            const row = document.getElementById('po-items-row-' + id);
            if (!row) return;
            row.classList.toggle('hidden');
            if (btnEl) {
                const chevron = btnEl.querySelector('.po-toggle-chevron');
                if (chevron) chevron.classList.toggle('rotate-180');
            }
        }
    </script>

</x-app-layout>