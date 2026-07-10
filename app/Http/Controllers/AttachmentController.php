<?php
// app/Http/Controllers/AttachmentController.php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Report;
use App\Models\PartOrder;
use App\Models\WorkRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\PartOrderItem;
use App\Models\SupplyProposal;

class AttachmentController extends Controller
{
    private const MAX_SIZE_KB = 51200; // 50MB

    // ─── Upload ───────────────────────────────────────────────

    public function storeForReport(Request $request, Report $report)
    {
        $this->authorizeUpload('report', $report);
        return $this->handleUpload($request, $report, 'reports');
    }

    public function storeForPartOrder(Request $request, PartOrder $partOrder)
    {
        $this->authorizeUpload('part_order', $partOrder);
        return $this->handleUpload($request, $partOrder, 'part-orders');
    }

    public function storeForWorkRequest(Request $request, WorkRequest $workRequest)
    {
        $this->authorizeUpload('work_request', $workRequest);
        return $this->handleUpload($request, $workRequest, 'work-requests');
    }

    // ─── Delete ───────────────────────────────────────────────

    public function destroyForReport(Report $report, Attachment $attachment)
    {
        $this->authorizeUpload('report', $report);
        $this->ensureBelongsTo($attachment, $report);
        return $this->handleDelete($attachment);
    }

    public function destroyForPartOrder(PartOrder $partOrder, Attachment $attachment)
    {
        $this->authorizeUpload('part_order', $partOrder);
        $this->ensureBelongsTo($attachment, $partOrder);
        return $this->handleDelete($attachment);
    }

    public function destroyForWorkRequest(WorkRequest $workRequest, Attachment $attachment)
    {
        $this->authorizeUpload('work_request', $workRequest);
        $this->ensureBelongsTo($attachment, $workRequest);
        return $this->handleDelete($attachment);
    }

    private function handleUpload(Request $request, Model $model, string $folder): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'files'   => 'required|array|max:5',
            'files.*' => [
                'required',
                'file',
                'max:' . self::MAX_SIZE_KB,
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx',
            ],
        ], [
            'files.required' => 'حداقل یک فایل انتخاب کنید.',
            'files.max'      => 'حداکثر ۵ فایل در هر بار مجاز است.',
            'files.*.max'    => 'حجم هر فایل نباید بیشتر از ۵۰ مگابایت باشد.',
            'files.*.mimes'  => 'فرمت مجاز: JPG، PNG، WEBP، PDF، DOC، DOCX، XLS، XLSX',
        ]);

        $count = 0;

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType();
            $path = $file->storeAs(
                "attachments/{$folder}/{$model->id}",
                \Str::uuid() . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            // ✅ polymorphic — نه Attachment::create مستقیم
            $model->attachments()->create([
                'uploaded_by' => Auth::id(),
                'file_name'   => $file->getClientOriginalName(),
                'file_path'   => $path,
                'file_type' => \App\Models\Attachment::resolveFileType($mime),
                'mime_type'   => $mime,
                'file_size'   => $file->getSize(),
            ]);

            $count++;
        }
        $model->touch();
        return back()->with('success', "{$count} فایل با موفقیت آپلود شد.");
    }
    private function handleDelete(Attachment $attachment): \Illuminate\Http\RedirectResponse
    {
        // فقط آپلودر می‌تونه فایل خودش رو حذف کنه
        if ($attachment->uploaded_by !== Auth::id()) {
            return back()->with('error', 'شما اجازه حذف این فایل را ندارید.');
        }
        $parent = $attachment->attachable;
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        if ($parent) {
            $parent->touch();                       // ← اضافه شد
        }

        return back()->with('success', 'فایل حذف شد.');
    }

    // ─── Authorization ────────────────────────────────────────

    /**
     * بررسی دسترسی آپلود بر اساس نوع مدل و نقش کاربر
     *
     * Report      → technician (مال خودش) | ❌ reception | ❌ supply | ❌ ceo
     * PartOrder   → technician (مال خودش) | ❌ reception | ❌ supply | ❌ ceo
     * WorkRequest → ❌ technician          | reception   | ❌ supply  | ceo
     */
    private function authorizeUpload(string $type, Model $model): void
    {
        $user = Auth::user();

        match ($user->role) {

            'technician' => match ($type) {
                'report', 'part_order' => $model->user_id !== $user->id
                    ? abort(403, 'فقط به موارد خودتان دسترسی دارید.')
                    : null,
                'part_order_item' => $model->partOrder->user_id !== $user->id
                    ? abort(403, 'فقط به موارد خودتان دسترسی دارید.')
                    : null,
                default => abort(403, 'دسترسی مجاز نیست.'),
            },

            'reception' => match ($type) {
                'work_request' => null,
                default        => abort(403, 'دسترسی مجاز نیست.'),
            },

            'supply' => match ($type) {
                'supply_proposal' => $model->created_by !== $user->id
                    ? abort(403, 'فقط به پیشنهادهای خودتان دسترسی دارید.')
                    : null,
                default => abort(403, 'دسترسی مجاز نیست.'),
            },

            'ceo' => match ($type) {
                'work_request' => null,
                // supply_proposal عمداً نیست: مدیر فقط مشاهده/دانلود دارد
                default => abort(403, 'دسترسی مجاز نیست.'),
            },

            default => abort(403),
        };
    }

    private function ensureBelongsTo(Attachment $attachment, Model $model): void
    {
        $modelClass = get_class($model);
        if (
            $attachment->attachable_type !== $modelClass ||
            $attachment->attachable_id   !== $model->id
        ) {
            abort(403, 'این فایل متعلق به این رکورد نیست.');
        }
    }
    public function storeForPartOrderItem(Request $request, PartOrderItem $partOrderItem)
    {
        $this->authorizeUpload('part_order_item', $partOrderItem);
        return $this->handleUpload($request, $partOrderItem, 'part-order-items');
    }

    public function destroyForPartOrderItem(PartOrderItem $partOrderItem, Attachment $attachment)
    {
        $this->authorizeUpload('part_order_item', $partOrderItem);
        $this->ensureBelongsTo($attachment, $partOrderItem);
        return $this->handleDelete($attachment);
    }

    public function storeForSupplyProposal(Request $request, SupplyProposal $proposal)
    {
        $this->authorizeUpload('supply_proposal', $proposal);
        return $this->handleUpload($request, $proposal, 'supply-proposals');
    }

    public function destroyForSupplyProposal(SupplyProposal $proposal, Attachment $attachment)
    {
        $this->authorizeUpload('supply_proposal', $proposal);
        $this->ensureBelongsTo($attachment, $proposal);
        return $this->handleDelete($attachment);
    }
}
