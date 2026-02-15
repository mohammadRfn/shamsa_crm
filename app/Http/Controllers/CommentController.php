<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Report;
use App\Models\PartOrder;
use App\Models\WorkRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * نمایش لیست کامنت‌های یک آیتم (گزارش/سفارش/درخواست)
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        if (!$type || !$id) {
            return redirect()->back()->with('error', 'اطلاعات ناقص است.');
        }

        $reportable = $this->getReportable($type, $id);

        if (!$reportable) {
            return redirect()->back()->with('error', 'آیتم یافت نشد.');
        }

        // بررسی دسترسی
        $user = Auth::user();

        if ($user->isTechnician() && $reportable->user_id !== $user->id) {
            return redirect()->back()->with('error', 'شما اجازه دسترسی ندارید.');
        }

        // گرفتن کامنت‌ها
        $comments = $reportable->comments()
            ->active()
            ->parentOnly()
            ->forUser($user)
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return view('comments.index', compact('comments', 'reportable', 'type'));
    }


    /**
     * ذخیره کامنت جدید
     */
    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'reportable_type' => 'required|in:report,partorder,workrequest',
            'reportable_id' => 'required|integer',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $user = Auth::user();
        $reportable = $this->getReportable($request->reportable_type, $request->reportable_id);

        if (!$reportable) {
            return redirect()->back()->with('error', 'آیتم یافت نشد.');
        }

        // بررسی دسترسی
        if ($user->isTechnician() && $reportable->user_id !== $user->id) {
            return redirect()->back()->with('error', 'شما اجازه ثبت کامنت ندارید.');
        }

        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);

            if (!$parentComment || !$parentComment->canBeRepliedBy($user)) {
                return redirect()->back()->with('error', 'شما نمی‌توانید به این کامنت پاسخ دهید.');
            }
        }

        $isVisibleToTechnician = false;

        if ($user->isApprover()) {
            $isVisibleToTechnician = true;
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'reportable_id' => $reportable->id,
            'reportable_type' => $this->getReportableClass($request->reportable_type),
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
            'role' => $user->role,
            'is_visible_to_technician' => $isVisibleToTechnician,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'کامنت با موفقیت ثبت شد.');
    }

    // Helper methods
    private function getReportable(string $type, int $id)
    {
        return match ($type) {
            'report' => Report::find($id),
            'partorder' => PartOrder::find($id),
            'workrequest' => WorkRequest::find($id),
            default => null,
        };
    }

    private function getReportableClass(string $type): string
    {
        return match ($type) {
            'report' => 'App\Models\Report',
            'partorder' => 'App\Models\PartOrder',
            'workrequest' => 'App\Models\WorkRequest',
            default => '',
        };
    }

    /**
     * نمایش کامنت‌های یک آیتم خاص
     */
    public function show(Request $request, $type, $id)
    {
        $reportable = $this->getReportable($type, $id);

        if (!$reportable) {
            return redirect()->back()->with('error', 'آیتم یافت نشد.');
        }

        $user = Auth::user();

        // بررسی دسترسی
        if ($user->isTechnician() && $reportable->user_id !== $user->id) {
            return redirect()->back()->with('error', 'شما اجازه دسترسی ندارید.');
        }

        $comments = $reportable->comments()
            ->active()
            ->parentOnly()
            ->forUser($user)
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return view('comments.show', compact('comments', 'reportable', 'type'));
    }


    /**
     * حذف کامنت (soft delete)
     */
    public function destroy(Comment $comment)
    {
        $user = Auth::user();

        // فقط صاحب کامنت یا CEO می‌تونه حذف کنه
        if ($comment->user_id !== $user->id && !$user->isCEO()) {
            return redirect()->back()->with('error', 'شما اجازه حذف این کامنت را ندارید.');
        }

        $comment->softDelete();

        return redirect()->back()->with('success', 'کامنت با موفقیت حذف شد.');
    }
}
