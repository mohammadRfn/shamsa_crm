<section class="space-y-6">
    <header class="pb-4 border-b-2 divider">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-cream-100">حذف حساب کاربری</h2>
                <p class="text-sm text-dark-400 mt-1">
                    با حذف حساب، تمام اطلاعات و داده‌های شما برای همیشه پاک می‌شود. قبل از حذف، اطلاعات مهم را دانلود کنید.
                </p>
            </div>
        </div>
    </header>

    <button x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-6 py-3 rounded-xl font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center gap-2 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        حذف حساب کاربری
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-dark-800 rounded-xl">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-cream-100">
                    آیا مطمئن هستید؟
                </h2>
            </div>

            <p class="text-dark-400 mb-6">
                با حذف حساب کاربری، تمام اطلاعات و داده‌های شما برای همیشه حذف خواهد شد. 
                لطفا برای تایید، رمز عبور خود را وارد کنید.
            </p>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-cream-200 mb-2">رمز عبور</label>
                <input id="password"
                       name="password"
                       type="password"
                       placeholder="رمز عبور خود را وارد کنید"
                       class="input-luxury w-full">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-400 text-sm" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        x-on:click="$dispatch('close')"
                        class="btn-secondary">
                    انصراف
                </button>

                <button type="submit"
                        class="px-6 py-3 rounded-xl font-semibold bg-red-500/25 text-red-300 border-2 border-red-500/40 hover:bg-red-500/35 transition-all inline-flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف حساب
                </button>
            </div>
        </form>
    </x-modal>
</section>