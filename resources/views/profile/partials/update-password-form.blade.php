<section>
    <header class="pb-4 border-b-2 divider mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-cream-100">تغییر رمز عبور</h2>
                <p class="text-sm text-dark-400 mt-1">از رمز عبور قوی و تصادفی برای امنیت بیشتر استفاده کنید</p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-cream-200 mb-2">
                رمز عبور فعلی
            </label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   autocomplete="current-password"
                   class="input-luxury w-full">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- New Password -->
        <div>
            <label for="update_password_password" class="block text-sm font-medium text-cream-200 mb-2">
                رمز عبور جدید
            </label>
            <input id="update_password_password" 
                   name="password" 
                   type="password" 
                   autocomplete="new-password"
                   class="input-luxury w-full">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-cream-200 mb-2">
                تایید رمز عبور جدید
            </label>
            <input id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   type="password" 
                   autocomplete="new-password"
                   class="input-luxury w-full">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" class="btn-primary">
                ذخیره رمز عبور
            </button>

            @if (session('status') === 'password-updated')
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