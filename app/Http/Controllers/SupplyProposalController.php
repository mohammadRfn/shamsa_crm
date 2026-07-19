<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\PartOrder;
use App\Models\SupplyProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplyProposalController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = SupplyProposal::with(['partOrder', 'creator']);

        // CEO همه رو میبینه، supply فقط خودش
        if ($user->isSupply()) {
            $query->where('created_by', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('supplier_name', 'like', "%{$request->search}%")
                    ->orWhere('part_name', 'like', "%{$request->search}%")
                    ->orWhereHas('partOrder', function ($q) use ($request) {
                        $q->where('order_number', 'like', "%{$request->search}%")
                            ->orWhere('equipment_name', 'like', "%{$request->search}%");
                    });
            });
        }

        $proposals = $query->orderBy('created_at', 'desc')->paginate(8);
        $statuses  = SupplyProposal::STATUSES;

        return view('supply.proposals.index', compact('proposals', 'statuses'));
    }

    // ── Create ────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        if (!Auth::user()->isSupply()) {
            return redirect()->route('supply-proposals.index')
                ->with('error', 'فقط تامین‌کننده می‌تواند پیشنهاد ثبت کند.');
        }

        // اگه از روی یه PartOrder خاص اومده باشه
        $selectedPartOrder = null;
        if ($request->filled('part_order_id')) {
            $selectedPartOrder = PartOrder::findOrFail($request->part_order_id);
        }

        $partOrders = PartOrder::orderBy('created_at', 'desc')->get();

        return view('supply.proposals.create', compact('partOrders', 'selectedPartOrder'));
    }

    // ── Store ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        if (!Auth::user()->isSupply()) {
            return redirect()->route('supply-proposals.index')
                ->with('error', 'فقط تامین‌کننده می‌تواند پیشنهاد ثبت کند.');
        }

        $validated = $request->validate([
            'part_order_id'       => 'required|integer|exists:part_orders,id',
            'part_name'           => 'required|string|max:255',
            'supplier_name'       => 'required|string|max:255',
            'unit_price'          => 'required|numeric|min:0',
            'quantity'            => 'required|integer|min:1',
            'estimated_delivery'  => 'nullable|string',
            'note'                => 'nullable|string',
            'attachments'         => 'nullable|array|max:5',
            'attachments.*'       => 'file|max:51200|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx',
        ]);

        if (!empty($validated['estimated_delivery'])) {
            $validated['estimated_delivery'] = toGregorian($validated['estimated_delivery']);
        }

        $proposal = new SupplyProposal();
        $proposal->part_order_id      = $validated['part_order_id'];
        $proposal->part_name          = $validated['part_name'];
        $proposal->supplier_name      = $validated['supplier_name'];
        $proposal->unit_price         = $validated['unit_price'];
        $proposal->quantity           = $validated['quantity'];
        $proposal->estimated_delivery = $validated['estimated_delivery'] ?? null;
        $proposal->note               = $validated['note'] ?? null;
        $proposal->status             = 'pending';
        $proposal->created_by         = Auth::id();
        $proposal->save();

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $mime = $file->getMimeType();
                $path = $file->storeAs(
                    "attachments/supply-proposals/{$proposal->id}",
                    Str::uuid() . '.' . $file->getClientOriginalExtension(),
                    'public'
                );

                $proposal->attachments()->create([
                    'uploaded_by' => Auth::id(),
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $path,
                    'file_type'   => Attachment::resolveFileType($mime),
                    'mime_type'   => $mime,
                    'file_size'   => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('supply-proposals.index')
            ->with('success', 'پیشنهاد با موفقیت ثبت شد.');
    }

    // ── Show ──────────────────────────────────────────────────────────────
    public function show(SupplyProposal $proposal)
    {
        $user = Auth::user();

        if ($user->isSupply() && $proposal->created_by !== $user->id) {
            abort(403, 'شما اجازه دسترسی ندارید.');
        }

        $proposal->load(['partOrder.user', 'creator']);

        // سایر پیشنهادهای همین PartOrder (برای نمایش در show)
        $relatedProposals = SupplyProposal::with('creator')
            ->where('part_order_id', $proposal->part_order_id)
            ->where('id', '!=', $proposal->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $statuses = SupplyProposal::STATUSES;

        return view('supply.proposals.show', compact('proposal', 'relatedProposals', 'statuses'));
    }

    // ── Edit ──────────────────────────────────────────────────────────────
    public function edit(SupplyProposal $proposal)
    {
        $user = Auth::user();

        if (!$user->isCEO()) {
            if ((int) $proposal->created_by !== (int) $user->id) {
                return redirect()->route('supply-proposals.index')
                    ->with('error', 'شما اجازه ویرایش ندارید.');
            }

            if ($proposal->status !== 'pending') {
                return redirect()->route('supply-proposals.index')
                    ->with('error', 'فقط پیشنهادهای در انتظار قابل ویرایش هستند.');
            }
        }

        $partOrders = PartOrder::orderBy('created_at', 'desc')->get();

        return view('supply.proposals.edit', compact('proposal', 'partOrders'));
    }

    // ── Update ────────────────────────────────────────────────────────────
    public function update(Request $request, SupplyProposal $proposal)
    {
        $user = Auth::user();

        if (!$user->isCEO()) {
            if ((int) $proposal->created_by !== (int) $user->id) {
                return redirect()->route('supply-proposals.index')
                    ->with('error', 'شما اجازه ویرایش ندارید.');
            }

            if ($proposal->status !== 'pending') {
                return redirect()->route('supply-proposals.index')
                    ->with('error', 'فقط پیشنهادهای در انتظار قابل ویرایش هستند.');
            }
        }

        $validated = $request->validate([
            'part_order_id'       => 'required|integer|exists:part_orders,id',
            'part_name'           => 'required|string|max:255',
            'supplier_name'       => 'required|string|max:255',
            'unit_price'          => 'required|numeric|min:0',
            'quantity'            => 'required|integer|min:1',
            'estimated_delivery'  => 'nullable|string',
            'note'                => 'nullable|string',
        ]);

        if (!empty($validated['estimated_delivery'])) {
            $validated['estimated_delivery'] = toGregorian($validated['estimated_delivery']);
        } else {
            $validated['estimated_delivery'] = null;
        }

        $proposal->update($validated);

        return redirect()->route('supply-proposals.show', $proposal)
            ->with('success', 'پیشنهاد با موفقیت بروزرسانی شد.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────
    public function destroy(SupplyProposal $proposal)
    {
        $user = Auth::user();

        if (!$user->isCEO() && (int) $proposal->created_by !== (int) $user->id) {
            return redirect()->route('supply-proposals.index')
                ->with('error', 'شما اجازه حذف ندارید.');
        }

        $proposal->delete();

        return redirect()->route('supply-proposals.index')
            ->with('success', 'پیشنهاد با موفقیت حذف شد.');
    }

    // ── Change Status ─────────────────────────────────────────────────────
    public function changeStatus(Request $request, SupplyProposal $proposal)
    {
        $user = Auth::user();

        $request->validate([
            'status'   => 'required|in:pending,approved,rejected,ordered,delivered',
            'ceo_note' => 'nullable|string|max:1000',
        ]);

        $newStatus = $request->status;

        // supply فقط ordered و delivered میتونه ست کنه
        if ($user->isSupply()) {
            if ((int) $proposal->created_by !== (int) $user->id) {
                return back()->with('error', 'شما اجازه تغییر وضعیت ندارید.');
            }
            if (!in_array($newStatus, SupplyProposal::SUPPLY_STATUSES)) {
                return back()->with('error', 'شما مجاز به این تغییر وضعیت نیستید.');
            }
        }

        // CEO همه وضعیت‌ها رو میتونه ست کنه
        if (!$user->isCEO() && !$user->isSupply()) {
            return back()->with('error', 'شما اجازه تغییر وضعیت ندارید.');
        }

        $updateData = ['status' => $newStatus];

        if ($user->isCEO()) {
            $updateData['ceo_note'] = $request->ceo_note;
            if ($newStatus === 'approved') {
                $updateData['selected_at'] = now();
            }
        }

        $proposal->update($updateData);

        return back()->with('success', 'وضعیت با موفقیت تغییر کرد.');
    }

    // ── API: قطعات یه PartOrder (برای dropdown) ───────────────────────────
    public function getPartNames(PartOrder $partOrder)
    {
        return response()->json([
            'parts' => $partOrder->part_name ?? [],
        ]);
    }
}
