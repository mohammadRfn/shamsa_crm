<?php

namespace App\Http\Controllers;

use App\Models\WorkRequest;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class WorkRequestController extends Controller
{
    /**
     * نمایش لیست درخواست‌های کار
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = WorkRequest::query()->with(['user', 'lastActionBy']);
        $query->forRole($user->role);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('request_number', 'like', "%{$request->search}%")
                    ->orWhere('serial_number', 'like', "%{$request->search}%")
                    ->orWhere('device_model', 'like', "%{$request->search}%")
                    ->orWhere('equipment_name', 'like', "%{$request->search}%")
                    ->orWhere('contact_person', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Request type filter
        if ($request->request_type) {
            $query->where('request_type', $request->request_type);
        }

        // Payment status filter
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $workRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('workrequests.index', compact('workRequests'));
    }

    /**
     * فرم ایجاد درخواست کار جدید
     */
    public function create()
    {
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('workrequests.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند درخواست ثبت کنند.');
        }

        return view('workrequests.create');
    }

    /**
     * ذخیره درخواست کار جدید
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('workrequests.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند درخواست ثبت کنند.');
        }
        if ($request->has('request_date')) {
            $request->merge([
                'request_date' => toGregorian($request->request_date)
            ]);
        }
        $validated = $request->validate([
            'request_number' => 'required|string|max:255',
            'request_date' => 'required|date',
            'serial_number' => 'required|string|max:255',
            'device_model' => 'required|string|max:255',
            'equipment_name' => 'nullable|string|max:255',
            'request_unit' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'work_description' => 'required|string',
            'workflow_description' => 'nullable|string',
            'issue_description' => 'nullable|string',
            'request_type' => 'required|in:repair,service,install,sale',
            'estimated_cost' => 'nullable|numeric|min:0',
            'initial_price_result' => 'nullable|string|max:255',
            'responsible_officer' => 'nullable|string|max:255',
        ]);

        WorkRequest::create([
            'user_id' => auth()->id(),
            ...$validated,
            'status' => 'pending',
        ]);

        return redirect()->route('workrequests.index')
            ->with('success', 'درخواست کار با موفقیت ثبت شد.');
    }

    /**
     * نمایش جزئیات درخواست کار
     */
    public function show(WorkRequest $workrequest)
    {
        $user = Auth::user();

        // بررسی دسترسی
        if ($user->isTechnician() && $workrequest->user_id !== $user->id) {
            abort(403, 'شما اجازه دسترسی به این درخواست را ندارید.');
        }

        // Load relations
        $workrequest->load(['user', 'approvals.user']);

        return view('workrequests.show', compact('workrequest'));
    }

    /**
     * فرم ویرایش درخواست کار
     */
    public function edit(WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $workrequest->user_id !== $user->id) {
            return redirect()->route('workrequests.index')
                ->with('error', 'شما اجازه ویرایش این درخواست را ندارید.');
        }

        // فقط درخواست‌های pending و new قابل ویرایش هستن
        if (!in_array($workrequest->status, ['new', 'pending'])) {
            return redirect()->route('workrequests.index')
                ->with('error', 'این درخواست قابل ویرایش نیست.');
        }

        return view('workrequests.edit', compact('workrequest'));
    }

    /**
     * آپدیت درخواست کار
     */
    public function update(Request $request, WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $workrequest->user_id !== $user->id) {
            return redirect()->route('workrequests.index')
                ->with('error', 'شما اجازه ویرایش این درخواست را ندارید.');
        }

        if (!in_array($workrequest->status, ['new', 'pending'])) {
            return redirect()->route('workrequests.index')
                ->with('error', 'این درخواست قابل ویرایش نیست.');
        }
        if ($request->has('request_date')) {
            $request->merge([
                'request_date' => toGregorian($request->request_date)
            ]);
        }

        $validated = $request->validate([
            'serial_number' => 'required|string|max:255',
            'request_date' => 'required|date',
            'device_model' => 'required|string|max:255',
            'equipment_name' => 'nullable|string|max:255',
            'request_unit' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'work_description' => 'required|string',
            'workflow_description' => 'nullable|string',
            'issue_description' => 'nullable|string',
            'request_type' => 'required|in:repair,service,install,sale',
            'estimated_cost' => 'nullable|numeric|min:0',
            'initial_price_result' => 'nullable|string|max:255',
            'responsible_officer' => 'nullable|string|max:255',
        ]);

        $workrequest->update($validated);

        return redirect()->route('workrequests.show', $workrequest)
            ->with('success', 'درخواست با موفقیت بروزرسانی شد.');
    }

    /**
     * حذف درخواست کار
     */
    public function destroy(WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $workrequest->user_id !== $user->id) {
            return redirect()->route('workrequests.index')
                ->with('error', 'شما اجازه حذف این درخواست را ندارید.');
        }

        // فقط درخواست‌های new و pending قابل حذف هستن
        if (!in_array($workrequest->status, ['new', 'pending'])) {
            return redirect()->route('workrequests.index')
                ->with('error', 'این درخواست قابل حذف نیست.');
        }

        $workrequest->delete();

        return redirect()->route('workrequests.index')
            ->with('success', 'درخواست با موفقیت حذف شد.');
    }

    /**
     * تایید درخواست کار
     */
    public function approve(Request $request, WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isApprover()) {
            return back()->with('error', 'شما اجازه تایید ندارید.');
        }

        // if (!$workrequest->canBeApprovedBy($user)) {
        //     return back()->with('error', 'شما قبلاً نظر خود را ثبت کرده‌اید.');
        // }

        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($workrequest, $user, $request) {
            // ثبت در جدول approvals
            Approval::create([
                'approvable_type' => 'App\Models\WorkRequest',
                'approvable_id' => $workrequest->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => 'approved',
                'comment' => $request->comment,
            ]);

            // آپدیت فیلد approval مربوطه
            match ($user->role) {
                'reception' => $workrequest->update(['request_approval' => 1]),
                'supply' => $workrequest->update(['supply_approval' => 1]),
                'ceo' => $workrequest->update(['ceo_approval' => 1]),
            };

            // آپدیت counters
            $workrequest->increment('approved_by_count');
            $workrequest->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);

            // بررسی تایید کامل
            $workrequest->refresh();
            if ($workrequest->isFullyApproved()) {
                $workrequest->update(['status' => 'approved']);
            } elseif ($workrequest->status == 'new') {
                $workrequest->update(['status' => 'pending']);
            }
        });

        return back()->with('success', 'رای تایید شما ثبت شد.');
    }

    /**
     * رد درخواست کار
     */
    public function reject(Request $request, WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isApprover()) {
            return back()->with('error', 'شما اجازه رد کردن ندارید.');
        }

        // if (!$workrequest->canBeApprovedBy($user)) {
        //     return back()->with('error', 'شما قبلاً نظر خود را ثبت کرده‌اید.');
        // }

        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($workrequest, $user, $request) {
            // ثبت در جدول approvals
            Approval::create([
                'approvable_type' => 'App\Models\WorkRequest',
                'approvable_id' => $workrequest->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => 'rejected',
                'comment' => $request->comment,
            ]);

            // آپدیت فیلد approval مربوطه
            match ($user->role) {
                'reception' => $workrequest->update(['request_approval' => 0]),
                'supply' => $workrequest->update(['supply_approval' => 0]),
                'ceo' => $workrequest->update(['ceo_approval' => 0]),
            };

            // آپدیت counters
            $workrequest->increment('rejected_by_count');
            $workrequest->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);

            // بررسی رد کامل
            $workrequest->refresh();
            if ($workrequest->isFullyRejected()) {
                $workrequest->update(['status' => 'rejected']);
            } elseif ($workrequest->status == 'new') {
                $workrequest->update(['status' => 'pending']);
            }
        });

        return back()->with('error', 'رأی رد شما ثبت شد.');
    }

    /**
     * تکمیل اطلاعات مالی (فقط CEO)
     */
    public function updateFinancial(Request $request, WorkRequest $workrequest)
    {
        if (!Auth::user()->isCEO()) {
            return back()->with('error', 'فقط مدیر عامل می‌تواند اطلاعات مالی را تکمیل کند.');
        }

        $validated = $request->validate([
            'estimated_cost'       => 'nullable|numeric|min:0',
            'initial_price_result' => 'nullable|string|max:255',
            'final_cost' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:credit,cash,documents',
            'invoice_number' => 'nullable|string|max:255',
            'accounting_document' => 'nullable|string|max:255',
            'receipt_document' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
        ]);

        $workrequest->update($validated);

        return back()->with('success', 'اطلاعات مالی با موفقیت ثبت شد.');
    }
    public function downloadPdf(WorkRequest $workrequest)
    {
        $workrequest->load(['user', 'approvals.user']);

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

        $html = view('workrequests.pdf', compact('workrequest'))->render();
        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'workrequest-' . $workrequest->request_number . '.pdf');
    }
}
