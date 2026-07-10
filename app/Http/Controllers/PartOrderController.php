<?php

namespace App\Http\Controllers;

use App\Models\PartOrder;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class PartOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PartOrder::query()->with(['user', 'lastActionBy']);
        $query->forRole($user->role);
        $query->withReadStatus($user->id);


        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('part_name', 'like', "%{$request->search}%")
                    ->orWhere('order_number', 'like', "%{$request->search}%")
                    ->orWhere('equipment_name', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        $partOrders = $query->orderBy('created_at', 'desc')->paginate(10);

        $viewMode = $request->input('view', session('partorders_view', 'grid'));
        if (in_array($viewMode, ['grid', 'list'])) {
            session(['partorders_view' => $viewMode]);
        } else {
            $viewMode = 'grid';
        }

        return view('partorders.index', compact('partOrders', 'viewMode'));
    }

    public function create(Request $request)
    {
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('partorders.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند سفارش ثبت کنند.');
        }

        $taskId = $request->task_id;

        return view('partorders.create', [
            'partorder' => new \App\Models\PartOrder(),
            'taskId' => $taskId,
        ]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('partorders.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند سفارش ثبت کنند.');
        }
        if ($request->has('order_date')) {
            $request->merge(['order_date' => toGregorian($request->order_date)]);
        }


        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'order_number' => 'required|string|max:255|unique:part_orders,order_number',
            'part_name' => 'required|array|min:1',
            'part_name.*' => 'required|string|max:255',
            'specifications' => 'required|array|min:1',
            'specifications.*' => 'required|string',
            'package' => 'required|array|min:1',
            'package.*' => 'required|string|max:255',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string',
        ]);

        $partorder = PartOrder::create([
            'user_id' => auth()->id(),
            'task_id' => $request->task_id ?? null,
            'equipment_name' => $validated['equipment_name'],
            'order_date' => $validated['order_date'],
            'order_number' => $validated['order_number'],
            'part_name' => $validated['part_name'],
            'specifications' => $validated['specifications'],
            'package' => $validated['package'],
            'quantity' => $validated['quantity'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);
        $rowKeys = $request->input('row_key', []);

        foreach ($validated['part_name'] as $i => $name) {
            $newItem = $partorder->items()->create([
                'part_name'      => $name,
                'specifications' => $validated['specifications'][$i] ?? null,
                'package'        => $validated['package'][$i] ?? null,
                'quantity'       => $validated['quantity'][$i] ?? 1,
                'description'    => $validated['description'][$i] ?? null,
            ]);

            $rowKey = $rowKeys[$i] ?? null;
            if ($rowKey !== null && $request->hasFile("item_files.$rowKey")) {
                foreach ($request->file("item_files.$rowKey") as $file) {
                    $mime = $file->getMimeType();
                    $path = $file->storeAs(
                        "attachments/part-order-items/{$newItem->id}",
                        \Str::uuid() . '.' . $file->getClientOriginalExtension(),
                        'public'
                    );
                    $newItem->attachments()->create([
                        'uploaded_by' => auth()->id(),
                        'file_name'   => $file->getClientOriginalName(),
                        'file_path'   => $path,
                        'file_type'   => \App\Models\Attachment::resolveFileType($mime),
                        'mime_type'   => $mime,
                        'file_size'   => $file->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('partorders.show', $partorder)
            ->with('success', 'سفارش قطعه با موفقیت ثبت شد.');
    }

    public function show(PartOrder $partorder)
    {
        $user = Auth::user();

        if ($user->isTechnician() && $partorder->user_id !== $user->id) {
            abort(403, 'شما اجازه دسترسی ندارید.');
        }

        $partorder->load(['user', 'approvals.user', 'items.attachments']);
        $partorder->markAsReadBy();
        return view('partorders.show', compact('partorder'));
    }

    public function edit(PartOrder $partorder)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $partorder->user_id !== $user->id) {
            return redirect()->route('partorders.index')
                ->with('error', 'شما اجازه ویرایش ندارید.');
        }

        if (!in_array($partorder->status, ['new', 'pending'])) {
            return redirect()->route('partorders.index')
                ->with('error', 'این سفارش قابل ویرایش نیست.');
        }
        $partorder->load('items');
        return view('partorders.edit', compact('partorder'));
    }

    public function update(Request $request, PartOrder $partorder)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $partorder->user_id !== $user->id) {
            return redirect()->route('partorders.index')
                ->with('error', 'شما اجازه ویرایش ندارید.');
        }
        if ($request->has('order_date')) {
            $request->merge(['order_date' => toGregorian($request->order_date)]);
        }

        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'part_name' => 'required|array|min:1',
            'part_name.*' => 'required|string|max:255',
            'specifications' => 'required|array|min:1',
            'specifications.*' => 'required|string',
            'package' => 'required|array|min:1',
            'package.*' => 'required|string|max:255',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'description' => 'required|array|min:1',
            'description.*' => 'required|string',
            'item_id'   => 'nullable|array',
            'item_id.*' => 'nullable|integer|exists:part_order_items,id',
        ]);

        $partorder->touch();
        $partorder->update($validated);

        $submittedIds = [];

        foreach ($validated['part_name'] as $i => $name) {
            $itemData = [
                'part_name'      => $name,
                'specifications' => $validated['specifications'][$i] ?? null,
                'package'        => $validated['package'][$i] ?? null,
                'quantity'       => $validated['quantity'][$i] ?? 1,
                'description'    => $validated['description'][$i] ?? null,
            ];

            $itemId = $validated['item_id'][$i] ?? null;

            if ($itemId && $partorder->items()->where('id', $itemId)->exists()) {
                $partorder->items()->where('id', $itemId)->update($itemData);
                $submittedIds[] = $itemId;
            } else {
                $newItem = $partorder->items()->create($itemData);
                $submittedIds[] = $newItem->id;
            }
        }

        $partorder->items()->whereNotIn('id', $submittedIds)->get()->each->delete();

        return redirect()->route('partorders.show', $partorder)
            ->with('success', 'سفارش با موفقیت بروزرسانی شد.');
    }
    public function destroy(PartOrder $partorder)
    {
        $user = Auth::user();

        if (!$user->isTechnician() || $partorder->user_id !== $user->id) {
            return redirect()->route('partorders.index')
                ->with('error', 'شما اجازه حذف ندارید.');
        }

        $partorder->delete();

        return redirect()->route('partorders.index')
            ->with('success', 'سفارش با موفقیت حذف شد.');
    }

    public function approve(Request $request, PartOrder $partorder)
    {
        $user = Auth::user();

        if (!$user->isApprover()) {
            return back()->with('error', 'شما اجازه تایید ندارید.');
        }

        // if (!$partorder->canBeApprovedBy($user)) {
        //     return back()->with('error', 'شما قبلاً نظر داده‌اید.');
        // }

        DB::transaction(function () use ($partorder, $user, $request) {
            Approval::create([
                'approvable_type' => 'App\Models\PartOrder',
                'approvable_id' => $partorder->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => 'approved',
                'comment' => $request->comment,
            ]);

            match ($user->role) {
                'reception' => $partorder->update(['reception_approval' => 1]),
                'supply' => $partorder->update(['supply_approval' => 1]),
                'ceo' => $partorder->update(['ceo_approval' => 1]),
            };

            $partorder->increment('approved_by_count');
            $partorder->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);

            $partorder->refresh();
            if ($partorder->isFullyApproved()) {
                $partorder->update(['status' => 'approved']);
            } elseif ($partorder->status == 'new') {
                $partorder->update(['status' => 'pending']);
            }
            $partorder->touch();
        });

        return back()->with('success', 'تایید شما ثبت شد.');
    }

    public function reject(Request $request, PartOrder $partorder)
    {
        $user = Auth::user();

        if (!$user->isApprover()) {
            return back()->with('error', 'شما اجازه رد ندارید.');
        }

        DB::transaction(function () use ($partorder, $user, $request) {
            Approval::create([
                'approvable_type' => 'App\Models\PartOrder',
                'approvable_id' => $partorder->id,
                'user_id' => $user->id,
                'role' => $user->role,
                'action' => 'rejected',
                'comment' => $request->comment,
            ]);

            match ($user->role) {
                'reception' => $partorder->update(['reception_approval' => 0]),
                'supply' => $partorder->update(['supply_approval' => 0]),
                'ceo' => $partorder->update(['ceo_approval' => 0]),
            };

            $partorder->increment('rejected_by_count');
            $partorder->update([
                'last_action_at' => now(),
                'last_action_by' => $user->id,
            ]);
            $partorder->refresh();
            if ($partorder->isFullyRejected()) {
                $partorder->update(['status' => 'rejected']);
            } elseif ($partorder->status == 'new') {
                $partorder->update(['status' => 'pending']);
            }
            $partorder->touch();
        });

        return back()->with('error', 'رد شما ثبت شد.');
    }
    public function downloadPdf(PartOrder $partorder)
    {
        $partorder->load(['user', 'approvals.user']);

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

        $html = view('partorders.pdf', compact('partorder'))->render();
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="partorder-' . $partorder->order_number . '.pdf"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
    /**
     * اکسپورت سفارشات انتخاب‌شده به فایل اکسل
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ids'   => 'required|array|min:1|max:200',
            'ids.*' => 'integer|exists:part_orders,id',
        ]);

        $partOrders = PartOrder::query()
            ->with(['user'])
            ->forRole($user->role)
            ->whereIn('id', $request->ids)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($partOrders->isEmpty()) {
            return back()->with('error', 'سفارشی برای اکسپورت یافت نشد.');
        }

        // ── ستون‌ها بر اساس blade ──────────────────────────────────────
        $columns = [
            'A' => ['ردیف',                    5,  'text'],
            'B' => ['شماره سفارش',             16, 'text'],
            'C' => ['تاریخ سفارش',             14, 'text'],
            'D' => ['نام تجهیز',               20, 'text'],
            'E' => ['تکنسین',                  16, 'text'],
            'F' => ['نام قطعه',                25, 'wrap'],
            'G' => ['مشخصات فنی',              30, 'wrap'],
            'H' => ['بسته‌بندی',               20, 'wrap'],
            'I' => ['تعداد',                   10, 'text'],
            'J' => ['توضیحات',                 30, 'wrap'],
            'K' => ['وضعیت',                   14, 'colored'],
            'L' => ['تایید پذیرش',             14, 'colored'],
            'M' => ['تایید تامین',             14, 'colored'],
            'N' => ['تایید مدیر عامل',         16, 'colored'],
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
            'failed'   => ['fill' => 'FEE2E2', 'font' => '991B1B', 'label' => '✕ رد شده'],
            'pending'  => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => '⏱ در انتظار'],
            'sent'     => ['fill' => 'DBEAFE', 'font' => '1E3A8A', 'label' => '📦 ارسال شده'],
            'new'      => ['fill' => 'DBEAFE', 'font' => '1E3A8A', 'label' => '★ جدید'],
        ];
        $approvalMap = [
            '1'    => ['fill' => 'D1FAE5', 'font' => '065F46', 'label' => '✓ تایید شده'],
            '0'    => ['fill' => 'FEE2E2', 'font' => '991B1B', 'label' => '✕ رد شده'],
            'null' => ['fill' => 'FEF9C3', 'font' => '854D0E', 'label' => '⏱ در انتظار'],
        ];

        $wb = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $wb->getActiveSheet();
        $ws->setTitle('سفارش قطعه');
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
        $ws->setCellValue('A1', 'گزارش سفارشات قطعه');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $WHITE], 'name' => 'Arial'],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $ROSE]],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $ws->getRowDimension(1)->setRowHeight(38);

        // ── ردیف ۲: تاریخ خروجی ────────────────────────────────────────
        $ws->mergeCells("A2:{$lastCol}2");
        $ws->setCellValue('A2', 'تاریخ خروجی: ' . now()->format('Y-m-d H:i') . '  |  تعداد رکورد: ' . $partOrders->count());
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

        foreach ($partOrders as $idx => $po) {
            $row   = $idx + 4;
            $rowBg = ($idx % 2 === 0) ? 'FFFFFF' : 'FAFAF9';

            $statusInfo = $statusMap[$po->status ?? 'new'] ?? ['fill' => 'F5F5F4', 'font' => $STONE_700, 'label' => '---'];
            $recInfo    = $approvalMap[$approvalKey($po->reception_approval)];
            $supInfo    = $approvalMap[$approvalKey($po->supply_approval)];
            $ceoInfo    = $approvalMap[$approvalKey($po->ceo_approval)];

            // آرایه‌های JSON
            $partNames  = implode('، ', $po->part_name      ?? []);
            $specs      = implode("\n", $po->specifications  ?? []);
            $packages   = implode('، ', $po->package         ?? []);
            $quantities = implode('، ', array_map('strval', $po->quantity ?? []));
            $descs      = implode("\n", $po->description     ?? []);

            $values = [
                'A' => $idx + 1,
                'B' => $po->order_number,
                'C' => $po->order_date_jalali ?? '---',
                'D' => $po->equipment_name,
                'E' => $po->user->name ?? '---',
                'F' => $partNames  ?: '---',
                'G' => $specs      ?: '---',
                'H' => $packages   ?: '---',
                'I' => $quantities ?: '---',
                'J' => $descs      ?: '---',
                'K' => $statusInfo['label'],
                'L' => $recInfo['label'],
                'M' => $supInfo['label'],
                'N' => $ceoInfo['label'],
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

            $colorCell($ws->getCell("K{$row}"), $statusInfo['fill'], $statusInfo['font']);
            $colorCell($ws->getCell("L{$row}"), $recInfo['fill'],    $recInfo['font']);
            $colorCell($ws->getCell("M{$row}"), $supInfo['fill'],    $supInfo['font']);
            $colorCell($ws->getCell("N{$row}"), $ceoInfo['fill'],    $ceoInfo['font']);

            $ws->getRowDimension($row)->setRowHeight(28);
        }

        $ws->freezePane('A4');

        $fileName = 'part-orders-' . now()->format('Ymd-His') . '.xlsx';
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
