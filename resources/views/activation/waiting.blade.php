@extends('layouts.app')

@section('content')
<div class="card-luxury max-w-md mx-auto p-6 text-center" dir="rtl">
    <h2 class="text-sm font-semibold text-dark-400 mb-3">در انتظار تایید</h2>
    <p id="status-text" class="text-xs text-dark-300">درخواست شما ارسال شد. لطفاً منتظر تایید بمانید...</p>
</div>

<script>
function poll() {
    fetch("{{ route('license.poll') }}")
        .then(r => r.json())
        .then(data => {
            if (data.status === 'approved') {
                window.location.href = "{{ route('license.enter') }}";
            } else if (data.status === 'rejected') {
                document.getElementById('status-text').innerText = 'درخواست شما رد شد. با پشتیبانی تماس بگیرید.';
            } else if (data.status === 'unreachable') {
                document.getElementById('status-text').innerText = 'ارتباط با سرور برقرار نیست. لطفاً بعداً تلاش کنید.';
            } else {
                setTimeout(poll, 5000);
            }
        })
        .catch(() => setTimeout(poll, 5000));
}
poll();
</script>
@endsection