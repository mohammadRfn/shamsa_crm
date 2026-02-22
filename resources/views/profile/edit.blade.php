<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-8">
            
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-l from-primary-400 to-cream-100 bg-clip-text text-transparent">
                    پروفایل کاربری
                </h1>
                <p class="text-dark-400 mt-2">مدیریت اطلاعات حساب کاربری و تنظیمات امنیتی</p>
            </div>

            <!-- Update Profile Information -->
            <div class="card-luxury p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password -->
            <div class="card-luxury p-6">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Delete Account -->
            <div class="card-luxury p-6">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>