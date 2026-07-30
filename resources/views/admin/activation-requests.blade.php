<x-app-layout>
    <div class="py-3 px-3 sm:px-5 lg:px-6">
        <div class="max-w-5xl mx-auto space-y-2">

            <div class="card-luxury p-2 flex items-center justify-between gap-3 flex-wrap">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-5 h-5 text-primary-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h1 class="text-base font-bold text-cream-100">درخواست‌های فعال‌سازی لایسنس</h1>
                </div>
                <span class="badge badge-info !text-xs">{{ $requests->count() }} درخواست</span>
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
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-right">پروژه</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-right">Fingerprint</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">وضعیت</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">تاریخ</th>
                                <th class="border border-dark-600 px-2 py-1.5 text-cream-300 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $r)
                            @php
                            $statusConfig = match($r->status) {
                                'approved' => ['badge-success', 'تایید شده', '✓'],
                                'rejected' => ['badge-danger', 'رد شده', '✕'],
                                default    => ['badge-warning', 'در انتظار بررسی', '⏱'],
                            };
                            @endphp
                            <tr class="hover:bg-dark-700/30 transition-all">
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-100 font-medium">{{ $r->customer_note ?? '-' }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-cream-400 font-mono truncate max-w-[220px]">{{ $r->raw_fingerprint }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center">
                                    <span class="badge {{ $statusConfig[0] }} !text-[10px] !py-0.5">{{ $statusConfig[2] }} {{ $statusConfig[1] }}</span>
                                </td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center text-dark-400">{{ $r->created_at->diffForHumans() }}</td>
                                <td class="border border-dark-600 px-2 py-1.5 text-center">
                                    @if($r->status === 'pending')
                                        <div class="flex items-center justify-center gap-1.5">
                                            <form method="POST" action="{{ route('admin.activations.approve', $r->id) }}">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500/25 transition-all border border-green-500/25" title="تایید">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.activations.reject', $r->id) }}">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/25 transition-all border border-red-500/25" title="رد">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-dark-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="border border-dark-600 px-2 py-4 text-center text-dark-400">درخواستی ثبت نشده</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>