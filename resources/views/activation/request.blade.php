@extends('layouts.app')

@section('content')
<div class="card-luxury max-w-md mx-auto p-6 text-right" dir="rtl">
    <h2 class="text-sm font-semibold text-dark-400 mb-4">فعال‌سازی نرم‌افزار</h2>

    @if(session('error'))
    <div class="text-xs text-red-400 mb-3">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('license.activate.submit') }}">
        @csrf
        <button type="submit" class="btn-luxury w-full !py-1.5 text-xs">
            ارسال درخواست فعال‌سازی
        </button>
    </form>
</div>
@endsection