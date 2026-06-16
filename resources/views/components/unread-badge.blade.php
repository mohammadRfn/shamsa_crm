
@if($model->isUnreadBy())
<span
    class="unread-badge"
    title="تغییرات جدید — مشاهده نشده"
    aria-label="خوانده نشده"
>
    <span class="unread-dot" aria-hidden="true"></span>
    جدید
</span>
@endif