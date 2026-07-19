<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkRequestStage extends Model
{
    protected $fillable = [
        'work_request_id', 'stage', 'status', 'note', 'actioned_at', 'actioned_by',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public static array $stageLabels = [
        'reception'  => 'ثبت پذیرش',
        'under_review' => 'در نوبت بررسی',
        'performing_initial_troubleshooting' => 'درحال عیب یابی اولیه',
        'informing_customer_of_initial_cost' => 'اعلام هزینه اولیه به مشتری',
        'waiting_for_customer_approval' => 'منتظر تایید مشتری',
        'waiting_for_parts' => 'منتظر تامین قطعه',
        'performing_final_repair' => 'درحال تعمیرات نهایی',
        'quality_control' => 'تست و کنترل کیفیت',
        'ready_for_delivery' => 'اماده تحویل',
        'delivery'   => 'تحویل به مشتری',
        'unrepairable' => 'غیر قابل تعمیر',
        'customer_rejection' => 'عدم تایید قیمت توسط مشتری',
    ];

    public static array $statusLabels = [
        'pending'  => 'در انتظار',
        'done'     => 'انجام شده',
        'rejected' => 'رد شده',
    ];

    public function workRequest()
    {
        return $this->belongsTo(WorkRequest::class);
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
