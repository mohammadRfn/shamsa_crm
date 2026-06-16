<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                        مدیریت کاربران
                    </h1>
                    <p class="text-dark-400 mt-2">ایجاد، ویرایش و مشاهده فعالیت اعضای تیم</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    کاربر جدید
                </a>
            </div>

            @if(session('success'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                $roleStats = [
                ['role' => 'technician', 'label' => 'تکنسین', 'color' => 'blue', 'icon' => 'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z'],
                ['role' => 'reception', 'label' => 'پذیرش', 'color' => 'green', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                ['role' => 'supply', 'label' => 'تامین', 'color' => 'yellow', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['role' => 'ceo', 'label' => 'مدیر', 'color' => 'red', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ];
                @endphp

                @foreach($roleStats as $stat)
                @php $count = $users->where('role', $stat['role'])->count(); @endphp
                <div class="card-luxury p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-{{ $stat['color'] }}-500/15 border border-{{ $stat['color'] }}-500/25 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-{{ $stat['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-cream-100">{{ $users->where('role', $stat['role'])->count() }}</div>
                        <div class="text-sm text-dark-400">{{ $stat['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Users Table --}}
            <div class="card-luxury overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 divider">
                                <th class="text-right px-6 py-4 text-xs font-semibold text-dark-400 uppercase tracking-wider">کاربر</th>
                                <th class="text-right px-6 py-4 text-xs font-semibold text-dark-400 uppercase tracking-wider hidden sm:table-cell">نقش</th>
                                <th class="text-right px-6 py-4 text-xs font-semibold text-dark-400 uppercase tracking-wider hidden lg:table-cell">گزارش</th>
                                <th class="text-right px-6 py-4 text-xs font-semibold text-dark-400 uppercase tracking-wider hidden lg:table-cell">سفارش</th>
                                <th class="text-right px-6 py-4 text-xs font-semibold text-dark-400 uppercase tracking-wider hidden xl:table-cell">عضویت</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-700/50">
                            @foreach($users as $user)
                            @php
                            $roleMap = [
                            'technician' => ['تکنسین', 'bg-blue-500/15 text-blue-400 border-blue-500/25'],
                            'reception' => ['پذیرش', 'bg-green-500/15 text-green-400 border-green-500/25'],
                            'supply' => ['تامین', 'bg-yellow-500/15 text-yellow-400 border-yellow-500/25'],
                            'ceo' => ['مدیرعامل', 'bg-red-500/15 text-red-400 border-red-500/25'],
                            ];
                            [$roleLabel, $roleClass] = $roleMap[$user->role] ?? ['نامشخص', 'bg-dark-700 text-dark-400 border-dark-600'];
                            @endphp
                            <tr class="hover:bg-dark-700/20 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500/40 to-primary-700/40 border border-primary-500/30 flex items-center justify-center text-primary-300 font-bold text-lg shrink-0">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-cream-100 group-hover:text-primary-400 transition-colors">{{ $user->name }}</div>
                                            <div class="text-sm text-dark-400 mt-0.5">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $roleClass }}">{{ $roleLabel }}</span>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    <span class="text-cream-200 font-medium">{{ $user->reports_count ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    <span class="text-cream-200 font-medium">{{ $user->part_orders_count ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 hidden xl:table-cell">
                                    <span class="text-sm text-dark-400">{{ $user->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.show', $user) }}"
                                            class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-primary-400 hover:bg-dark-700 transition-all border border-dark-700"
                                            title="مشاهده فعالیت">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="p-2 rounded-lg bg-yellow-500/10 text-yellow-400 hover:bg-yellow-500/25 transition-all border border-yellow-500/25"
                                            title="ویرایش">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('آیا از حذف کاربر {{ addslashes($user->name) }} اطمینان دارید؟')">
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
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="px-6 py-4 border-t-2 divider">
                    {{ $users->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>