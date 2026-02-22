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
        $validated = $request->validate([
            'reportable_type' => 'required|string|in:App\Models\Report,App\Models\PartOrder,App\Models\WorkRequest',
            'reportable_id' => 'required|integer',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'comment' => 'required|string|max:1000',
        ]);

        $user = auth()->user();

        // بررسی دسترسی به موجودیت اصلی
        $reportable = $this->getReportable($validated['reportable_type'], $validated['reportable_id']);

        if (!$reportable) {
            return back()->with('error', 'موردی یافت نشد.');
        }

        // اگه تکنسین باشه، فقط روی گزارش‌های خودش کامنت بذاره
        if ($user->isTechnician() && $reportable->user_id !== $user->id) {
            return back()->with('error', 'شما اجازه کامنت روی این مورد را ندارید.');
        }

        // بررسی parent comment (اگه وجود داره)
        if (isset($validated['parent_id']) && $validated['parent_id']) {
            $parentComment = Comment::find($validated['parent_id']);
            if (
                !$parentComment ||
                $parentComment->reportable_id != $validated['reportable_id'] ||
                $parentComment->reportable_type != $validated['reportable_type']
            ) {
                return back()->with('error', 'کامنت والد نامعتبر است.');
            }
        }

        // تعیین visibility
        $isVisibleToTechnician = $user->isApprover();

        Comment::create([
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => $user->id,
            'role' => $user->role,
            'comment' => $validated['comment'],
            'is_visible_to_technician' => $isVisibleToTechnician,
            'status' => 'active',
        ]);

        return back()->with('success', 'نظر شما ثبت شد.');
    }

    /**
     * حذف نرم کامنت
     */
    public function destroy(Comment $comment)
    {
        $user = auth()->user();

        // فقط صاحب کامنت یا CEO می‌تونه حذف کنه
        if ($comment->user_id !== $user->id && !$user->isCEO()) {
            return back()->with('error', 'شما اجازه حذف این نظر را ندارید.');
        }

        $comment->softDelete();

        return back()->with('success', 'نظر حذف شد.');
    }

    /**
     * دریافت موجودیت قابل کامنت
     */
    private function getReportable($type, $id)
    {
        if (!class_exists($type)) {
            return null;
        }

        return $type::find($id);
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
}
