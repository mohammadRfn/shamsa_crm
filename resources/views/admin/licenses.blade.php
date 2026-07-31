<x-app-layout>
    <div class="py-3 px-3 sm:px-5 lg:px-6">
        <div class="max-w-6xl mx-auto space-y-2">

            <div class="card-luxury p-2 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-5 h-5 text-primary-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <h1 class="text-base font-bold text-cream-100">مدیریت لایسنس‌ها و نظارت سیستم‌ها</h1>
                </div>
                <span class="badge badge-info !text-xs">{{ $licenses->count() }} مورد</span>
            </div>

            @if(session('success'))
            <div class="card-luxury p-2.5 text-xs text-green-400 border border-green-500/25 bg-green-500/5">
                {{ session('success') }}
            </div>
            @endif

            <div class="card-luxury p-2.5">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-xs">
                        <thead>
                            <tr class="bg-dark-700/50">
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-right">پروژه / وضعیت</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-right">Fingerprint</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-right">نام سیستم</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-right">ویندوز/کاربر</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">اولین بازدید</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">آخرین بازدید</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">IP</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">کشور</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($licenses as $l)
                            <tr class="hover:bg-dark-700/30 transition-all {{ $l->auto_registered ? 'bg-red-500/5' : '' }}">
                                <td class="border border-dark-600 px-2 py-1.5">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-cream-100 font-medium whitespace-nowrap">{{ $l->customer_note ?? '—' }}</span>
                                        @if($l->auto_registered)
                                        <span class="badge badge-danger !text-[10px] !py-0.5 w-fit">⚠ ثبت خودکار / بدون تایید</span>
                                        @elseif($l->revoked)
                                        <span class="badge badge-danger !text-[10px] !py-0.5 w-fit">✕ غیرفعال</span>
                                        @else
                                        <span class="badge badge-success !text-[10px] !py-0.5 w-fit">✓ فعال و تاییدشده</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-400">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono break-all">{{ $l->machine_fingerprint }}</span>
                                        <button
                                            type="button"
                                            onclick="navigator.clipboard.writeText('{{ $l->machine_fingerprint }}'); this.innerText='✓'; setTimeout(() => this.innerText='📋', 1200)"
                                            class="shrink-0 p-1 rounded-md bg-dark-700/60 hover:bg-primary-500/20 text-cream-300 hover:text-primary-400 transition-all border border-dark-600">📋</button>
                                    </div>
                                </td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-200 whitespace-nowrap">{{ $l->hostname ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-300 whitespace-nowrap">
                                    <div class="text-[11px] text-dark-400">{{ $l->os_info ?? '-' }}</div>
                                    <div>{{ $l->windows_username ?? '-' }}</div>
                                </td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center text-dark-400 whitespace-nowrap">
                                    {{ $l->first_seen_at ? \Carbon\Carbon::parse($l->first_seen_at)->diffForHumans() : '-' }}
                                </td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center text-dark-400 whitespace-nowrap">
                                    {{ $l->last_seen_at ? \Carbon\Carbon::parse($l->last_seen_at)->diffForHumans() : 'هنوز متصل نشده' }}
                                </td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center text-cream-300 font-mono">{{ $l->last_ip ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center text-cream-300">{{ $l->last_country ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center">
                                    @if($l->revoked)
                                    <form method="POST" action="{{ route('admin.licenses.reactivate', $l->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500/25 transition-all border border-green-500/25 text-xs whitespace-nowrap">
                                            فعال‌سازی مجدد
                                        </button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('admin.licenses.revoke', $l->id) }}" onsubmit="return confirm('آیا مطمئنید می‌خواهید این سیستم را مسدود کنید؟')">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25 text-xs whitespace-nowrap">
                                            مسدودسازی
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="border border-dark-600 px-2 py-4 text-center text-dark-400">هنوز موردی ثبت نشده</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>