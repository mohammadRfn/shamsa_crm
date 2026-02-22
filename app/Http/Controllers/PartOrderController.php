<?php

namespace App\Http\Controllers;

use App\Models\PartOrder;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PartOrder::query()->with(['user', 'lastActionBy']);
        $query->forRole($user->role);

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

        return view('partorders.index', compact('partOrders'));
    }

    public function create()
    {
        if (!Auth::user()->isTechnician()) {
            return redirect()->route('partorders.index')
                ->with('error', 'فقط تکنسین‌ها می‌توانند سفارش ثبت کنند.');
        }

        return view('partorders.create');
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
            'order_number' => 'required|string|max:255',
            'part_name' => 'required|string|max:255',
            'specifications' => 'required|string',
            'package' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        PartOrder::create([
            'user_id' => auth()->id(),
            ...$validated,
            'status' => 'pending',
        ]);

        return redirect()->route('partorders.index')
            ->with('success', 'سفارش قطعه با موفقیت ثبت شد.');
    }

    public function show(PartOrder $partorder)
    {
        $user = Auth::user();

        if ($user->isTechnician() && $partorder->user_id !== $user->id) {
            abort(403, 'شما اجازه دسترسی ندارید.');
        }

        $partorder->load(['user', 'approvals.user']);
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
            'part_name' => 'required|string|max:255',
            'specifications' => 'required|string',
            'package' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string',
        ]);

        $partorder->update($validated);

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
        });

        return back()->with('error', 'رد شما ثبت شد.');
    }
}
