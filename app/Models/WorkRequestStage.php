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
        'reception'  => 'پذیرش',
        'workshop'   => 'کارگاه',
        'estimation' => 'برآورد هزینه',
        'approval'   => 'رد یا تایید',
        'delivery'   => 'تحویل کالا',
        'financial'  => 'مالی',
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
