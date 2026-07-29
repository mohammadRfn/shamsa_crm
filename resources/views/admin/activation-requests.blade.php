@extends('layouts.app')

@section('content')
<div class="card-luxury p-4" dir="rtl">
    <h2 class="text-sm font-semibold text-dark-400 mb-4">درخواست‌های فعال‌سازی</h2>

    @if(session('success'))
        <div class="text-xs text-green-400 mb-3">{{ session('success') }}</div>
    @endif

    <table class="w-full text-xs text-right">
        <thead>
            <tr class="text-dark-400">
                <th class="p-2">Fingerprint خام</th>
                <th class="p-2">یادداشت</th>
                <th class="p-2">وضعیت</th>
                <th class="p-2">تاریخ</th>
                <th class="p-2">عملیات</th>
            </tr>
        </thead>
        <tbody>
        @foreach($requests as $r)
            <tr class="border-t border-dark-700">
                <td class="p-2 font-mono">{{ $r->raw_fingerprint }}</td>
                <td class="p-2">{{ $r->customer_note ?? '-' }}</td>
                <td class="p-2">{{ $r->status }}</td>
                <td class="p-2">{{ $r->created_at }}</td>
                <td class="p-2">
                    @if($r->status === 'pending')
                        <form method="POST" action="{{ route('admin.activations.approve', $r->id) }}" class="inline">
                            @csrf
                            <button class="text-green-400">تایید</button>
                        </form>
                        <form method="POST" action="{{ route('admin.activations.reject', $r->id) }}" class="inline">
                            @csrf
                            <button class="text-red-400">رد</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection