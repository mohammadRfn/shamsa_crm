<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

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
        $query->withReadStatus($user->id);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('part_name', 'like', "%{$request->search}%")
                    ->orWhere('request_number', 'like', "%{$request->search}%")
                    ->orWhere('serial_number', 'like', "%{$request->search}%")
                    ->orWhere('device_model', 'like', "%{$request->search}%")
                    ->orWhere('issue_description', 'like', "%{$request->search}%")
                    ->orWhere('activity_report', 'like', "%{$request->search}%")
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
        $viewMode = $request->input('view', session('reports_view', 'grid'));
        if (in_array($viewMode, ['grid', 'list'])) {
            session(['reports_view' => $viewMode]);
        } else {
            $viewMode = 'grid';
        }

        return view('reports.index', compact('reports', 'viewMode'));
    }

    /**
     * فرم ایجاد گزارش جدید
     */
    public function create(Request $request)
    {
        // فقط تکنسین می‌تونه گزارش بسازه
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('reports.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند گزارش ثبت کنند.');
        }

        $taskId = $request->task_id;

        return view('reports.create', [
            'report' => new \App\Models\Report(),
            'taskId' => $taskId,
        ]);
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
            'task_id' => $request->task_id ?? null,
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
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $mime = $file->getMimeType();
                $path = $file->storeAs(
                    "attachments/reports/{$report->id}",
                    \Str::uuid() . '.' . $file->getClientOriginalExtension(),
                    'public'
                );
                $report->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'file_type'   => str_starts_with($mime, 'image/') ? 'image' : 'pdf',
                    'mime_type'   => $mime,
                    'file_size'   => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('reports.show', $report)
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
        $report->markAsReadBy();

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
        $report->touch();

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

            $report->touch();
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
            $report->touch();
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

        $html = view('reports.pdf', compact('report', 'parts'))->render();
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="report-' . $report->request_number . '.pdf"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
    /**
     * اکسپورت گزارشات انتخاب‌شده به فایل اکسل
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ids'   => 'required|array|min:1|max:200',
            'ids.*' => 'integer|exists:reports,id',
        ]);

        $reports = Report::query()
            ->with(['user'])
            ->forRole($user->role)
            ->whereIn('id', $request->ids)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'گزارشی برای اکسپورت یافت نشد.');
        }

        // ── ستون‌ها بر اساس blade ──────────────────────────────────────
        $columns = [
            'A' => ['ردیف',                    5,  'text'],
            'B' => ['شماره درخواست',           16, 'text'],
            'C' => ['تاریخ درخواست',           14, 'text'],
            'D' => ['تاریخ پایان',             14, 'text'],
            'E' => ['نام قطعه',                20, 'text'],
            'F' => ['شماره سریال',             16, 'text'],
            'G' => ['مدل دستگاه',              16, 'text'],
            'H' => ['تکنسین',                  16, 'text'],
            'I' => ['تعداد نفر',               10, 'text'],
            'J' => ['ساعت کار هر نفر',         14, 'text'],
            'K' => ['شرح مشکل',                35, 'wrap'],
            'L' => ['گزارش فعالیت',            35, 'wrap'],
            'M' => ['قطعات مصرفی',             25, 'wrap'],
            'N' => ['وضعیت',                   14, 'colored'],
            'O' => ['تایید پذیرش',             14, 'colored'],
            'P' => ['تایید تامین',             14, 'colored'],
            'Q' => ['تایید مدیر عامل',         16, 'colored'],
        ];

        $lastCol = array_key_last($columns);

        // ── رنگ‌ها ────────────────────────────────────────────────────
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
        $approvalMap = [
            '1'    => ['fill' => 'D1FAE5', 'font' => '065F46', 'label' => '✓ تایید شده'],
            '0'    => ['fill' => 'FEE2E2', 'font' => '991B1B', 'label' => '✕ رد شده'],
            'null' => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => '⏱ در انتظار'],
        ];

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('گزارش کار');
        $ws->setRightToLeft(true);

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

        // ── ردیف ۱: بنر ────────────────────────────────────────────────
        $ws->mergeCells("A1:{$lastCol}1");
        $ws->setCellValue('A1', 'گزارش کار تعمیرات');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $ROSE]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws->getRowDimension(1)->setRowHeight(38);

        // ── ردیف ۲: تاریخ خروجی ────────────────────────────────────────
        $ws->mergeCells("A2:{$lastCol}2");
        $ws->setCellValue('A2', 'تاریخ خروجی: ' . now()->format('Y-m-d H:i') . '  |  تعداد رکورد: ' . $reports->count());
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => $STONE_500], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $STONE_100]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws->getRowDimension(2)->setRowHeight(20);

        // ── ردیف ۳: هدر ────────────────────────────────────────────────
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

        // ── ردیف‌های داده ───────────────────────────────────────────────
        $thinBorder  = ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E5E7EB']]];
        $baseFont    = ['name' => 'Arial', 'size' => 9, 'color' => ['rgb' => $STONE_700]];
        $centerAlign = ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true];
        $rightAlign  = ['horizontal' => 'right',  'vertical' => 'center', 'wrapText' => true, 'indent' => 1];
        $approvalKey = fn($v) => is_null($v) ? 'null' : ($v ? '1' : '0');

        foreach ($reports as $idx => $rp) {
            $row   = $idx + 4;
            $rowBg = ($idx % 2 === 0) ? 'FFFFFF' : 'FAFAF9';

            $statusInfo = $statusMap[$rp->status ?? 'new'] ?? ['fill' => 'F5F5F4', 'font' => $STONE_700, 'label' => '---'];
            $recInfo    = $approvalMap[$approvalKey($rp->request_approval)];
            $supInfo    = $approvalMap[$approvalKey($rp->supply_approval)];
            $ceoInfo    = $approvalMap[$approvalKey($rp->ceo_approval)];

            // قطعات مصرفی از JSON
            $usedParts = '---';
            if ($rp->used_parts_list) {
                $partsArr  = is_string($rp->used_parts_list)
                    ? json_decode($rp->used_parts_list, true)
                    : (array) $rp->used_parts_list;
                $usedParts = implode('، ', array_filter($partsArr ?? [])) ?: '---';
            }

            $values = [
                'A' => $idx + 1,
                'B' => $rp->request_number,
                'C' => $rp->request_date_jalali ?? '---',
                'D' => $rp->end_date ? toJalali($rp->end_date) : '---',
                'E' => $rp->part_name,
                'F' => $rp->serial_number,
                'G' => $rp->device_model,
                'H' => $rp->user->name ?? '---',
                'I' => $rp->workers_count,
                'J' => $rp->hours_per_worker,
                'K' => $rp->issue_description    ?: '---',
                'L' => $rp->activity_report       ?: '---',
                'M' => $usedParts,
                'N' => $statusInfo['label'],
                'O' => $recInfo['label'],
                'P' => $supInfo['label'],
                'Q' => $ceoInfo['label'],
            ];

            foreach ($values as $col => $val) {
                $cell = $ws->getCell("{$col}{$row}");
                $cell->setValue($val);
                $colType = $columns[$col][2];
                $align   = ($colType === 'wrap') ? $rightAlign : $centerAlign;
                $cell->getStyle()->applyFromArray([
                    'font'      => $baseFont,
                    'alignment' => $align,
                    'borders'   => $thinBorder,
                    'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $rowBg]],
                ]);
            }

            $colorCell($ws->getCell("N{$row}"), $statusInfo['fill'], $statusInfo['font']);
            $colorCell($ws->getCell("O{$row}"), $recInfo['fill'],    $recInfo['font']);
            $colorCell($ws->getCell("P{$row}"), $supInfo['fill'],    $supInfo['font']);
            $colorCell($ws->getCell("Q{$row}"), $ceoInfo['fill'],    $ceoInfo['font']);

            $ws->getRowDimension($row)->setRowHeight(28);
        }

        $ws->freezePane('A4');

        $fileName = 'reports-' . now()->format('Ymd-His') . '.xlsx';
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
