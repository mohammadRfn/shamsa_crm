<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

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
        // فقط تکنسین می‌تونه گزارش بسازه
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('reports.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند گزارش ثبت کنند.');
        }

        return view('reports.create');
    }

    /**
     * ذخیره گزارش جدید
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('reports.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند گزارش ثبت کنند.');
        }
        if ($request->has('request_date')) {
            $request->merge(['request_date' => toGregorian($request->request_date)]);
        }
        if ($request->has('end_date')) {
            $request->merge(['end_date' => toGregorian($request->end_date)]);
        }

        $validated = $request->validate([
            'part_name' => 'required|string|max:255',
            'request_date' => 'required|date',
            'request_number' => 'required|string|max:255',
            'serial_number' => 'required|string|max:50',
            'device_model' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'activity_report' => 'required|string',
            'used_parts_list' => 'nullable|array',
            'workers_count' => 'required|integer|min:1',
            'hours_per_worker' => 'required|numeric|min:0.5',
            'end_date' => 'required|date|after_or_equal:request_date',
        ]);

        // تبدیل آرایه به JSON
        $usedPartsList = !empty($validated['used_parts_list'])
            ? json_encode(array_filter($validated['used_parts_list']))
            : null;

        $report = Report::create([
            'user_id' => auth()->id(),
            'part_name' => $validated['part_name'],
            'request_date' => $validated['request_date'],
            'request_number' => $validated['request_number'],
            'serial_number' => $validated['serial_number'],
            'device_model' => $validated['device_model'],
            'issue_description' => $validated['issue_description'],
            'activity_report' => $validated['activity_report'],
            'used_parts_list' => $usedPartsList,
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

        // بررسی دسترسی
        if ($user->isTechnician() && $report->user_id !== $user->id) {
            abort(403, 'شما اجازه دسترسی به این گزارش را ندارید.');
        }

        // Load relations - جایگزین کن
        $report->load(['user', 'approvals.user']);

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

        // فقط گزارشات pending و new قابل ویرایش هستن
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
        if ($request->has('request_date')) {
            $request->merge(['request_date' => toGregorian($request->request_date)]);
        }
        if ($request->has('end_date')) {
            $request->merge(['end_date' => toGregorian($request->end_date)]);
        }

        $validated = $request->validate([
            'part_name' => 'required|string|max:255',
            'request_date' => 'required|date',
            'serial_number' => 'required|string|max:50',
            'device_model' => 'required|string|max:50',
            'issue_description' => 'required|string',
            'activity_report' => 'required|string',
            'used_parts_list' => 'nullable|array',
            'workers_count' => 'required|integer|min:1',
            'hours_per_worker' => 'required|numeric|min:0.5',
            'end_date' => 'required|date',
        ]);

        // تبدیل آرایه به JSON
        $usedPartsList = !empty($validated['used_parts_list'])
            ? json_encode(array_filter($validated['used_parts_list']))
            : null;

        $report->update([
            'part_name' => $validated['part_name'],
            'request_date' => $validated['request_date'],
            'serial_number' => $validated['serial_number'],
            'device_model' => $validated['device_model'],
            'issue_description' => $validated['issue_description'],
            'activity_report' => $validated['activity_report'],
            'used_parts_list' => $usedPartsList,
            'workers_count' => $validated['workers_count'],
            'hours_per_worker' => $validated['hours_per_worker'],
            'end_date' => $validated['end_date'],
        ]);

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

        // فقط گزارشات new و pending قابل حذف هستن
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

        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($report, $user, $request) {
            // ابتدا approval قبلی این کاربر رو پیدا کن
            $existingApproval = Approval::where('approvable_type', 'App\Models\Report') // یا PartOrder یا WorkRequest
                ->where('approvable_id', $report->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingApproval) {
                // اگه قبلاً رأی داده، آپدیت کن
                $existingApproval->update([
                    'action' => 'approved', // یا 'rejected'
                    'comment' => $request->comment,
                ]);
            } else {
                // اگه رأی نداده، ایجاد کن
                Approval::create([
                    'approvable_type' => 'App\Models\Report',
                    'approvable_id' => $report->id,
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'action' => 'approved', // یا 'rejected'
                    'comment' => $request->comment,
                ]);
            }

            // آپدیت فیلد approval مربوطه
            match ($user->role) {
                'reception' => $report->update(['request_approval' => 1]),
                'supply' => $report->update(['supply_approval' => 1]),
                'ceo' => $report->update(['ceo_approval' => 1]),
            };

            // آپدیت counters
            $report->increment('approved_by_count');
            $report->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);

            // بررسی تایید کامل
            $report->refresh();
            if ($report->isFullyApproved()) {
                $report->update(['status' => 'approved']);
            } elseif ($report->status == 'new') {
                $report->update(['status' => 'pending']);
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


        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($report, $user, $request) {
            // ابتدا approval قبلی این کاربر رو پیدا کن
            $existingApproval = Approval::where('approvable_type', 'App\Models\Report') // یا PartOrder یا WorkRequest
                ->where('approvable_id', $report->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingApproval) {
                // اگه قبلاً رأی داده، آپدیت کن
                $existingApproval->update([
                    'action' => 'rejected', // یا 'rejected'
                    'comment' => $request->comment,
                ]);
            } else {
                // اگه رأی نداده، ایجاد کن
                Approval::create([
                    'approvable_type' => 'App\Models\Report',
                    'approvable_id' => $report->id,
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'action' => 'rejected', // یا 'rejected'
                    'comment' => $request->comment,
                ]);
            }

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
            $report->refresh();
            if ($report->isFullyRejected()) {
                $report->update(['status' => 'rejected']);
            } elseif ($report->status == 'new') {
                $report->update(['status' => 'pending']);
            }
        });

        return back()->with('error', 'رأی رد شما ثبت شد.');
    }
    public function downloadPdf(Report $report)
    {
        $report->load(['user', 'approvals.user']);
        $parts = json_decode($report->used_parts_list) ?? [];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => 'rtl',
            'tempDir' => storage_path('app/mpdf'),
            'fontDir' => [resource_path('fonts')],
            'fontdata' => [
                'vazir' => [
                    'R' => 'Vazir-Regular.ttf',
                    'B' => 'Vazir-Bold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'default_font' => 'vazir',
        ]);

        $html = view('reports.pdf', compact('report', 'parts'))->render();
        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'report-' . $report->request_number . '.pdf');
    }
}
