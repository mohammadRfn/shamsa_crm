<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * نمایش لیست گزارشات
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Report::query()->with(['user', 'lastActionBy']);

        // Filter based on role
        $query->forRole($user->role);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('part_name', 'like', "%{$request->search}%")
                    ->orWhere('request_number', 'like', "%{$request->search}%")
                    ->orWhere('serial_number', 'like', "%{$request->search}%")
                    ->orWhere('device_model', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('reports.index', compact('reports'));
    }

    /**
     * فرم ایجاد گزارش جدید
     */
    public function create()
    {
        $this->authorize('create', Report::class);

        return view('reports.create');
    }

    /**
     * ذخیره گزارش جدید
     */
    public function store(Request $request)
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'part_name' => 'required|string|max:255',
            'request_date' => 'required|date',
            'request_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:50',
            'device_model' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'activity_report' => 'required|string',
            'used_parts_list' => 'nullable|string',
            'workers_count' => 'required|integer|min:1',
            'hours_per_worker' => 'required|numeric|min:0.5',
            'end_date' => 'required|date|after_or_equal:request_date',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'part_name' => $validated['part_name'],
            'request_date' => $validated['request_date'],
            'request_number' => $validated['request_number'],
            'serial_number' => $validated['serial_number'],
            'device_model' => $validated['device_model'],
            'issue_description' => $validated['issue_description'],
            'activity_report' => $validated['activity_report'],
            'used_parts_list' => $validated['used_parts_list'],
            'workers_count' => $validated['workers_count'],
            'hours_per_worker' => $validated['hours_per_worker'],
            'end_date' => $validated['end_date'],
            'status' => 'pending',
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'گزارش با موفقیت ثبت شد.');
    }

    /**
     * نمایش جزئیات گزارش
     */
    public function show(Report $report)
    {
        $user = Auth::user();

        if ($user->isTechnician() && $report->user_id !== $user->id) {
            abort(403, 'شما اجازه دسترسی به این گزارش را ندارید.');
        }

        $report->load(['user', 'approvals.user', 'comments' => function ($query) use ($user) {
            $query->active()
                ->parentOnly()
                ->forUser($user)
                ->with(['user', 'replies.user'])
                ->latest();
        }]);

        return view('reports.show', compact('report'));
    }

    /**
     * فرم ویرایش گزارش
     */
    public function edit(Report $report)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $report->user_id !== $user->id) {
            return redirect()->route('reports.index')
                ->with('error', 'شما اجازه ویرایش این گزارش را ندارید.');
        }

        if (!in_array($report->status, ['new', 'pending'])) {
            return redirect()->route('reports.index')
                ->with('error', 'این گزارش قابل ویرایش نیست.');
        }

        return view('reports.edit', compact('report'));
    }

    /**
     * آپدیت گزارش
     */
    public function update(Request $request, Report $report)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $report->user_id !== $user->id) {
            return redirect()->route('reports.index')
                ->with('error', 'شما اجازه ویرایش این گزارش را ندارید.');
        }

        if (!in_array($report->status, ['new', 'pending'])) {
            return redirect()->route('reports.index')
                ->with('error', 'این گزارش قابل ویرایش نیست.');
        }

        $validated = $request->validate([
            'part_name' => 'required|string|max:255',
            'request_date' => 'required|date',
            'serial_number' => 'required|string|max:50',
            'device_model' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'activity_report' => 'required|string',
            'used_parts_list' => 'nullable|string',
            'workers_count' => 'required|integer|min:1',
            'hours_per_worker' => 'required|numeric|min:0.5',
            'end_date' => 'required|date',
        ]);

        $report->update($validated);

        return redirect()->route('reports.show', $report)
            ->with('success', 'گزارش با موفقیت بروزرسانی شد.');
    }

    /**
     * حذف گزارش
     */
    public function destroy(Report $report)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $report->user_id !== $user->id) {
            return redirect()->route('reports.index')
                ->with('error', 'شما اجازه حذف این گزارش را ندارید.');
        }

        if (!in_array($report->status, ['new', 'pending'])) {
            return redirect()->route('reports.index')
                ->with('error', 'این گزارش قابل حذف نیست.');
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'گزارش با موفقیت حذف شد.');
    }

    /**
     * تایید گزارش
     */
    public function approve(Request $request, Report $report)
    {
        $user = Auth::user();

        if (!$user->isApprover()) {
            return back()->with('error', 'شما اجازه تایید ندارید.');
        }

        if (!$report->canBeApprovedBy($user)) {
            return back()->with('error', 'شما قبلاً نظر خود را ثبت کرده‌اید.');
        }

        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($report, $user, $request) {
            // ثبت در جدول approvals
            Approval::create([
                'approvable_type' => 'App\Models\Report',
                'approvable_id' => $report->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => 'approved',
                'comment' => $request->comment,
            ]);

            match ($user->role) {
                'reception' => $report->update(['request_approval' => 1]),
                'supply' => $report->update(['supply_approval' => 1]),
                'ceo' => $report->update(['ceo_approval' => 1]),
            };

            $report->increment('approved_by_count');
            $report->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);

            if ($report->fresh()->isFullyApproved()) {
                $report->update(['status' => 'approved']);
            }
        });

        return back()->with('success', 'رای تایید شما ثبت شد.');
    }

    /**
     * رد گزارش
     */
    public function reject(Request $request, Report $report)
    {
        $user = Auth::user();

        if (!$user->isApprover()) {
            return back()->with('error', 'شما اجازه رد کردن ندارید.');
        }

        if (!$report->canBeApprovedBy($user)) {
            return back()->with('error', 'شما قبلاً نظر خود را ثبت کرده‌اید.');
        }

        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($report, $user, $request) {
            // ثبت در جدول approvals
            Approval::create([
                'approvable_type' => 'App\Models\Report',
                'approvable_id' => $report->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => 'rejected',
                'comment' => $request->comment,
            ]);

            // آپدیت فیلد approval مربوطه
            match ($user->role) {
                'reception' => $report->update(['request_approval' => 0]),
                'supply' => $report->update(['supply_approval' => 0]),
                'ceo' => $report->update(['ceo_approval' => 0]),
            };

            // آپدیت counters
            $report->increment('rejected_by_count');
            $report->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);

            // بررسی رد کامل
            if ($report->fresh()->isFullyRejected()) {
                $report->update(['status' => 'rejected']);
            }
        });

        return back()->with('error', 'رأی رد شما ثبت شد.');
    }
}
