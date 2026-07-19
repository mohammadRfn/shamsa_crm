<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\WorkRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * پنل تسک‌ساز — فقط reception و ceo
     * لیست تمام تسک‌هایی که این کاربر ساخته
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            abort(403);
        }

        $query = Task::with(['workRequest', 'assignedTo', 'createdBy'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks       = $query->paginate(8);
        $technicians = User::where('role', 'technician')->get();

        return view('tasks.index', compact('tasks', 'technicians'));
    }

    /**
     * فرم ساخت تسک — انتخاب شماره درخواست + تعمیرکار
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            abort(403);
        }

        // اگه از صفحه ورک ریکوست اومده، از قبل پر میشه
        $workRequest = null;
        if ($request->work_request_id) {
            $workRequest = WorkRequest::findOrFail($request->work_request_id);
        }

        $technicians  = User::where('role', 'technician')->orderBy('name')->get();
        $workRequests = WorkRequest::with('user')
            ->select(
                'id',
                'request_number',
                'equipment_name',
                'device_model',
                'serial_number',
                'request_date',
                'request_type',
                'work_description',
                'issue_description',
                'workflow_description',
                'user_id'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.create', compact('workRequest', 'technicians', 'workRequests'));
    }

    /**
     * ذخیره تسک جدید
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            abort(403);
        }

        $validated = $request->validate([
            'work_request_id' => 'required|exists:work_requests,id',
            'assigned_to'     => 'required|exists:users,id',
            'note'            => 'nullable|string|max:1000',
        ]);

        // بررسی اینکه کاربر انتخاب‌شده واقعاً تعمیرکار باشه
        $technician = User::findOrFail($validated['assigned_to']);
        if (!$technician->isTechnician()) {
            return back()->withErrors(['assigned_to' => 'کاربر انتخاب‌شده تعمیرکار نیست.']);
        }

        Task::create([
            'work_request_id' => $validated['work_request_id'],
            'assigned_to'     => $validated['assigned_to'],
            'created_by'      => $user->id,
            'note'            => $validated['note'],
            'status'          => 'pending',
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'تسک با موفقیت ارسال شد.');
    }

    /**
     * نمایش جزئیات تسک (برای reception/ceo)
     */
    public function show(Task $task)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            abort(403);
        }

        $task->load(['workRequest', 'assignedTo', 'createdBy', 'reports.user', 'partOrders.user']);

        return view('tasks.show', compact('task'));
    }
    public function destroy(Task $task)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            abort(403);
        }

        // فقط تسک‌های pending قابل حذف باشن
        if ($task->status !== 'pending') {
            return back()->with('error', 'فقط تسک‌های در انتظار قابل حذف هستند.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'تسک با موفقیت حذف شد.');
    }
    /**
     * آپدیت وضعیت تسک — فقط reception/ceo
     */
    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'وضعیت تسک بروز شد.');
    }

    // ══════════════════════════════════════════════════════════════
    // پنل تعمیرکار
    // ══════════════════════════════════════════════════════════════

    /**
     * لیست تسک‌های تعمیرکار — فقط technician
     */
    public function myTasks(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTechnician()) {
            abort(403);
        }

        $query = Task::with(['workRequest', 'createdBy', 'reports', 'partOrders'])
            ->where('assigned_to', $user->id)
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query->paginate(8);

        // تعداد تسک‌های ندیده‌شده برای نشان دادن badge
        $unseenCount = Task::where('assigned_to', $user->id)
            ->whereNull('seen_at')
            ->count();

        return view('tasks.my-tasks', compact('tasks', 'unseenCount'));
    }

    /**
     * نمایش جزئیات تسک برای تعمیرکار + علامت‌گذاری به‌عنوان دیده‌شده
     */
    public function showMyTask(Task $task)
    {
        $user = Auth::user();

        if (!$user->isTechnician()) {
            abort(403);
        }

        // فقط تسک خودش رو ببینه
        if ($task->assigned_to !== $user->id) {
            abort(403);
        }

        // علامت‌گذاری به‌عنوان دیده‌شده
        $task->markAsSeen();

        $task->load(['workRequest', 'createdBy', 'reports.user', 'partOrders.user']);

        return view('tasks.my-task-show', compact('task'));
    }
}
