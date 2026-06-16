<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto space-y-8">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}"
                    class="p-2 rounded-lg bg-dark-800 text-dark-300 hover:text-cream-100 hover:bg-dark-700 transition-all border border-dark-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-cream-100">کاربر جدید</h1>
                    <p class="text-dark-400 text-sm mt-1">ایجاد حساب کاربری برای عضو جدید تیم</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="card-luxury p-8 space-y-6">
                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-cream-300 mb-2">نام و نام خانوادگی</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="input-luxury w-full @error('name') border-red-500/50 @enderror"
                            placeholder="محمد احمدی" autofocus>
                        @error('name')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-cream-300 mb-2">ایمیل</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="input-luxury w-full direction-ltr @error('email') border-red-500/50 @enderror"
                            placeholder="user@company.com">
                        @error('email')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-cream-300 mb-2">نقش</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach([
                            ['technician', 'تکنسین', 'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z', 'blue'],
                            ['reception', 'پذیرش', 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'green'],
                            ['supply', 'تامین', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'yellow'],
                            ['ceo', 'مدیرعامل', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'red'],
                            ] as [$val, $lbl, $icon, $color])
                            <label class="role-card cursor-pointer" data-color="{{ $color }}">
                                <input type="radio" name="role" value="{{ $val }}"
                                    class="sr-only role-radio"
                                    {{ old('role') === $val ? 'checked' : '' }}>
                                <div class="role-card-inner flex items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200
                                            border-dark-600 bg-dark-800/50 hover:border-{{ $color }}-500/40 hover:bg-{{ $color }}-500/5">
                                    <svg class="w-5 h-5 text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                                    </svg>
                                    <span class="font-medium text-cream-200">{{ $lbl }}</span>
                                    <svg class="w-4 h-4 text-{{ $color }}-400 mr-auto role-check opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('role')
                        <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-cream-300 mb-2">رمز عبور</label>
                            <input type="password" name="password"
                                class="input-luxury w-full direction-ltr @error('password') border-red-500/50 @enderror"
                                placeholder="••••••••">
                            @error('password')
                            <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-cream-300 mb-2">تکرار رمز عبور</label>
                            <input type="password" name="password_confirmation"
                                class="input-luxury w-full direction-ltr"
                                placeholder="••••••••">
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1 py-3 font-semibold">
                            ایجاد کاربر
                        </button>
                        <a href="{{ route('users.index') }}" class="btn-secondary px-6 py-3">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.role-card').forEach(card => {
            const radio = card.querySelector('.role-radio');
            const inner = card.querySelector('.role-card-inner');
            const check = card.querySelector('.role-check');
            const color = card.dataset.color;

            function update() {
                if (radio.checked) {
                    inner.classList.add(`border-${color}-500`, `bg-${color}-500/10`);
                    inner.classList.remove('border-dark-600', 'bg-dark-800/50');
                    check.classList.remove('opacity-0');
                } else {
                    inner.classList.remove(`border-${color}-500`, `bg-${color}-500/10`);
                    inner.classList.add('border-dark-600', 'bg-dark-800/50');
                    check.classList.add('opacity-0');
                }
            }

            radio.addEventListener('change', () => {
                document.querySelectorAll('.role-card').forEach(c => {
                    const r = c.querySelector('.role-radio');
                    const i = c.querySelector('.role-card-inner');
                    const ch = c.querySelector('.role-check');
                    const col = c.dataset.color;
                    i.classList.remove(`border-${col}-500`, `bg-${col}-500/10`);
                    i.classList.add('border-dark-600', 'bg-dark-800/50');
                    ch.classList.add('opacity-0');
                });
                update();
            });

            update();
        });
    </script>
</x-app-layout>