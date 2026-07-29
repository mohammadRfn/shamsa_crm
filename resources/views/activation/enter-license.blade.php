@extends('layouts.app')

@section('content')
<div class="card-luxury max-w-md mx-auto p-6 text-right" dir="rtl">
    <h2 class="text-sm font-semibold text-dark-400 mb-4">وارد کردن کد لایسنس</h2>

    @if(session('error'))
        <div class="text-xs text-red-400 mb-3">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('license.enter.submit') }}">
        @csrf
        <textarea name="license_key" class="input-luxury w-full !py-1.5 !px-2 text-xs" rows="4" placeholder="کد لایسنس را اینجا وارد کنید"></textarea>
        <button type="submit" class="btn-luxury w-full !py-1.5 text-xs mt-3">تایید و فعال‌سازی</button>
    </form>
</div>
@endsection