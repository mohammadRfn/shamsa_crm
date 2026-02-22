<section>
    <header class="pb-4 border-b-2 divider mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-cream-100">اطلاعات پروفایل</h2>
                <p class="text-sm text-dark-400 mt-1">به‌روزرسانی نام و ایمیل حساب کاربری</p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-cream-200 mb-2">نام کامل</label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   class="input-luxury w-full">
            <x-input-error class="mt-2 text-red-400 text-sm" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-cream-200 mb-2">ایمیل</label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="username"
                   class="input-luxury w-full">
            <x-input-error class="mt-2 text-red-400 text-sm" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-500/10 border-2 border-yellow-500/30 rounded-lg">
                    <p class="text-sm text-yellow-400">
                        ایمیل شما تایید نشده است.
                        <button form="send-verification" 
                                class="underline text-yellow-300 hover:text-yellow-200 font-medium">
                            برای ارسال مجدد لینک تایید کلیک کنید
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-400 font-medium">
                            لینک تایید جدید به ایمیل شما ارسال شد.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" class="btn-primary">
                ذخیره تغییرات
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-400 font-medium">
                    ✓ ذخیره شد
                </p>
            @endif
        </div>
    </form>
</section>