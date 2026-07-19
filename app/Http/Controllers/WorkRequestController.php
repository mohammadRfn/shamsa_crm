<?php

namespace App\Http\Controllers;


use App\Models\WorkRequest;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

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
        $query->withReadStatus($user->id); 


        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('request_number', 'like', "%{$request->search}%")
                    ->orWhere('serial_number', 'like', "%{$request->search}%")
                    ->orWhere('device_model', 'like', "%{$request->search}%")
                    ->orWhere('equipment_name', 'like', "%{$request->search}%")
                    ->orWhere('contact_person', 'like', "%{$request->search}%")
                    ->orWhere('work_description', 'like', "%{$request->search}%")       
                    ->orWhere('workflow_description', 'like', "%{$request->search}%")
                    ->orWhere('issue_description', 'like', "%{$request->search}%")
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

        $workRequests = $query->orderBy('created_at', 'desc')->paginate(8);

        $viewMode = $request->input('view', session('workrequests_view', 'grid'));
        if (in_array($viewMode, ['grid', 'list'])) {
            session(['workrequests_view' => $viewMode]);
        } else {
            $viewMode = 'grid';
        }

        return view('workrequests.index', compact('workRequests', 'viewMode'));
    }

    /**
     * فرم ایجاد درخواست کار جدید
     */
    public function create()
    {
        if (!Auth::user()->isReception() && !Auth::user()->isCEO()) {
            return redirect()->route('workrequests.index')
                ->with('error', 'فقط تکنسین‌ها و مدیران می‌توانند درخواست ثبت کنند.');
        }

        $previousContacts = WorkRequest::select('request_unit', 'contact_person', 'contact_phone')
            ->whereNotNull('request_unit')
            ->latest()
            ->get()
            ->unique('request_unit')
            ->values();

        return view('workrequests.create', [
            'workrequest' => new \App\Models\WorkRequest(),
            'previousContacts' => $previousContacts,
        ]);
    }

    /**
     * ذخیره درخواست کار جدید
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isReception() && !Auth::user()->isCEO()) {
            return redirect()->route('workrequests.index')
                ->with('error', 'فقط تکنسین‌ها و مدیران می‌توانند درخواست ثبت کنند.');
        }
        if ($request->has('request_date')) {
            $request->merge([
                'request_date' => toGregorian($request->request_date)
            ]);
        }
        if ($request->filled('bank_payment_date')) {
            $request->merge([
                'bank_payment_date' => toGregorian($request->bank_payment_date)
            ]);
        }
        $validated = $request->validate([
            'request_number' => 'required|string|max:255|unique:work_requests,request_number',
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
            'final_cost' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:credit,cash,documents',
            'invoice_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_payment_date' => 'nullable|date',
            'bank_payment_amount' => 'nullable|numeric|min:0',
            'accounting_document' => 'nullable|string|max:255',
            'receipt_document' => 'nullable|string|max:255',
        ]);

        $workrequest = WorkRequest::create([
            'user_id' => auth()->id(),
            ...$validated,
            'status' => 'pending',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $mime = $file->getMimeType();
                $path = $file->storeAs(
                    "attachments/work-requests/{$workrequest->id}",
                    \Str::uuid() . '.' . $file->getClientOriginalExtension(),
                    'public'
                );
                $workrequest->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'file_type'   => \App\Models\Attachment::resolveFileType($mime),
                    'mime_type'   => $mime,
                    'file_size'   => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('workrequests.show', $workrequest)
            ->with('success', 'درخواست کار با موفقیت ثبت شد.');
    }

    /**
     * نمایش جزئیات درخواست کار
     */
    public function show(WorkRequest $workrequest)
    {

        // Load relations
        $workrequest->load(['user', 'approvals.user', 'stages.actionedBy']);
        $workrequest->markAsReadBy(); 

        return view('workrequests.show', compact('workrequest'));
    }

    /**
     * فرم ویرایش درخواست کار
     */
    public function edit(WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isReception()  && !$user->isCEO()) {
            return redirect()->route('workrequests.index')
                ->with('error', 'شما اجازه ویرایش این درخواست را ندارید.');
        }

        // فقط درخواست‌های pending و new قابل ویرایش هستن
        if (!in_array($workrequest->status, ['new', 'pending'])) {
            return redirect()->route('workrequests.index')
                ->with('error', 'این درخواست قابل ویرایش نیست.');
        }

        $previousContacts = WorkRequest::select('request_unit', 'contact_person', 'contact_phone')
            ->whereNotNull('request_unit')
            ->latest()
            ->get()
            ->unique('request_unit')
            ->values();

        return view('workrequests.edit', compact('workrequest', 'previousContacts'));
    }

    /**
     * آپدیت درخواست کار
     */
    public function update(Request $request, WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isReception()  && !$user->isCEO()) {
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
        if ($request->filled('bank_payment_date')) {
            $request->merge([
                'bank_payment_date' => toGregorian($request->bank_payment_date)
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
            'final_cost' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:credit,cash,documents',
            'invoice_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_payment_date' => 'nullable|date',
            'bank_payment_amount' => 'nullable|numeric|min:0',
            'accounting_document' => 'nullable|string|max:255',
            'receipt_document' => 'nullable|string|max:255',
        ]);
        $workrequest->touch(); 
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

        if (!$user->isReception()  && !$user->isCEO()) {
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

        if (!$user->isReception() && !$user->isCEO()) {
            return back()->with('error', 'شما اجازه تایید ندارید.');
        }


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
            $workrequest->touch();
        });

        return back()->with('success', 'رای تایید شما ثبت شد.');
    }

    /**
     * رد درخواست کار
     */
    public function reject(Request $request, WorkRequest $workrequest)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
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
            $workrequest->touch();
        });

        return back()->with('error', 'رأی رد شما ثبت شد.');
    }

    /**
     * تکمیل اطلاعات مالی (فقط CEO)
     */
    public function updateFinancial(Request $request, WorkRequest $workrequest)
    {
        if (!Auth::user()->isCEO() && !Auth::user()->isReception()) {
            return back()->with('error', 'فقط مدیر عامل می‌تواند اطلاعات مالی را تکمیل کند.');
        }
        if ($request->has('bank_payment_date')) {
            $request->merge([
                'bank_payment_date' => toGregorian($request->bank_payment_date)
            ]);
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
            'bank_payment_date'    => 'nullable|date',    // ← اضافه شد
            'bank_payment_amount'  => 'nullable|numeric|min:0',
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
            'fontDir' => [resource_path('fonts/Vazirmatn')],
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

        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="workrequest-' . $workrequest->request_number . '.pdf"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
    public function updateStage(Request $request, WorkRequest $workrequest, string $stage)
    {
        $user = Auth::user();

        if (!$user->isReception() && !$user->isCEO()) {
            return back()->with('error', 'دسترسی ندارید.');
        }

        $validStages = ['reception', 'workshop', 'estimation', 'approval', 'delivery', 'financial'];
        if (!in_array($stage, $validStages)) {
            return back()->with('error', 'مرحله نامعتبر است.');
        }

        $request->validate([
            'status' => 'required|in:pending,done,rejected',
            'note'   => 'nullable|string|max:500',
        ]);

        $workrequest->stages()->updateOrCreate(
            ['stage' => $stage],
            [
                'status'      => $request->status,
                'note'        => $request->note,
                'actioned_at' => now(),
                'actioned_by' => $user->id,
            ]
        );

        return back()->with('success', 'وضعیت مرحله بروز شد.');
    }
    /**
     * اکسپورت درخواست‌های انتخاب‌شده به فایل اکسل
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ids'   => 'required|array|min:1|max:200',
            'ids.*' => 'integer|exists:work_requests,id',
        ]);

        $workRequests = WorkRequest::query()
            ->with(['user'])
            ->forRole($user->role)
            ->whereIn('id', $request->ids)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($workRequests->isEmpty()) {
            return back()->with('error', 'درخواستی برای اکسپورت یافت نشد.');
        }

        // ── ستون‌ها دقیقاً بر اساس عناوین blade ──────────────────────────
        // هر آیتم: [عنوان فارسی، عرض ستون، نوع: 'text'|'colored'|'number']
        $columns = [
            'A'  => ['ردیف',                        5,  'text'],
            'B'  => ['شماره درخواست',               16, 'text'],
            'C'  => ['تاریخ درخواست / ورود',        16, 'text'],
            'D'  => ['مدل',                          16, 'text'],
            'E'  => ['شماره سریال دستگاه',           18, 'text'],
            'F'  => ['واحد درخواست کننده',           20, 'text'],
            'G'  => ['مسئول پیگیری درخواست',        20, 'text'],
            'H'  => ['شماره تماس',                   14, 'text'],
            'I'  => ['نوع درخواست',                  14, 'colored'],
            'J'  => ['وضعیت',                        14, 'colored'],
            'K'  => ['ثبت‌کننده',                    16, 'text'],
            'L'  => ['شرح کار درخواستی',             30, 'wrap'],
            'M'  => ['شرح ایراد اعلامی',             30, 'wrap'],
            'N'  => ['شرح گردش کار',                 30, 'wrap'],
            'O'  => ['هزینه برآورد شده اولیه (ریال)', 22, 'number'],
            'P'  => ['نتیجه اعلام قیمت اولیه',       22, 'text'],
            'Q'  => ['هزینه نهایی (ریال)',            18, 'number'],
            'R'  => ['وضعیت پرداخت',                 16, 'colored'],
            'S'  => ['شماره فاکتور',                  16, 'text'],
            'T'  => ['نام بانک',                      16, 'text'],
            'U'  => ['تاریخ پرداخت بانک',             18, 'text'],
            'V'  => ['مبلغ پرداخت بانک (ریال)',       20, 'number'],
            'W'  => ['سند حسابداری',                  16, 'text'],
            'X'  => ['سند دریافت',                    16, 'text'],
            'Y'  => ['تایید پذیرش',                   16, 'colored'],
            'Z'  => ['تایید مدیر عامل',               18, 'colored'],
        ];

        $lastCol  = array_key_last($columns); // 'Z'
        $colRange = "A1:{$lastCol}1";

        // ── رنگ‌ها ────────────────────────────────────────────────────────
        $ROSE      = 'E8476A';
        $STONE_700 = '44403C';
        $STONE_100 = 'F5F5F4';
        $STONE_500 = '78716C';
        $WHITE     = 'FFFFFF';

        $statusMap = [
            'approved' => ['fill' => 'D1FAE5', 'font' => '065F46', 'label' => '✓ تایید شده'],
            'rejected' => ['fill' => 'FEE2E2', 'font' => '991B1B', 'label' => '✕ رد شده'],
            'pending'  => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => '⏱ در انتظار'],
            'new'      => ['fill' => 'DBEAFE', 'font' => '1E3A8A', 'label' => '★ جدید'],
        ];
        $typeMap = [
            'repair'  => ['fill' => 'FEE2E2', 'font' => '991B1B', 'label' => '🔧 تعمیرات'],
            'service' => ['fill' => 'DBEAFE', 'font' => '1E3A8A', 'label' => '⚙️ سرویس و نصب'],
            'install' => ['fill' => 'D1FAE5', 'font' => '065F46', 'label' => '🔌 ساخت'],
            'sale'    => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => '💰 فروش'],
        ];
        $paymentMap = [
            'credit'    => ['fill' => 'DBEAFE', 'font' => '1E3A8A', 'label' => 'اعتباری'],
            'cash'      => ['fill' => 'D1FAE5', 'font' => '065F46', 'label' => 'نقدی'],
            'documents' => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => 'اسنادی'],
        ];
        $approvalMap = [
            '1'    => ['fill' => 'D1FAE5', 'font' => '065F46', 'label' => '✓ تایید شده'],
            '0'    => ['fill' => 'FEE2E2', 'font' => '991B1B', 'label' => '✕ رد شده'],
            'null' => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => '⏱ در انتظار'],
        ];

        // ── ساخت Spreadsheet ───────────────────────────────────────────────
        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('گردش کار');
        $ws->setRightToLeft(true);

        // Helper: رنگ‌دهی سلول
        $colorCell = function (
            \PhpOffice\PhpSpreadsheet\Cell\Cell $cell,
            string $bg,
            string $fg
        ) {
            $cell->getStyle()->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $bg]],
                'font' => ['color' => ['rgb' => $fg], 'bold' => true],
            ]);
        };

        // ── ردیف ۱: بنر عنوان ─────────────────────────────────────────────
        $ws->mergeCells("A1:{$lastCol}1");
        $ws->setCellValue('A1', 'گزارش درخواست‌های کار');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $ROSE]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws->getRowDimension(1)->setRowHeight(38);

        // ── ردیف ۲: تاریخ خروجی ───────────────────────────────────────────
        $ws->mergeCells("A2:{$lastCol}2");
        $ws->setCellValue('A2', 'تاریخ خروجی: ' . now()->format('Y-m-d H:i') . '  |  تعداد رکورد: ' . $workRequests->count());
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => $STONE_500], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $STONE_100]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws->getRowDimension(2)->setRowHeight(20);

        // ── ردیف ۳: هدر ستون‌ها ───────────────────────────────────────────
        foreach ($columns as $col => [$label, $width]) {
            $ws->setCellValue("{$col}3", $label);
            $ws->getColumnDimension($col)->setWidth($width);
            $ws->getStyle("{$col}3")->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $WHITE], 'name' => 'Arial'],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $STONE_700]],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                'borders'   => ['bottom' => ['borderStyle' => 'medium', 'color' => ['rgb' => $ROSE]]],
            ]);
        }
        $ws->getRowDimension(3)->setRowHeight(34);

        // ── ردیف‌های داده ─────────────────────────────────────────────────
        $thinBorder = ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E5E7EB']]];
        $baseFont   = ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => $STONE_700]];
        $centerAlign = ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true];
        $rightAlign  = ['horizontal' => 'right',  'vertical' => 'center', 'wrapText' => true, 'indent' => 1];

        $approvalKey = fn($v) => is_null($v) ? 'null' : ($v ? '1' : '0');

        foreach ($workRequests as $idx => $wr) {
            $row   = $idx + 4;
            $rowBg = ($idx % 2 === 0) ? 'FFFFFF' : 'FAFAF9';

            $statusInfo  = $statusMap[$wr->status ?? 'new']     ?? ['fill' => 'F5F5F4', 'font' => $STONE_700, 'label' => '---'];
            $typeInfo    = $typeMap[$wr->request_type ?? '']     ?? ['fill' => 'F5F5F4', 'font' => $STONE_700, 'label' => '---'];
            $payInfo     = $paymentMap[$wr->payment_status ?? ''] ?? null;
            $recInfo     = $approvalMap[$approvalKey($wr->request_approval)];
            $ceoInfo     = $approvalMap[$approvalKey($wr->ceo_approval)];

            // مقادیر سلول‌ها به ترتیب ستون
            $values = [
                'A' => $idx + 1,
                'B' => $wr->request_number,
                'C' => $wr->request_date_jalali ?? '---',
                'D' => $wr->device_model,
                'E' => $wr->serial_number,
                'F' => $wr->request_unit,
                'G' => $wr->contact_person,
                'H' => $wr->contact_phone,
                'I' => $typeInfo['label'],
                'J' => $statusInfo['label'],
                'K' => $wr->user->name ?? '---',
                'L' => $wr->work_description  ?: '---',
                'M' => $wr->issue_description  ?: '---',
                'N' => $wr->workflow_description ?: '---',
                'O' => $wr->estimated_cost    ? (float) $wr->estimated_cost    : '---',
                'P' => $wr->initial_price_result ?: '---',
                'Q' => $wr->final_cost        ? (float) $wr->final_cost        : '---',
                'R' => $payInfo ? $payInfo['label'] : '---',
                'S' => $wr->invoice_number    ?: '---',
                'T' => $wr->bank_name         ?: '---',
                'U' => $wr->bank_payment_date ? toJalali($wr->bank_payment_date) : '---',
                'V' => $wr->bank_payment_amount ? (float) $wr->bank_payment_amount : '---',
                'W' => $wr->accounting_document ?: '---',
                'X' => $wr->receipt_document  ?: '---',
                'Y' => $recInfo['label'],
                'Z' => $ceoInfo['label'],
            ];

            // ست کردن مقادیر و استایل پایه
            foreach ($values as $col => $val) {
                $cell = $ws->getCell("{$col}{$row}");
                $cell->setValue($val);

                $colType = $columns[$col][2];
                $align   = in_array($colType, ['wrap']) ? $rightAlign : $centerAlign;

                $cell->getStyle()->applyFromArray([
                    'font'      => $baseFont,
                    'alignment' => $align,
                    'borders'   => $thinBorder,
                    'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $rowBg]],
                ]);

                // فرمت عدد برای ستون‌های مالی
                if ($colType === 'number' && is_float($val)) {
                    $ws->getStyle("{$col}{$row}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }
            }

            // رنگ‌دهی ستون‌های colored
            $colorCell($ws->getCell("I{$row}"), $typeInfo['fill'],   $typeInfo['font']);
            $colorCell($ws->getCell("J{$row}"), $statusInfo['fill'], $statusInfo['font']);
            if ($payInfo) {
                $colorCell($ws->getCell("R{$row}"), $payInfo['fill'], $payInfo['font']);
            }
            $colorCell($ws->getCell("Y{$row}"), $recInfo['fill'], $recInfo['font']);
            $colorCell($ws->getCell("Z{$row}"), $ceoInfo['fill'], $ceoInfo['font']);

            // ارتفاع ردیف — برای ستون‌های wrap بیشتر
            $ws->getRowDimension($row)->setRowHeight(28);
        }

        // ── Freeze پس از هدر ──────────────────────────────────────────────
        $ws->freezePane('A4');

        // ── Stream فایل ───────────────────────────────────────────────────
        $fileName = 'work-requests-' . now()->format('Ymd-His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($wb);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
        ]);
    }
}
